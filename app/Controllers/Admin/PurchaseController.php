<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PurchaseModel;
use App\Models\PurchaseItemModel;
use App\Repositories\VendorRepository;
use App\Repositories\CompanyRepository;
use App\Services\ProductService;
use App\Services\StockService;
use App\Services\VendorService;
use Config\Database;

/**
 * Purchases — vendor bills. The missing half of the vendor money trail:
 * receiving a purchase increases vendor payable and stocks items in;
 * payments (here or on the vendor page) decrease payable.
 */
class PurchaseController extends BaseController
{
    private PurchaseModel     $model;
    private PurchaseItemModel $items;
    private VendorRepository  $vendors;
    private CompanyRepository $companies;
    private ProductService    $products;

    public function __construct()
    {
        $this->model     = new PurchaseModel();
        $this->items     = new PurchaseItemModel();
        $this->vendors   = new VendorRepository();
        $this->companies = new CompanyRepository();
        $this->products  = new ProductService();
    }

    public function index()
    {
        $page    = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage = 15;
        $q       = $this->request->getGet('q');
        $status  = $this->request->getGet('status');
        $vendor  = $this->request->getGet('vendor_un_id');

        $builder = $this->model->where('deleted_at', null);
        if ($q)      $builder->like('purchase_no', $q);
        if ($status) $builder->where('status', $status);
        if ($vendor) $builder->where('vendor_un_id', $vendor);

        $total     = $builder->countAllResults(false);
        $purchases = $builder->orderBy('purchase_date', 'DESC')->orderBy('id', 'DESC')
                             ->paginate($perPage, 'default', $page);

        $db = Database::connect();
        foreach ($purchases as &$p) {
            $v = $db->table('vendors')->select('vendor_name')->where('un_id', $p['vendor_un_id'])->get()->getRowArray();
            $p['vendor_name'] = $v['vendor_name'] ?? '-';
            $p['item_count']  = $this->items->where('purchase_un_id', $p['un_id'])->where('deleted_at', null)->countAllResults();
        }
        unset($p);

        $totalsRow = $db->table('purchases')
            ->selectSum('total_amount', 'total')
            ->selectSum('due_amount', 'due')
            ->where('deleted_at', null)->get()->getRowArray();

        return view('admin/purchases/index', [
            'title'      => 'Purchases',
            'purchases'  => $purchases,
            'vendors'    => $this->vendors->search([], 1, 200)['items'],
            'filters'    => ['q' => $q, 'status' => $status, 'vendor_un_id' => $vendor],
            'totals'     => [
                'total_amount' => (float) ($totalsRow['total'] ?? 0),
                'total_due'    => (float) ($totalsRow['due'] ?? 0),
                'count'        => $total,
            ],
            'pagination' => [
                'page'      => $page,
                'per_page'  => $perPage,
                'total'     => $total,
                'last_page' => max(1, (int) ceil($total / $perPage)),
            ],
        ]);
    }

    public function create()
    {
        return view('admin/purchases/form', [
            'title'            => 'New Purchase',
            'purchase'         => null,
            'purchase_items'   => [],
            'vendors'          => $this->vendors->search([], 1, 200)['items'],
            'companies'        => $this->companies->search([], 1, 100)['items'],
            'products'         => $this->products->forSelect(),
            'banks'            => (new \App\Services\BankAccountService())->list([], 1, 100)['items'] ?? [],
            'action'           => site_url('admin/purchases'),
            'preselect_vendor' => $this->request->getGet('vendor_un_id'),
        ]);
    }

