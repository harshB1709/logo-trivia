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
                    ->limit(2)
                    ->get()
            );
        }

        $words = $words->map(function($word) {
            $word->setAppends([]);
            return [
                'id' => $word->id,
                'name' => $word->name,
                'url' => $word->url,
                'characters' => strlen($word->name),
                'guesses_remaining' => 3,
                'points' => $word->points,
                'hints' => null,
                'is_completed' => false
            ];
        })->toArray();

        session([
            'words' => $words,
            'points_scored' => 0,
            'current_index' => 0
        ]);

        return response()->json([
            'logo' => Storage::get($words[0]['url']),
            'charLength' => $words[0]['characters']
        ]);
    }

    public function gameAction(Request $request) {
        function incrementIndex(&$current_index, &$word_change, &$game_over) {
            $current_index++;
            $word_change = $current_index <= 5;
            $game_over = $current_index > 5;
        }

        $action = $request->get('action', 'skipWord');

        if(in_array($action, ['guessWord', 'skipWord'])) {
            $word_change = false;
            $game_over = false;
            $words = session('words', []);
            $current_index = session('current_index', null);
            $points_scored = session('points_scored', 0);

            switch ($action) {
                case 'guessWord':
                    $guess = $request->get('guess', '');
                    $word = &$words[$current_index];
                    if(strtolower($guess) === strtolower($word['name'])) {
                        $points_scored += $word['guesses_remaining']*$word['points'];
                        incrementIndex($current_index, $word_change, $game_over);
                    }
                    else {
                        $word['guesses_remaining']  = $word['guesses_remaining'] - 1;
                        if($word['guesses_remaining'] == 0) {
                            incrementIndex($current_index, $word_change, $game_over);
                        }
                    }
                    break;

                case 'skipWord':
                    incrementIndex($current_index, $word_change, $game_over);
                    break;
            }

            $curr_word = null;

            if(!$game_over) {
                session([
                    'words' => $words,
                    'points_scored' => $points_scored,
                    'current_index' => $current_index
                ]);

                $curr_word = $words[$current_index];
            }
            else {
                $request->session()->forget(['words', 'points_scored', 'current_index']);
                $game = Game::create([
                    'player_id' => session('player_id'),
                    'score' => $points_scored,
                ]);
            }

            return response()->json([
                'points' => $points_scored,
                'guessesRemaining' => !$game_over ? $curr_word['guesses_remaining'] : 0,
                'gameOver' => $game_over,
                'wordChange' => $word_change,
                'logo' => $word_change ? Storage::get($curr_word['url']) : null,
                'charLength' => $word_change ? $curr_word['characters'] : null
            ]);
        }
        abort(404);
    }
}
