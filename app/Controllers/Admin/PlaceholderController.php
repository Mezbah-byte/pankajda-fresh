<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

/**
 * PlaceholderController - clean "coming soon" page for modules that are
 * planned but not yet implemented (Employees, Farm Projects, Expenses,
 * Reports, Settings). Routes hit this until the real controllers ship.
 */
class PlaceholderController extends BaseController
{
    public function employees()    { return $this->render('Employees', 'bi-person-badge'); }
    public function farmProjects() { return $this->render('Farm Projects', 'bi-tree'); }
    public function expenses()     { return $this->render('Expenses', 'bi-cash-coin'); }
    public function reports()      { return $this->render('Reports', 'bi-graph-up'); }
    public function settings()     { return $this->render('Settings', 'bi-gear-fill'); }

    private function render(string $module, string $icon)
    {
        return view('admin/placeholder', [
            'title'  => $module,
            'module' => $module,
            'icon'   => $icon,
        ]);
    }
}
