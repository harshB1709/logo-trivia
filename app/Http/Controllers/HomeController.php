<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function home(Request $request) {
        $self_registration = AppSetting::where([
            'key' => 'player_registration'
        ])->first() ?? [
            'value' => true,
            'message' => ''
        ];

        return Inertia::render('Home', [
            'registrationSetting' => $self_registration
        ]);
    }
}
