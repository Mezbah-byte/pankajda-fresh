<?php

namespace App\Services;

use App\Models\VisaStageModel;
use Config\Database;

class VisaPipelineService extends BaseService
{
    public const NEW_KEY = '__new__';

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
        $db         = Database::connect();
        $stageKeys  = array_keys(self::STAGES);
        $counts     = [];

        foreach ($stageKeys as $stage) {
            $counts[$stage] = (int) $db->table('visas')
                ->where('status', $stage)
                ->where('deleted_at', null)
                ->countAllResults();
        }

        // Visas not yet entered into any pipeline stage
        $counts[self::NEW_KEY] = (int) $db->table('visas')
            ->whereNotIn('status', $stageKeys)
            ->where('deleted_at', null)
            ->countAllResults();

        return $counts;
    }

    public function recentByStage(int $limit = 5): array
    {
        $db        = Database::connect();
        $stageKeys = array_keys(self::STAGES);
        $result    = [];

        foreach ($stageKeys as $stage) {
            $rows = $db->table('visas v')
                ->select('v.*, co.company_name')
                ->join('companies co', 'co.un_id = v.company_un_id', 'left')
                ->where('v.status', $stage)
                ->where('v.deleted_at', null)
                ->orderBy('v.updated_at', 'DESC')
                ->limit($limit)
                ->get()->getResultArray();
            $result[$stage] = $rows;
        }

        // "New" bucket: visas with status outside pipeline stage keys
        $result[self::NEW_KEY] = $db->table('visas v')
            ->select('v.*, co.company_name')
            ->join('companies co', 'co.un_id = v.company_un_id', 'left')
            ->whereNotIn('v.status', $stageKeys)
            ->where('v.deleted_at', null)
            ->orderBy('v.updated_at', 'DESC')
            ->limit($limit)
            ->get()->getResultArray();

        return $result;
    }
}
