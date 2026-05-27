<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\VisaPipelineService;
use App\Services\VisaService;

class VisaPipelineController extends BaseController
{
    private VisaPipelineService $pipeline;
    private VisaService         $visas;

    public function __construct()
    {
        $this->pipeline = new VisaPipelineService();
        $this->visas    = new VisaService();
    }

    public function index()
    {
        return view('admin/visas/pipeline', [
            'title'   => 'Visa Pipeline',
            'stages'  => VisaPipelineService::STAGES,
            'counts'  => $this->pipeline->pipeline(),
            'recent'  => $this->pipeline->recentByStage(5),
        ]);
    }

    public function timeline(string $visaUnId)
    {
        $visa = $this->visas->get($visaUnId);
        if (! $visa) return redirect()->to('admin/visas')->with('error', 'Visa not found.');
        return view('admin/visas/_timeline', [
            'visa'   => $visa,
            'stages' => $this->pipeline->stagesFor($visaUnId),
            'labels' => VisaPipelineService::STAGES,
        ]);
    }

    public function addStage(string $visaUnId)
    {
        if (! $this->validate([
            'stage'      => 'required|in_list[applied,documents_submitted,biometrics,processing,approved,rejected,delivered]',
            'stage_date' => 'required',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        try {
            $this->pipeline->addStage($visaUnId, $this->request->getPost());
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
        return redirect()->to('admin/visas/' . $visaUnId)->with('success', 'Stage added.');
    }
}
