<?php

namespace App\Repositories;

use App\Models\CompanyModel;

class CompanyRepository extends BaseRepository
{
    protected function bootModel(): void
    {
        $this->model = new CompanyModel();
    }

    /**
     * Search/filter list with pagination.
     */
    public function search(array $filters, int $page = 1, int $perPage = 20): array
    {
        return $this->paginate($page, $perPage, function ($builder) use ($filters) {
            if (! empty($filters['q'])) {
                $q = $filters['q'];
                $builder->groupStart()
                    ->like('company_name', $q)
                    ->orLike('email', $q)
                    ->orLike('phone', $q)
                    ->orLike('trade_license', $q)
                    ->groupEnd();
            }
            if (! empty($filters['status'])) {
                $builder->where('status', $filters['status']);
            }
            if (! empty($filters['company_type'])) {
                $builder->where('company_type', $filters['company_type']);
            }
        });
    }

    public function totals(): array
    {
        $total    = $this->model->where('deleted_at', null)->countAllResults(false);
        $active   = $this->model->where('status', STATUS_ACTIVE)->where('deleted_at', null)->countAllResults(false);
        $inactive = $this->model->where('status', STATUS_INACTIVE)->where('deleted_at', null)->countAllResults();
        return compact('total', 'active', 'inactive');
    }

    public function statsForCompany(string $companyUnId): array
    {
        $db = \Config\Database::connect();

        $customers  = (int) $db->table('customers')->where('company_un_id', $companyUnId)->where('deleted_at', null)->countAllResults();
        $employees  = (int) $db->table('employees')->where('company_un_id', $companyUnId)->where('deleted_at', null)->countAllResults();
        $visas      = (int) $db->table('visas')->where('company_un_id', $companyUnId)->where('deleted_at', null)->countAllResults();
        $containers = (int) $db->table('containers')->where('company_un_id', $companyUnId)->where('deleted_at', null)->countAllResults();
        $vendors    = (int) $db->table('vendors')->where('company_un_id', $companyUnId)->where('deleted_at', null)->countAllResults();
        $farm_projects = (int) $db->table('farm_projects')->where('company_un_id', $companyUnId)->where('deleted_at', null)->countAllResults();

        $sales_total = (float) ($db->table('sales')
            ->selectSum('total_amount')
            ->where('company_un_id', $companyUnId)
            ->where('deleted_at', null)
            ->get()->getRow()->total_amount ?? 0);

        $expenses_total = (float) ($db->table('expenses')
            ->selectSum('amount')
            ->where('company_un_id', $companyUnId)
            ->where('deleted_at', null)
            ->get()->getRow()->amount ?? 0);

        $sales_due = (float) ($db->table('sales')
            ->selectSum('due_amount')
            ->where('company_un_id', $companyUnId)
            ->where('deleted_at', null)
            ->get()->getRow()->due_amount ?? 0);

        return compact(
            'customers', 'employees', 'visas', 'containers',
            'vendors', 'farm_projects',
            'sales_total', 'expenses_total', 'sales_due'
        );
    }

    public function recentCustomers(string $companyUnId, int $limit = 5): array
    {
        $db = \Config\Database::connect();
        return $db->table('customers')
            ->where('company_un_id', $companyUnId)
            ->where('deleted_at', null)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get()->getResultArray();
    }

    public function recentEmployees(string $companyUnId, int $limit = 5): array
    {
        $db = \Config\Database::connect();
        return $db->table('employees')
            ->where('company_un_id', $companyUnId)
            ->where('deleted_at', null)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get()->getResultArray();
    }

    public function recentSales(string $companyUnId, int $limit = 5): array
    {
        $db = \Config\Database::connect();
        return $db->table('sales')
            ->where('company_un_id', $companyUnId)
            ->where('deleted_at', null)
            ->orderBy('sale_date', 'DESC')
            ->limit($limit)
            ->get()->getResultArray();
    }
}
