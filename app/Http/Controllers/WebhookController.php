<?php

namespace App\Http\Controllers;


use App\Models\WhatsappSetting;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class WebhookController extends Controller
{
    public $phone;


    public function webhookSetup(Request $request)
    {

        $mode=$request->hub_mode;
        $token=$request->hub_verify_token;
        $challenge=$request->hub_challenge;
        if($mode and $token){
            $fptr = fopen('myfile.txt','w');
            fwrite($fptr,$mode." ".$token." ".$challenge." ".json_encode($request->all())." hhhhh ".json_encode($request));
            fclose($fptr);
            return response ($challenge, 200);
        }
        return response('',404);

    }

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

    public function webhookReceiver(Request $request)
    {

        $fptr = fopen('myfile.txt', 'w');
        fwrite($fptr, json_encode($request->all()));
        fclose($fptr);
        $arr = $request->all();


        if(array_key_exists('messages',$arr['entry'][0]['changes'][0]['value'])){
            if(array_key_exists('text',$arr['entry'][0]['changes'][0]['value']['messages'][0])){
                $this->handleMsg($arr);
            }
            elseif(array_key_exists('interactive',$arr['entry'][0]['changes'][0]['value']['messages'][0])) {
                if (array_key_exists('button_reply', $arr['entry'][0]['changes'][0]['value']['messages'][0]['interactive'])) {
                    $this->handleResponse($arr);

                }
                elseif(array_key_exists('list_reply', $arr['entry'][0]['changes'][0]['value']['messages'][0]['interactive'])) {
                    $this->handleList($arr);

                }
            }

//            $keys=$arr['entry'][0]['changes'][0]['value']['messages'][0];
//            $this->phone = $arr['entry'][0]['changes'][0]['value']['messages'][0]['from'] ?? 'no number';
//            $message = $arr['entry'][0]['changes'][0]['value']['messages'][0]['text']['body'];
        }
        elseif(array_key_exists('statuses',$arr['entry'][0]['changes'][0]['value']))
        {
            $this->handleStatus($arr);

        }



    return response('',200);
    }


    public function handleMsg($arr)
    {


        $fptr = fopen('myfile2.txt', 'w');
        //$status = $arr['entry'][0]['changes'][0]['value']['statuses']['status'];
        $message=$arr['entry'][0]['changes'][0]['value']['messages'][0]['text']['body'];
        $this->phone=$arr['entry'][0]['changes'][0]['value']['messages'][0]['from'] ?? 'no number';
        fwrite($fptr, $this->phone . ' ' . $message);
        fclose($fptr);
        $contact=$arr['entry'][0]['changes'][0]['value']['contacts'][0]['profile']['name'];
        if ($message == 'hie') {
            $this->sendMsgInteractive(array(
                'Restaurant Name',
                'Hie '.$contact.' .Welcome to Restaurant Name whatsapp chatbot. Where you can order food and have it delivered to your doorstep',
                'Get Started'),
                array(
                    ['id'=>'pizza','title'=>'Pizza'],
                    ['id'=>'burger','title'=>'Burgers'],
                    ['id'=>'drink','title'=>'Drinks']
                ));
        }
        elseif($message == 'bye') {
            $this->sendMsgList(array(
                'header'=>'Restaurant Name',
                'body'=>'Welcome '.$contact.' to Restaurant Name whatsapp chatbot. Where you can order food and have it delivered to your doorstep.',
                'footer'=>'Get Started',
                'button'=>'Food Menu'),
            array(
                [
                    'id'=>'pizza',
                    'title'=>'Pizza',
                    'description'=>'Meat and Vegan pizzas for you to devour'
                ],
                [
                    'id'=>'burger',
                    'title'=>'Burgers',
                    'description'=>'Mouth watering burgers'
                ],
                [
                    'id'=>'deserts',
                    'title'=>'Deserts',
                    'description'=>'Tasty treats and sweets'
                ],
                [
                    'id'=>'drink',
                    'title'=>'Drinks',
                    'description'=>'You thirsty huh?'
                ]));
            //$this->sendMsgText('Thank you for eating here. Have a nice day!!!!');
        }

    }

    public function handleStatus($arr)
    {
        $fptr = fopen('myfile4.txt', 'w');
        fwrite($fptr,$arr['entry'][0]['changes'][0]['value']['statuses'][0]['status'].' status '.implode(',',array_keys($arr['entry'][0]['changes'][0]['value']['statuses'][0])));
        fclose($fptr);
    }

    public function handleResponse($arr)
    {

//        $fptr = fopen('myfile3.txt', 'w');
//        fwrite($fptr,$arr['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['button_reply']['id'].' response '.implode(',',array_keys($arr['entry'][0]['changes'][0]['value']['messages'][0]['interactive'])));
//        fclose($fptr);
        $this->phone=$arr['entry'][0]['changes'][0]['value']['contacts'][0]['wa_id'];
        if($arr['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['button_reply']['id']=='pizza'){
            $this->sendMsgInteractive(array(
                'Pizza Menu',
                'Please select from our list of pizza types.',
                'Whats your poison?'),
                array(
                    ['id'=>'meat','title'=>'Meat'],
                    ['id'=>'vegi','title'=>'Vegetarian'],
                    ['id'=>'mixed','title'=>'Mixed']
                ));
        }
        elseif($arr['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['button_reply']['id']=='meat'){
            $this->sendMsgInteractive(array(
                'Meat Menu',
                'Please select from our list of pizza below.',
                'Wolf Bites'),
                array(
                    ['id'=>'chicken','title'=>'Chicken Peri'],
                    ['id'=>'beef','title'=>'Beef Strog'],
                    ['id'=>'pepperoni','title'=>'Pepperoni']
                ));
        }
        elseif($arr['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['button_reply']['id']=='vegi'){
            $this->sendMsgInteractive(array(
                'Vegi Menu',
                'Please select from our list of pizza below.',
                'Rabbit Bites'),
                array(
                    ['id'=>'mushroom','title'=>'Mushroom'],
                    ['id'=>'garlic','title'=>'Garlic Mayo'],
                    ['id'=>'peppermint','title'=>'Peppermint']
                ));
        }
        elseif($arr['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['button_reply']['id']=='mixed'){
            $this->sendMsgInteractive(array(
                'Mixed Menu',
                'Please select from our list of pizza below.',
                'Half Bites'),
                array(
                    ['id'=>'chicken_mushroom','title'=>'Chicken Mush'],
                    ['id'=>'four_seasons','title'=>'4 Seasons'],
                    ['id'=>'pizza_name','title'=>'Idk Pizza Name']
                ));
        }
        elseif($arr['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['button_reply']['id']=='burger'){
            $this->sendMsgInteractive(array(
                'Burger Menu',
                'Please select from our list of pizza below.',
                'Open wide!!!'),
                array(
                    ['id'=>'beef_burger','title'=>'Beef Burger'],
                    ['id'=>'chicken_burger','title'=>'Chicken Burger'],
                    ['id'=>'cheese_burger','title'=>'Cheese Burger']
                ));
        }
        elseif($arr['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['button_reply']['id']=='drink'){
            $this->sendMsgInteractive(array(
                'Drink Menu',
                'Please select a drink type.',
                'Thirsty?'),
                array(
                    ['id'=>'juice','title'=>'Juice'],
                    ['id'=>'fizzy_drink','title'=>'Fizzy Drink'],
                    ['id'=>'water','title'=>'Water']
                ));
        }

    }

    public function handleList($arr)
    {
        $this->phone=$arr['entry'][0]['changes'][0]['value']['contacts'][0]['wa_id'];
    if($arr['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['list_reply']['id']=='pizza'){

        $this->sendMsgList(array(
            'header'=>'Pizza Menu',
            'body'=>'Welcome to our pizza menu of meat and vegan options.',
            'footer'=>'Copyright 2022',
            'button'=>'Pizza Menu'),
            array(
                [
                    'id'=>'pizza_pepperoni',
                    'title'=>'Pepperoni Pizza',
                    'description'=>'Pepperoni and cheese pizza'
                ],
                [
                    'id'=>'pizza_pineapple',
                    'title'=>'Pineapple Pizza',
                    'description'=>'The wierd kid of the pizza family'
                ],
                [
                    'id'=>'pizza_mushroom',
                    'title'=>'Mushroom Pizza',
                    'description'=>'Someone actually orders this pizza?'
                ],
                [
                    'id'=>'pizza_chicken_mushroom',
                    'title'=>'Chicken Mushroom Pizza',
                    'description'=>'The best pizza on the planet, fight me if you disagree'
                ]));
        }
        elseif($arr['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['list_reply']['id']=='burger'){

            $this->sendMsgList(array(
                'header'=>'Burger Menu',
                'body'=>'Welcome to our burger menu of meat and vegan options.',
                'footer'=>'Copyright 2022',
                'button'=>'Burger Menu'),
                array(
                    [
                        'id'=>'burger_plain',
                        'title'=>'Beef Burger',
                        'description'=>'Ground Beef Burger'
                    ],
                    [
                        'id'=>'burger_cheese',
                        'title'=>'Cheese Burger',
                        'description'=>'Ground Beef and Cheese Burger'
                    ],
                    [
                        'id'=>'burger_chilli',
                        'title'=>'Beef Chilli Burger',
                        'description'=>'Its time to harass your taste buds'
                    ],
                    [
                        'id'=>'burger_vegan',
                        'title'=>'Vegan Burger',
                        'description'=>'For all the people who hate fun'
                    ],
                    [
                        'id'=>'burger_chicken',
                        'title'=>'Chicken Burger',
                        'description'=>'Why for the love of god why?'
                    ]));
        }
    }


    public function sendMsgText($textMsg)
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
                "to": "'.$this->phone.'",
                "type": "text",
                "text": {
                    "body": "'. $textMsg .'"
                }
            }';

        $request = new \GuzzleHttp\Psr7\Request('POST', 'https://graph.facebook.com/v13.0/'.$this->webhookId().'/messages', $headers, $body);
        $res = $client->sendAsync($request)->wait();
        echo $res->getBody();
    }

    public function sendMsgInteractive($text,$buttons)
    {
        $client = new Client();
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$this->webhookToken()
        ];
        $buttonJson='';
        foreach($buttons as $button) {
            $buttonJson .= '{
                          "type": "reply",
                          "reply": {
                            "id": "'.$button['id'].'",
                            "title": "'.$button['title'].'"
                          }
                        },';
        }
//        $fptr = fopen('myfile5.txt', 'w');
//        fwrite($fptr, $buttonJson);
//        fclose($fptr);
        $body = '{
                  "recipient_type": "individual",
                  "messaging_product": "whatsapp",
                  "to": "'.$this->phone.'",
                  "type": "interactive",
                  "interactive": {
                    "type": "button",
                    "header": {
                      "type": "text",
                      "text": "'.$text[0].'"
                    },
                    "body": {
                      "text": "'.$text[1].'"
                    },
                    "footer": {
                      "text": "'.$text[2].'"
                    },
                    "action": {
                      "buttons": [
                        '.$buttonJson.'
                      ]
                    }
                  }
                }';
        $request = new \GuzzleHttp\Psr7\Request('POST', 'https://graph.facebook.com/v14.0/'.$this->webhookId().'/messages', $headers, $body);
        $res = $client->sendAsync($request)->wait();
        echo $res->getBody();
    }

    public function sendMsgList($text,$list)
    {
        $client = new Client();
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$this->webhookToken()
        ];

        $listJson='';
        foreach($list as $item) {
            $listJson .= '{
                            "id": "'.$item['id'].'",
                            "title": "'.$item['title'].'",
                            "description": "'.$item['description'].'"
                          },';
        }

        $body = '{
      "recipient_type": "individual",
      "messaging_product": "whatsapp",
      "to": "'.$this->phone.'",
      "type": "interactive",
      "interactive": {
        "type": "list",
        "header": {
          "type": "text",
          "text": "'.$text['header'].'"
        },
        "body": {
          "text": "'.$text['body'].'"
        },
        "footer": {
          "text": "'.$text['footer'].'"
        },
        "action":{
          "button": "'.$text['button'].'",
          "sections":[
            {

              "rows": [
                '.$listJson.'
              ]
            }
          ]
      }
    }
  }';
        $request = new \GuzzleHttp\Psr7\Request('POST', 'https://graph.facebook.com/v14.0/'.$this->webhookId().'/messages', $headers, $body);
        $res = $client->sendAsync($request)->wait();
        echo $res->getBody();
    }
}
