<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Repositories\CompanyRepository;

class HomeController extends BaseController
{
    public function index()
    {
        return view('web/home', [
            'title' => 'Pankaj Da Business - Modern ERP',
        ]);
    }

    public function about()
    {
        return view('web/about', ['title' => 'About Us']);
    }

    public function services()
    {
        return view('web/services', ['title' => 'Services']);
    }

    public function companies()
    {
        $repo = new CompanyRepository();
        $result = $repo->search(['status' => 'active'], 1, 30);
        return view('web/companies', [
            'title'     => 'Our Companies',
            'companies' => $result['items'],
        ]);
    }

    public function contact()
    {
        return view('web/contact', ['title' => 'Contact Us']);
    }

    public function submitContact()
    {
        $rules = [
            'name'    => 'required|min_length[2]',
            'email'   => 'required|valid_email',
            'message' => 'required|min_length[10]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Please correct the errors.');
        }
        // TODO: persist or email — for now we acknowledge
        return redirect()->to('contact')->with('success', 'Thank you! We\'ll be in touch soon.');
    }
}
