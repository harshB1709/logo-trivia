<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
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

    public function appHome(Request $request) {
        $start = now()->subDays(2)->format('Y-m-d');
        $end = now()->addDays(2)->format('Y-m-d');
        $events = Event::whereBetween('start_date', [$start, $end])
            ->orwhereBetween('end_date', [$start, $end])
            ->get();

        return Inertia::render('AppHome', [
            'events' => $events
        ]);
    }

    public function runCommand(Request $request) {
        return Inertia::render('RunCommand', []);
    }

    public function postRunCommand(Request $request) {
        $command = $request->get('command', '');
        $output = null;
        try {
            $output = Artisan::call($command);
        } catch (\Exception $e) {
            $output = $e->getMessage();
        }
        $output = shell_exec($command);

        return response()->json([
            'success' => true,
            'output' => $output
        ]);
    }
}
