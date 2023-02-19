<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use App\Models\Player;
use App\Models\Word;
use App\Models\Game;
use App\Notifications\GameInvite;
use App\QueryBuilder\SortByScore;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class PlayerController extends Controller
{
    const TOTAL_WORDS = 21;
    const GUESSES_PER_WORD = 3;
    const DELAY_SECONDS = 3;

    public function index(Request $request) {

        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
                    $query->where(function ($query) use ($value) {
                        Collection::wrap($value)->each(function ($value) use ($query) {
                            $query->orWhere('name', 'LIKE', "%{$value}%")
                                ->orWhere('email', 'LIKE', "%{$value}%")
                                ->orWhere('display_name', 'LIKE', "%{$value}%");
                        });
                    });
                });

        $custom_sort = AllowedSort::custom('score', new SortByScore())->defaultDirection('desc');

        $players = QueryBuilder::for(Player::class)
                    ->with('game')
                    ->allowedSorts([
                        'name',
                        'email',
                        'display_name',
                        $custom_sort,
                    ])
                    ->defaultSort($custom_sort)
                    ->allowedFilters(['name', 'email', 'display_name', $globalSearch])
                    ->paginate()
                    ->withQueryString();

        return Inertia::render('Players', [
            'players' => $players,
        ])->table(function (InertiaTable $table) {
            $table
                ->withGlobalSearch('Search players..')
                ->column(key: 'name', sortable: true, canBeHidden: false)
                ->column(key: 'email', sortable: true)
                ->column(key: 'display_name', sortable: true)
                ->column(key: 'score', sortable: true)
                ->column(label: 'Actions');
        });
    }

    public function sendInvite(Player $player, Request $request) {
        $player->notify(new GameInvite());

        return response()->json([
            'status' => 'success',
            'message' => 'Invite Sent Successfully'
        ]);
    }

    public function resetGame(Player $player, Request $request) {
        $player->game()?->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Game Reset Successfully'
        ]);
    }

    public function home(Request $request) {
        $registered = session('registered', false);
        return Inertia::render('Welcome', [
            'registered' => $registered
        ]);
    }

    public function register(Request $request) {
        $request->validate([
            'name' => 'required|string|max:100',
            'display_name' => 'string|max:60|nullable',
            'email' => 'required|string|email:rfc,dns|unique:players,email,max:255',
            'phone' => 'numeric|digits:10|nullable'
        ]);

        $player = Player::create([
            'email' => $request->get('email'),
            'name' => $request->get('name'),
            'display_name' => $request->get('display_name', null),
            'phone' => $request->get('phone') ?? ''
        ]);

        $player->notify(new GameInvite());

        return redirect()->route('home')
                    ->with('registered', true);
    }

    public function gamePage(Request $request, Player $player) {
        $request->session()->forget(['words', 'points_scored', 'current_index', 'started_at']);
        if($request->user())
            $player->game()?->delete();

        if ((!$request->hasValidSignature() && !$request->user()) || $player->game) {
            abort(401);
        }

        session(['player_id' => $player->id ]);
        return Inertia::render('Game');
    }

    public function startGame(Request $request) {
        $player = Player::findOrFail(session('player_id'));

        if($player->game && !$request->user())
            abort(403);

        $words = collect();

        for($i = 1; $i < 4; $i++) {
            $words = $words->merge(
                Word::inRandomOrder()
                    ->where([
                        ['points', $i],
                        ['is_active', true]
                    ])
                    ->limit(self::TOTAL_WORDS/3)
                    ->get()
            );
        }

        $game = Game::create([
            'player_id' => session('player_id'),
            'score' => 0
        ]);

        $game->words()->syncWithPivotValues($words->map(fn ($word) => $word->id)->toArray(), ['score' => 0]);

        $words = $words->map(function($word) {
            $word->setAppends([]);
            return [
                'id' => $word->id,
                'name' => $word->name,
                'url' => $word->url,
                'characters' => strlen($word->name),
                'guesses_remaining' => self::GUESSES_PER_WORD,
                'points' => $word->points,
                'hint' => !empty($word->hint) ? $word->hint : null,
                'is_completed' => false
            ];
        })->toArray();

        session([
            'words' => $words,
            'points_scored' => 0,
            'current_index' => 0,
            'started_at' => now()->addSeconds(self::DELAY_SECONDS + 3)->timestamp
        ]);

        return response()->json([
            'logo' => Storage::get($words[0]['url']),
            'charLength' => $words[0]['characters'],
            'guessesRemaining' => $words[0]['guesses_remaining'],
            'hasHint' => (bool) $words[0]['hint']
        ]);
    }

    public function gameAction(Request $request) {
        function incrementIndex(&$current_index, &$word_change, &$game_over, &$started_at) {
            $current_index++;
            $word_change = $current_index <= (PlayerController::TOTAL_WORDS - 1);
            $game_over = $current_index > (PlayerController::TOTAL_WORDS - 1);
            $started_at = now()->addSeconds(PlayerController::DELAY_SECONDS)->timestamp;
        }

        $action = $request->get('action', 'skipWord');

        if(in_array($action, ['guessWord', 'skipWord', 'getHint'])) {
            $word_change = false;
            $game_over = false;
            $words = session('words', []);
            $current_index = session('current_index', null);
            $points_scored = session('points_scored', 0);
            $hint = null;
            $started_at = session('started_at');
            $time_elapsed = now()->timestamp - $started_at;
            if($time_elapsed > 40) {
                $request->session()->forget(['words', 'points_scored', 'current_index', 'started_at']);
                return response()->json([
                    'status' => 'redirect',
                    'redirect' => route('home')
                ]);
            }


            switch ($action) {
                case 'guessWord':
                    $guess = $request->get('guess', '');
                    $word = &$words[$current_index];
                    logger(now()->timestamp . ' ' . $started_at . ' ' . 31 - $time_elapsed);
                    if(strtolower($guess) === strtolower($word['name'])) {
                        $time_remaining = 31 - $time_elapsed;
                        $time_remaining = min($time_remaining, 30);
                        $time_remaining = max(1, $time_remaining);
                        $word_points = $word['points'] * ($word['guesses_remaining'] + $time_remaining);
                        $points_scored += $word_points;
                        $game = Game::where('player_id', session('player_id'))->first();
                        $game->score = $points_scored;
                        $game->save();
                        $game
                            ->words()
                            ->wherePivot('word_id', $word['id'])
                            ->update([
                                'score' => $word_points
                            ]);
                        incrementIndex($current_index, $word_change, $game_over, $started_at);
                    }
                    else {
                        $word['guesses_remaining']  = $word['guesses_remaining'] - 1;
                        if($word['guesses_remaining'] == 0) {
                            incrementIndex($current_index, $word_change, $game_over, $started_at);
                        }
                    }
                    break;

                case 'skipWord':
                    incrementIndex($current_index, $word_change, $game_over, $started_at);
                    break;

                case 'getHint':
                    $word = &$words[$current_index];
                    if(($word['guesses_remaining'] > 1) && $word['hint']) {
                        $word['guesses_remaining']  = $word['guesses_remaining'] - 1;
                        $hint = $word['hint'];
                        $word['hint'] = null;
                    }
                    break;
            }

            $curr_word = null;

            if(!$game_over) {
                session([
                    'words' => $words,
                    'points_scored' => $points_scored,
                    'current_index' => $current_index,
                    'started_at' => $started_at
                ]);

                $curr_word = $words[$current_index];
            }
            else {
                $request->session()->forget(['words', 'points_scored', 'current_index', 'started_at']);
            }

            return response()->json([
                'points' => $points_scored,
                'guessesRemaining' => !$game_over ? $curr_word['guesses_remaining'] : 0,
                'gameOver' => $game_over,
                'wordChange' => $word_change,
                'logo' => $word_change ? Storage::get($curr_word['url']) : null,
                'charLength' => $word_change ? $curr_word['characters'] : null,
                'hint' => $hint,
                'hasHint' => !$game_over && ($curr_word['guesses_remaining'] > 1) && ((bool) $curr_word['hint'])
            ]);
        }
        abort(404);
    }

    public function leaderboard(Request $request) {
        $games = Game::with('player:id,name,display_name')
                    ->select('id', 'player_id', 'score')
                    ->orderByDesc('score')
                    ->paginate(25);

        $games->onEachSide(0)->links();

        return Inertia::render('Leaderboard', [
            'games' => $games,
        ]);
    }
}
