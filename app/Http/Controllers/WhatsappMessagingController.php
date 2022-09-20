<?php

namespace App\Http\Controllers;

use App\Models\WhatsappSetting;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class WhatsappMessagingController extends Controller
{
    public function webhookToken():string
    {
        $settings=WhatsappSetting::first();
        return $settings->bearer_token;
    }

    public function webhookId():string
    {
        $settings=WhatsappSetting::first();
        return $settings->whatsapp_id;
    }

    public function sendMsgText($phone,$textMsg)
    {
        $client = new Client();
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$this->webhookToken()
        ];
        $body = '{
                "messaging_product": "whatsapp",
                "preview_url": false,
                "recipient_type": "individual",
                "to": "'.$phone.'",
                "type": "text",
                "text": {
                    "body": "'. $textMsg .'"
                }
            }';

        $request = new \GuzzleHttp\Psr7\Request('POST', 'https://graph.facebook.com/v13.0/'.$this->webhookId().'/messages', $headers, $body);
        $res = $client->sendAsync($request)->wait();
        echo $res->getBody();
    }

    public function checkMsgTime()
    {

    }

}
