<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Models\AppSetting;
use App\Models\Word;
use App\Models\Wordset;
use App\Http\Requests\StoreOrUpdateWordsetRequest;
use Inertia\Inertia;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;
use Illuminate\Support\Facades\Storage;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class WordsetController extends Controller
{
    public function index(Request $request) {
        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
                    $query->where(function ($query) use ($value) {
                        Collection::wrap($value)->each(function ($value) use ($query) {
                            $query->orWhere('name', 'LIKE', "%{$value}%");
                        });
                    });
                });

        $wordsets = QueryBuilder::for(Wordset::class)
                    ->with('words:id,name')
                    ->allowedSorts(['name'])
                    ->allowedFilters(['name', $globalSearch])
                    ->orderBy('id')
                    ->paginate()
                    ->withQueryString();

        $words = Word::where('is_active', true)->get();

        return Inertia::render('Wordsets', [
            'wordsets' => $wordsets,
            'words' => $words
        ])->table(function (InertiaTable $table) {
            $table
                ->withGlobalSearch('Search wordsets..')
                ->column(key: 'name', sortable: true, canBeHidden: false)
                ->column(key: 'words', label: 'Words')
                ->column(label: 'Wordcount')
                ->column(label: 'Actions');
        });
    }

    public function store(StoreOrUpdateWordsetRequest $request) {
        $wordset = Wordset::create([
            'name' => $request->get('name', '')
        ]);

        $words = array_keys($request->get('words', []));

        $wordset->words()->sync($words);

        return response()->json([
            'status' => 'success',
            'message' => 'Wordset Created Successfully'
        ]);
    }

    public function update(StoreOrUpdateWordsetRequest $request, Wordset $wordset) {
        $wordset->update([
            'name' => $request->get('name', '')
        ]);

        $words = array_keys($request->get('words', []));

        $wordset->words()->sync($words);

        return response()->json([
            'status' => 'success',
            'message' => 'Wordset Created Successfully'
        ]);
    }
}
