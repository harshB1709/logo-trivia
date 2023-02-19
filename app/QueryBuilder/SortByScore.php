<?php

namespace App\QueryBuilder;

use App\Models\Game;
use Spatie\QueryBuilder\Sorts\Sort;
use Illuminate\Database\Eloquent\Builder;

class SortByScore implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property)
    {
        $direction = $descending ? 'DESC' : 'ASC';

        $query->orderBy(
            Game::limit(1)
                ->select('score')
                ->whereColumn('games.player_id', 'players.id'),
            $direction
        );
    }
}