    public function store()
    {
        if (! $this->validate([
            'vendor_un_id'  => 'required',
            'purchase_date' => 'required',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $post  = $this->request->getPost();
        $items = $this->parseItems($post);
        if (empty($items)) {
            return redirect()->back()->withInput()->with('error', 'Add at least one purchased item.');
        }

        $subtotal = array_sum(array_column($items, 'total'));
        $discount = (float) ($post['discount'] ?? 0);
        $total    = round(max(0, $subtotal - $discount), 2);
        $paid     = max(0, min((float) ($post['paid_amount'] ?? 0), $total));
        $due      = max(0, $total - $paid);
        $status   = ($post['status'] ?? 'received') === 'draft' ? 'draft' : 'received';

        $purchase = [
            'purchase_no'      => $this->nextPurchaseNo(),
            'vendor_un_id'     => $post['vendor_un_id'],
            'company_un_id'    => $post['company_un_id'] ?? null,
            'purchase_date'    => $post['purchase_date'],
            'subtotal'         => $subtotal,
            'discount'         => $discount,
            'total_amount'     => $total,
            'paid_amount'      => $paid,
            'due_amount'       => $due,
            'status'           => $status,
            'notes'            => $post['notes'] ?? null,
            'created_by_un_id' => session('user_un_id'),
        ];

        $id = $this->model->insert($purchase, true);
        if ($id === false) {
            return redirect()->back()->withInput()->with('error', 'Failed to create purchase.');
        }
        $row = $this->model->find($id);

        foreach ($items as $item) {
            $item['purchase_un_id'] = $row['un_id'];
            $this->items->insert($item);
        }

        if ($status === 'received') {
            $this->applyReceive($row, $items, $paid, $post['bank_account_un_id'] ?? null, $post);
        }

        return redirect()->to('admin/purchases/' . $row['un_id'])->with('success', 'Purchase ' . $row['purchase_no'] . ' created.');
    }

    public function show(string $unId)
    {
        $purchase = $this->model->where('un_id', $unId)->where('deleted_at', null)->first();
        if (! $purchase) return redirect()->to('admin/purchases')->with('error', 'Purchase not found.');

        $vendor  = $this->vendors->findByUnId($purchase['vendor_un_id']);
        $company = $purchase['company_un_id'] ? $this->companies->findByUnId($purchase['company_un_id']) : null;
        $items   = $this->items->where('purchase_un_id', $unId)->where('deleted_at', null)->orderBy('id', 'ASC')->findAll();

        return view('admin/purchases/show', [
            'title'          => $purchase['purchase_no'],
            'purchase'       => $purchase,
            'vendor'         => $vendor,
            'company'        => $company,
            'purchase_items' => $items,
        ]);
    }

    /**
     * Receive a draft purchase: stock-in + vendor payable increase.
     */
    public function receive(string $unId)
    {
        $purchase = $this->model->where('un_id', $unId)->where('deleted_at', null)->first();
        if (! $purchase) return redirect()->to('admin/purchases')->with('error', 'Purchase not found.');
        if ($purchase['status'] === 'received') {
            return redirect()->to('admin/purchases/' . $unId)->with('error', 'Purchase already received.');
        }

        $items = $this->items->where('purchase_un_id', $unId)->where('deleted_at', null)->findAll();
        $this->model->update($purchase['id'], ['status' => 'received']);
        $this->applyReceive($purchase, $items, 0, null, []);

        return redirect()->to('admin/purchases/' . $unId)->with('success', 'Purchase received — stock and vendor payable updated.');
    }

    public function delete(string $unId)
    {
        $purchase = $this->model->where('un_id', $unId)->where('deleted_at', null)->first();
        if (! $purchase) return redirect()->to('admin/purchases')->with('error', 'Purchase not found.');

        // Reverse effects of a received purchase before deleting
        if ($purchase['status'] === 'received') {
            $items = $this->items->where('purchase_un_id', $unId)->where('deleted_at', null)->findAll();
            $stock = new StockService();
            foreach ($items as $item) {
                if (! $item['product_un_id']) continue;
                $stock->deductForProduct(
                    $item['product_un_id'],
                    (float) $item['quantity'],
                    $purchase['purchase_no'],
                    'Purchase deleted: ' . $purchase['purchase_no']
                );
            }
            $vendor = $this->vendors->findByUnId($purchase['vendor_un_id']);
            if ($vendor) {
                $newPayable = max(0, (float) $vendor['current_payable'] - (float) $purchase['due_amount']);
                $this->vendors->updateByUnId($purchase['vendor_un_id'], ['current_payable' => $newPayable]);
            }
        }

        $this->model->where('un_id', $unId)->set(['deleted_at' => date('Y-m-d H:i:s')])->update();
        return redirect()->to('admin/purchases')->with('success', 'Purchase deleted.');
    }

    /**
     * Effects of receiving: stock-in each item (auto-creates stock items),
     * vendor payable += total, optional initial payment via VendorService.
     */
    private function applyReceive(array $purchase, array $items, float $paid, ?string $bankUnId, array $post): void
    {
        try {
            $stock = new StockService();
            foreach ($items as $item) {
                if (empty($item['product_un_id'])) continue;
                $stock->addForProduct(
                    $item['product_un_id'],
                    (float) $item['quantity'],
                    $purchase['purchase_no'],
                    (float) $item['unit_cost'],
                    'Purchase: ' . $purchase['purchase_no']
                );
            }

            // Owe the vendor the full bill
            $vendor = $this->vendors->findByUnId($purchase['vendor_un_id']);
            if ($vendor) {
                $newPayable = (float) $vendor['current_payable'] + (float) $purchase['total_amount'];
                $this->vendors->updateByUnId($purchase['vendor_un_id'], ['current_payable' => $newPayable]);
            }

            // Initial payment reduces payable + hits bank if selected
            if ($paid > 0) {
                (new VendorService())->addPayment($purchase['vendor_un_id'], [
                    'amount'              => $paid,
                    'payment_date'        => $purchase['purchase_date'],
                    'payment_method'      => $post['payment_method'] ?? 'cash',
                    'reference_no'        => $purchase['purchase_no'],
                    'notes'               => 'Payment with purchase ' . $purchase['purchase_no'],
                    'bank_account_un_id'  => $bankUnId,
                ]);
            }
        } catch (\Throwable $e) {
            log_message('error', 'Purchase receive failed for ' . $purchase['purchase_no'] . ': ' . $e->getMessage());
        }
    }

    private function parseItems(array $post): array
    {
        $names  = $post['item_product_name'] ?? [];
        $unIds  = $post['item_product_un_id'] ?? [];
        $units  = $post['item_unit'] ?? [];
        $qtys   = $post['item_quantity'] ?? [];
        $costs  = $post['item_unit_cost'] ?? [];

        $items = [];
        foreach ($names as $i => $name) {
            $name = trim($name);
            if ($name === '') continue;
            $qty  = (float) ($qtys[$i] ?? 0);
            $cost = (float) ($costs[$i] ?? 0);
            if ($qty <= 0) continue;

            $items[] = [
                'product_un_id' => ($unIds[$i] ?? '') !== '' ? $unIds[$i] : null,
                'product_name'  => $name,
                'unit'          => $units[$i] ?? 'pcs',
                'quantity'      => $qty,
                'unit_cost'     => $cost,
                'total'         => round($qty * $cost, 2),
            ];
        }
        return $items;
    }

    private function nextPurchaseNo(): string
    {
        $db   = Database::connect();
        $row  = $db->table('purchases')->select('purchase_no')->orderBy('id', 'DESC')->limit(1)->get()->getRowArray();
        $year = date('Y');
        if ($row && preg_match('/PUR-' . $year . '-(\d+)/', $row['purchase_no'], $m)) {
            $next = (int) $m[1] + 1;
        } else {
            $next = 1001;
        }
        return sprintf('PUR-%s-%05d', $year, $next);
    }
}
