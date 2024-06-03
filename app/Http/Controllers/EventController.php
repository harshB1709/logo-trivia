<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Models\AppSetting;
use App\Models\Event;
use App\Models\Wordset;
use App\Models\Word;
use Inertia\Inertia;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;
use Illuminate\Support\Facades\Storage;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Requests\StoreOrUpdateEventRequest;

class EventController extends Controller
{
    public function index(Request $request) {
        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
                    $query->where(function ($query) use ($value) {
                        Collection::wrap($value)->each(function ($value) use ($query) {
                            $query->orWhere('name', 'LIKE', "%{$value}%");
                        });
                    });
                });

        $events = QueryBuilder::for(Event::class)
                    ->with('wordset')
                    ->allowedSorts(['name'])
                    ->allowedFilters(['name', $globalSearch])
                    ->orderBy('id')
                    ->paginate()
                    ->withQueryString();

        $wordsets = Wordset::all();

        // $words = Word::where('is_active', true)->get();

        return Inertia::render('Events', [
            'events' => $events,
            'wordsets' => $wordsets,
            // 'words' => $words
        ])->table(function (InertiaTable $table) {
            $table
                ->withGlobalSearch('Search events..')
                ->column(key: 'name', sortable: true, canBeHidden: false)
                ->column(key: 'slug')
                ->column(key: 'wordset')
                ->column(key: 'start_date')
                ->column(key: 'end_date')
                ->column(key: 'is_active')
                ->column(key: 'Actions');
        });
    }

    public function store(StoreOrUpdateEventRequest $request) {
        $event = new Event();
        $event->name = $request->get('name', '');
        $event->slug = $request->get('slug', '');
        $event->wordset_id = $request->get('wordset_id', null);
        $event->start_date = $request->get('start_date', '');
        $event->end_date = $request->get('end_date', '');
        $event->home_content = $request->get('home_content', '');
        $event->is_active = $request->has('is_active') ? true : false;

        if($request->hasFile('event-bg')) {
            $file = $request->file('event-bg');
            $extension = $file->getClientOriginalExtension();
            $event->background_img_url = Storage::disk('public')->putFileAs('background', $file, uniqid() . '.' . $extension);
        }

        $event->save();

        $settings = [
            'app_status' => true,
            'player_registration' => true,
            'show_leaderboard' => false
        ];

        foreach($settings as $key => $value) {
            $app_setting = AppSetting::firstOrCreate([
                'key' => $key,
                'event_id' => $event->id
            ], [
                'value' => $value
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Event Created Successfully'
        ]);
    }

    public function update(StoreOrUpdateEventRequest $request, Event $event) {
        $event->name = $request->get('name', '');
        $event->slug = $request->get('slug', '');
        $event->wordset_id = $request->get('wordset_id', null);
        $event->start_date = $request->get('start_date', '');
        $event->end_date = $request->get('end_date', '');
        $event->home_content = $request->get('home_content', '');
        $event->is_active = $request->has('is_active') ? true : false;

        if($request->hasFile('event-bg')) {
            $file = $request->file('event-bg');
            $extension = $file->getClientOriginalExtension();
            $event->background_img_url = Storage::disk('public')->putFileAs('background', $file, uniqid() . '.' . $extension);
        }

        $event->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Event Updated Successfully'
        ]);
    }
}
