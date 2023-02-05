<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Word;
use Illuminate\Support\Facades\File;
use Illuminate\Http\File as HttpFile;
use Illuminate\Support\Facades\Storage;

class WordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json = File::get("database/data/words.json");
        $words = json_decode($json);

        foreach ($words as $key => $value) {
            $name = strtolower($value->name);
            Word::create([
                "name" => $value->name,
                "points" => $value->points,
                "url" => Storage::putFileAs('logos', new HttpFile("database/data/logos/{$name}.svg"), uniqid().".svg")
            ]);
        }
    }
}
