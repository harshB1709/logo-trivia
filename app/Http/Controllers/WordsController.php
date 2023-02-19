<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Models\AppSetting;
use App\Models\Word;
use App\Http\Requests\StoreOrUpdateWordRequest;
use Inertia\Inertia;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;
use Illuminate\Support\Facades\Storage;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class WordsController extends Controller
{
    public function index(Request $request) {

        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
                    $query->where(function ($query) use ($value) {
                        Collection::wrap($value)->each(function ($value) use ($query) {
                            $query->orWhere('name', 'LIKE', "%{$value}%");
                        });
                    });
                });

        $words = QueryBuilder::for(Word::class)
                    ->allowedSorts(['name', 'points'])
                    ->allowedFilters(['name', 'is_active', $globalSearch])
                    ->paginate()
                    ->withQueryString();

        return Inertia::render('Words', [
            'words' => $words,
        ])->table(function (InertiaTable $table) {
            $table
                ->withGlobalSearch('Search words..')
                ->column(key: 'name', sortable: true, canBeHidden: false)
                ->column(key: 'url', label: 'Logo')
                ->column(key: 'points', sortable: true)
                ->column(key: 'is_active', label: 'Status')
                ->column(label: 'Actions')
                ->selectFilter('is_active', [
                    0 => "Disabled",
                    1 => "Enabled",
                ]);
        });
    }

    public function store(StoreOrUpdateWordRequest $request) {
        $word = new Word();
        $word->name = $request->get('name');
        $word->points = $request->get('points');
        $word->hint = $request->get('hint', null);
        $word->is_active = $request->has('is_active') ? true : false;

        if($request->hasFile('svg-file')) {
            $word->url = Storage::putFileAs('logos', $request->file('svg-file'), uniqid().".svg");
        }

        $word->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Word Created Successfully'
        ]);
    }

    public function update(StoreOrUpdateWordRequest $request, Word $word) {
        $word->name = $request->get('name');
        $word->points = $request->get('points');
        $word->hint = $request->get('hint', null);
        $word->is_active = $request->has('is_active') ? true : false;

        if($request->hasFile('svg-file')) {
            $word->url = Storage::putFileAs('logos', $request->file('svg-file'), uniqid().".svg");
        }

        $word->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Word Updated Successfully'
        ]);
    }

    public function toggleSetting(Request $request) {
        $setting_key = $request->get('setting', null);
        if($setting_key) {
            $setting = AppSetting::where('key', $setting_key)->firstOrFail();
            $setting->value = !$setting->value;
            if(!$setting->value) {
                $setting->message = $request->get('message', '');
            }
            $setting->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Setting toggled Successfully',
                'value' => (int) $setting->value,
                'settingMessage' => $setting->message
            ]);
        }
        abort(400);
    }
}
