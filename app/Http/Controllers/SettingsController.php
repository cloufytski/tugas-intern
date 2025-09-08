<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function view()
    {
        if (!Auth::check()) {
            return redirect()->route('unauthorized');
        }
        return view('settings.settings');
    }

    public function accountView()
    {
        if (!Auth::check()) {
            return redirect()->route('unauthorized');
        }
        return view('settings.settings-account', [
            'user' => Auth::user(),
        ]);
    }

    public function securityView()
    {
        if (!Auth::check() || !Auth::user()->is_local) {
            return redirect()->route('unauthorized');
        }
        return view('settings.settings-security', [
            'user' => Auth::user(),
        ]);
    }

    public function filterView()
    {
        if (!Auth::check()) {
            return redirect()->route('unauthorized');
        }
        return view('settings.settings-filter');
    }
}
