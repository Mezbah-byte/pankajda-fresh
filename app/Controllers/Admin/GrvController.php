<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\GrvModel;
use App\Models\GrvItemModel;
use App\Repositories\CustomerRepository;
use App\Repositories\CompanyRepository;
use App\Services\ProductService;
use App\Services\StockService;
use Config\Database;

class GrvController extends BaseController
{
    private GrvModel        $model;
    private GrvItemModel    $items;
    private CustomerRepository $customers;
    private CompanyRepository  $companies;
    private ProductService     $products;

    public function __construct()
    {
        $this->model     = new GrvModel();
        $this->items     = new GrvItemModel();
        $this->customers = new CustomerRepository();
        $this->companies = new CompanyRepository();
        $this->products  = new ProductService();
    }

    public function index()
    {
        $page    = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage = 15;
        $q       = $this->request->getGet('q');
        $status  = $this->request->getGet('status');

        $builder = $this->model->where('deleted_at', null);
        if ($q) {
            $builder->like('grv_no', $q);
        }
        if ($status) {
            $builder->where('status', $status);
        }

        $total = $builder->countAllResults(false);
        $grvs  = $builder->orderBy('grv_date', 'DESC')->orderBy('id', 'DESC')
                         ->paginate($perPage, 'default', $page);

        $db = Database::connect();
        foreach ($grvs as &$g) {
            $cust = $g['customer_un_id']
                ? $db->table('customers')->select('customer_name')->where('un_id', $g['customer_un_id'])->get()->getRowArray()
                : null;
            $g['customer_name'] = $cust['customer_name'] ?? '-';
            $g['item_count']    = $this->items->where('grv_un_id', $g['un_id'])->where('deleted_at', null)->countAllResults();
        }
        unset($g);

        // Totals
        $totalsRow = $db->table('goods_return_vouchers')
            ->selectSum('total_amount', 'total')
            ->where('deleted_at', null)->get()->getRowArray();

        return view('admin/grv/index', [
            'title'      => 'Goods Return Vouchers',
            'grvs'       => $grvs,
            'filters'    => ['q' => $q, 'status' => $status],
            'totals'     => [
                'total_amount' => (float) ($totalsRow['total'] ?? 0),
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
        $customerUnId = $this->request->getGet('customer_un_id');
        $saleUnId     = $this->request->getGet('sale_un_id');
        return view('admin/grv/form', [
            'title'               => 'New GRV',
            'grv'                 => null,
            'grv_items'           => [],
            'customers'           => $this->customers->search([], 1, 200)['items'],
            'companies'           => $this->companies->search([], 1, 100)['items'],
            'products'            => $this->products->forSelect(),
            'action'              => site_url('admin/grv'),
            'preselect_customer'  => $customerUnId,
            'preselect_sale'      => $saleUnId,
        ]);
    }

    public function store()
    {
        if (! $this->validate([
            'customer_un_id' => 'required',
            'grv_date'       => 'required',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $post  = $this->request->getPost();
        $items = $this->parseItems($post);

        if (empty($items)) {
            return redirect()->back()->withInput()->with('error', 'Add at least one returned item.');
        }

        $totalAmount = array_sum(array_column($items, '_line_total'));

        $grv = [
            'grv_no'           => $this->nextGrvNo(),
            'customer_un_id'   => $post['customer_un_id'],
            'company_un_id'    => $post['company_un_id'] ?? null,
            'sale_un_id'       => $post['sale_un_id'] ?? null,
            'grv_date'         => $post['grv_date'],
            'description'      => $post['description'] ?? null,
            'notes'            => $post['notes'] ?? null,
            'total_amount'     => $totalAmount,
            'status'           => $post['status'] ?? 'draft',
            'created_by_un_id' => session('user_un_id'),
        ];

        $id  = $this->model->insert($grv, true);
        if ($id === false) {
            return redirect()->back()->withInput()->with('error', 'Failed to create GRV.');
        }
        $row = $this->model->find($id);

        foreach ($items as $item) {
            unset($item['_line_total']);
            $item['grv_un_id'] = $row['un_id'];
            $this->items->insert($item);
        }

        // Auto stock-in + customer credit if approved on creation
        if ($row['status'] === 'approved') {
            $this->applyStockIn($row['un_id'], $row['grv_no']);
            $this->applyCustomerCredit($row);
        }

        return redirect()->to('admin/grv/' . $row['un_id'])->with('success', 'GRV ' . $row['grv_no'] . ' created.');
    }

    public function edit(string $unId)
    {
        $grv = $this->model->where('un_id', $unId)->where('deleted_at', null)->first();
        if (! $grv) return redirect()->to('admin/grv')->with('error', 'GRV not found.');
        if ($grv['status'] === 'approved') {
            return redirect()->to('admin/grv/' . $unId)->with('error', 'Approved GRV cannot be edited.');
        }

        $grvItems = $this->items->where('grv_un_id', $unId)->where('deleted_at', null)->orderBy('id', 'ASC')->findAll();

        return view('admin/grv/form', [
            'title'      => 'Edit ' . $grv['grv_no'],
            'grv'        => $grv,
            'grv_items'  => $grvItems,
            'customers'  => $this->customers->search([], 1, 200)['items'],
            'companies'  => $this->companies->search([], 1, 100)['items'],
            'products'   => $this->products->forSelect(),
            'action'     => site_url('admin/grv/' . $unId),
        ]);
    }

    public function update(string $unId)
    {
        $grv = $this->model->where('un_id', $unId)->where('deleted_at', null)->first();
        if (! $grv) return redirect()->to('admin/grv')->with('error', 'GRV not found.');
        if ($grv['status'] === 'approved') {
            return redirect()->to('admin/grv/' . $unId)->with('error', 'Approved GRV cannot be edited.');
        }

        if (! $this->validate(['customer_un_id' => 'required', 'grv_date' => 'required'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $post  = $this->request->getPost();
        $items = $this->parseItems($post);

        if (empty($items)) {
            return redirect()->back()->withInput()->with('error', 'Add at least one returned item.');
        }

        $totalAmount = array_sum(array_column($items, '_line_total'));

        $this->model->update($grv['id'], [
            'customer_un_id' => $post['customer_un_id'],
            'company_un_id'  => $post['company_un_id'] ?? null,
            'sale_un_id'     => $post['sale_un_id'] ?? null,
            'grv_date'       => $post['grv_date'],
            'description'    => $post['description'] ?? null,
            'notes'          => $post['notes'] ?? null,
            'total_amount'   => $totalAmount,
            'status'         => $post['status'] ?? 'draft',
        ]);

        // Replace items: soft-delete old, insert new
        $this->items->where('grv_un_id', $unId)->set(['deleted_at' => date('Y-m-d H:i:s')])->update();
        foreach ($items as $item) {
            unset($item['_line_total']);
            $item['grv_un_id'] = $unId;
            $this->items->insert($item);
        }

        if ($post['status'] === 'approved' && $grv['status'] !== 'approved') {
            $this->applyStockIn($unId, $grv['grv_no']);
            $fresh = $this->model->where('un_id', $unId)->first();
            if ($fresh) $this->applyCustomerCredit($fresh);
        }

        return redirect()->to('admin/grv/' . $unId)->with('success', 'GRV updated.');
    }

    public function show(string $unId)
    {
        $grv = $this->model->where('un_id', $unId)->where('deleted_at', null)->first();
        if (! $grv) return redirect()->to('admin/grv')->with('error', 'GRV not found.');

        $customer = $grv['customer_un_id'] ? $this->customers->findByUnId($grv['customer_un_id']) : null;
        $company  = $grv['company_un_id']  ? $this->companies->findByUnId($grv['company_un_id'])  : null;
        $grvItems = $this->items->where('grv_un_id', $unId)->where('deleted_at', null)->orderBy('id', 'ASC')->findAll();

        // Resolve linked sale info
        $sale = null;
        if ($grv['sale_un_id']) {
            $db   = Database::connect();
            $sale = $db->table('sales')->where('un_id', $grv['sale_un_id'])->get()->getRowArray();
        }

        return view('admin/grv/show', [
            'title'     => $grv['grv_no'],
            'grv'       => $grv,
            'customer'  => $customer,
            'company'   => $company,
            'grv_items' => $grvItems,
            'sale'      => $sale,
        ]);
    }

    public function approve(string $unId)
    {
        $grv = $this->model->where('un_id', $unId)->where('deleted_at', null)->first();
        if (! $grv) return redirect()->to('admin/grv')->with('error', 'GRV not found.');
        if ($grv['status'] === 'approved') {
            return redirect()->to('admin/grv/' . $unId)->with('error', 'GRV already approved.');
        }

        $this->model->update($grv['id'], ['status' => 'approved']);
        $this->applyStockIn($unId, $grv['grv_no']);
        $this->applyCustomerCredit($grv);

        return redirect()->to('admin/grv/' . $unId)->with('success', 'GRV approved — stock restocked and customer due credited.');
    }

    public function delete(string $unId)
    {
        $grv = $this->model->where('un_id', $unId)->where('deleted_at', null)->first();
        if (! $grv) return redirect()->to('admin/grv')->with('error', 'GRV not found.');

        $this->model->deleteByUnId($unId);
        return redirect()->to('admin/grv')->with('success', 'GRV deleted.');
    }

    private function parseItems(array $post): array
    {
        $names    = $post['item_product_name'] ?? [];
        $unIds    = $post['item_product_un_id'] ?? [];
        $units    = $post['item_unit'] ?? [];
        $qtys     = $post['item_quantity'] ?? [];
        $prices   = $post['item_unit_price'] ?? [];
        $vats     = $post['item_vat'] ?? [];
        $reasons  = $post['item_reason'] ?? [];

        $items = [];
        foreach ($names as $i => $name) {
            $name = trim($name);
            if ($name === '') continue;
            $qty   = (float) ($qtys[$i] ?? 0);
            $price = (float) ($prices[$i] ?? 0);
            $vat   = (float) ($vats[$i] ?? 0);
            if ($qty <= 0) continue;

            $items[] = [
                'product_un_id' => $unIds[$i] ?? null ?: null,
                'product_name'  => $name,
                'unit'          => $units[$i] ?? 'pcs',
                'quantity'      => $qty,
                'unit_price'    => $price,
                'vat'           => $vat,
                'reason'        => trim($reasons[$i] ?? ''),
                '_line_total'   => $qty * $price + $vat,
            ];
        }
        return $items;
    }

    private function applyStockIn(string $grvUnId, string $grvNo): void
    {
        $items = $this->items->where('grv_un_id', $grvUnId)->where('deleted_at', null)->findAll();
        if (empty($items)) return;

        try {
            $stockService = new StockService();
            foreach ($items as $item) {
                if (! $item['product_un_id']) continue;
                // addForProduct auto-creates the stock item if the product has none
                $stockService->addForProduct(
                    $item['product_un_id'],
                    (float) $item['quantity'],
                    $grvNo,
                    (float) $item['unit_price'],
                    'GRV return: ' . $grvNo
                );
            }
        } catch (\Throwable $e) {
            log_message('error', 'GRV stock-in failed for ' . $grvNo . ': ' . $e->getMessage());
        }
    }

    /**
     * Approved return reduces what the customer owes (credit note effect).
     */
    private function applyCustomerCredit(array $grv): void
    {
        $amount = (float) ($grv['total_amount'] ?? 0);
        if ($amount <= 0 || empty($grv['customer_un_id'])) return;

        try {
            $this->customers->adjustDue($grv['customer_un_id'], -$amount);
        } catch (\Throwable $e) {
            log_message('error', 'GRV customer credit failed for ' . ($grv['grv_no'] ?? '?') . ': ' . $e->getMessage());
        }
    }

    private function nextGrvNo(): string
    {
        $db   = Database::connect();
        $row  = $db->table('goods_return_vouchers')->select('grv_no')->orderBy('id', 'DESC')->limit(1)->get()->getRowArray();
        $year = date('Y');
        if ($row && preg_match('/GRV-' . $year . '-(\d+)/', $row['grv_no'], $m)) {
            $next = (int) $m[1] + 1;
        } else {
            $next = 1001;
        }
        return sprintf('GRV-%s-%05d', $year, $next);
    }
}
