<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Event;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function home(Request $request, Event $event) {
        $self_registration = $event->appSettings()->where([
            'key' => 'player_registration'
        ])->first() ?? [
            'value' => true,
            'message' => ''
        ];

        return Inertia::render('Home', [
            'registrationSetting' => $self_registration
        ]);
    }

    public function adminHome(Request $request) {
        return redirect()->route('login');
    }
}
