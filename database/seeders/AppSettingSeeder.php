<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AppSetting;

class AppSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settings = [
            'app_status' => true,
            'player_registration' => true
        ];

        foreach($settings as $key => $value) {
            $app_setting = AppSetting::firstOrCreate([
                'key' => $key
            ], [
                'value' => $value
            ]);
        }
    }
}
