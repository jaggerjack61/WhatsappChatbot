<?php

namespace Database\Seeders;

use App\Models\WhatsappSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WhatsappSettings extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        WhatsappSetting::create([
            'bearer_token'=>'EAAI6mp4cpyIBALKfZCL85vDYuoBPgZBXZCUsPTtvukD5t5apMFfWtUKx4rqwZAHYah5zBZBwJZABt3CxuTMjmhbxEECH5SJ1ZBp5M3fHuMFQzgoapzyRYdzAkoZCI2TFy29vtw4AeZAjh9ppuS68YK00bz4q6CuCYGYPcgb9w7OfPYK4y2yPb1H4o',
            'whatsapp_id'=>'102848469183001'
        ]);
    }
}
