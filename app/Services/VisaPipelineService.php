<?php

namespace App\Services;

use App\Models\VisaStageModel;
use Config\Database;

class VisaPipelineService extends BaseService
{
    public const STAGES = [
        'applied'             => ['label' => 'Applied',             'color' => 'secondary'],
        'documents_submitted' => ['label' => 'Docs Submitted',      'color' => 'primary'],
        'biometrics'          => ['label' => 'Biometrics',          'color' => 'info'],
        'processing'          => ['label' => 'Processing',          'color' => 'warning'],
        'approved'            => ['label' => 'Approved',            'color' => 'success'],
        'rejected'            => ['label' => 'Rejected',            'color' => 'danger'],
        'delivered'           => ['label' => 'Delivered',           'color' => 'success'],
    ];

    public function stagesFor(string $visaUnId): array
    {
        return (new VisaStageModel())
            ->where('visa_un_id', $visaUnId)
            ->orderBy('stage_date', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
    }

    public function addStage(string $visaUnId, array $input): string
    {
        $model   = new VisaStageModel();
        $userUnId = session('user_un_id');

        $data = [
            'visa_un_id'       => $visaUnId,
            'stage'            => $input['stage'],
            'notes'            => $input['notes'] ?? null,
            'stage_date'       => $input['stage_date'] ?: date('Y-m-d'),
            'changed_by_un_id' => $userUnId,
        ];

        $id  = $model->insert($data, true);
        $row = $model->find($id);
        $unId = $row['un_id'] ?? '';

        // Sync visa status to current stage
        $db = Database::connect();
        $db->table('visas')
           ->where('un_id', $visaUnId)
           ->update(['status' => $input['stage'], 'updated_at' => date('Y-m-d H:i:s')]);

        $this->audit('visa.stage_added', 'visa', $visaUnId, ['stage' => $input['stage']]);
        return $unId;
    }

    public function pipeline(): array
    {
        $db     = Database::connect();
        $counts = [];
        foreach (array_keys(self::STAGES) as $stage) {
            $counts[$stage] = (int) $db->table('visas')
                ->where('status', $stage)
                ->where('deleted_at', null)
                ->countAllResults();
        }
        return $counts;
    }

    public function recentByStage(int $limit = 5): array
    {
        $db     = Database::connect();
        $result = [];
        foreach (array_keys(self::STAGES) as $stage) {
            $rows = $db->table('visas')
                ->where('status', $stage)
                ->where('deleted_at', null)
                ->orderBy('updated_at', 'DESC')
                ->limit($limit)
                ->get()->getResultArray();
            if ($rows) $result[$stage] = $rows;
        }
        return $result;
    }
}
