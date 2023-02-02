<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Models\Word;
use Inertia\Inertia;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;
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

        return Inertia::render('Dashboard', [
            'words' => $words
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
}
