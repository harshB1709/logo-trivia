<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Player;
use App\Models\Word;
use App\Models\Game;
use App\Notifications\GameInvite;
use Illuminate\Support\Facades\Storage;

class PlayerController extends Controller
{
    const TOTAL_WORDS = 15;
    const GUESSES_PER_WORD = 3;

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
            'phone' => 'required|numeric|digits:10'
        ]);

        $player = Player::create([
            'email' => $request->get('email'),
            'name' => $request->get('name'),
            'display_name' => $request->get('display_name', null),
            'phone' => $request->get('phone')
        ]);

        $player->notify(new GameInvite());

        return redirect()->route('home')
                    ->with('registered', true);
    }

    public function gamePage(Request $request, Player $player) {
        if($request->user())
            $player->game()?->delete();

        if ((! $request->hasValidSignature()) || $player->game) {
            abort(401);
        }

        session(['player_id' => $player->id ]);
        return Inertia::render('Game');
    }

    public function startGame(Request $request) {
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
            'started_at' => now()->addSeconds(3)->timestamp
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
            $started_at = now()->addSeconds(3)->timestamp;
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

            switch ($action) {
                case 'guessWord':
                    $guess = $request->get('guess', '');
                    $word = &$words[$current_index];
                    logger(now()->timestamp . ' ' . $started_at . ' ' . 31 - now()->timestamp + $started_at);
                    if(strtolower($guess) === strtolower($word['name'])) {
                        $time_remaining = 31 - now()->timestamp + $started_at;
                        $time_remaining = min($time_remaining, 30);
                        $word_points = $word['guesses_remaining'] * ($word['points'] + $time_remaining);
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
                $request->session()->forget(['words', 'points_scored', 'current_index']);
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
}
