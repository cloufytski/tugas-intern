<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MiscController extends Controller
{
    public function comingSoonPage()
    {
        return view('contents.errors.comingsoon-misc');
    }

    public function underMaintenancePage()
    {
        return view('contents.errors.under-maintenance-misc');
    }

    public function errorPage()
    {
        return view('contents.errors.error-misc');
    }

    public function unauthorizedPage()
    {
        return view('contents.errors.not-authorized-misc');
    }

    public function dashboardView()
    {
        // NOTE: change here for main dashboard routing, future if there is certain role access
        if (Auth::user()->hasRole('material-procurement')) {
            return redirect('procurement/dashboard');
        }
        return redirect('inventory/dashboard');
    }
}
