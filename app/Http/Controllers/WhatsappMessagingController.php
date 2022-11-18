<?php

namespace App\Http\Controllers;

use App\Models\WhatsappSetting;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;


class WhatsappMessagingController extends Controller
{
    public function webhookToken()
    {
        $settings=WhatsappSetting::first();
        return $settings->bearer_token;
    }

    public function webhookId()
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

        $request = new Request('POST', 'https://graph.facebook.com/v13.0/'.$this->webhookId().'/messages', $headers, $body);
        $res = $client->sendAsync($request)->wait();
        return 0;
    }

    public function sendMsgTemplate($phone,$template,$data)
    {

        $client = new Client();
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$this->webhookToken()
        ];
        $body = '{
                  "messaging_product": "whatsapp",
                  "recipient_type": "individual",
                  "to": "'.$phone.'",
                  "type": "template",
                  "template": {
                    "name": "'.$template.'",
                    "language": {
                      "code": "en_GB"
                    },
                    "components": [
                      {
                        "type": "body",
                        "parameters": [
                          {
                            "type": "text",
                            "text": "'.$data.'"
                          }
                        ]
                      }
                    ]
                  }
                }';

        $request = new Request('POST', 'https://graph.facebook.com/v13.0/'.$this->webhookId().'/messages', $headers, $body);
        $res = $client->sendAsync($request)->wait();
        return 0;
    }

    public function checkMsgTime()
    {

    }

}
