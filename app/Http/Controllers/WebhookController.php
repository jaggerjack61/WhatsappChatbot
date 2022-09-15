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
        if ($message) {
            $this->sendMsgInteractive(array(
                'Insurance Company Name',
                'Hie '.$contact.' .Welcome to Insurance Company whatsapp chatbot.',
                'Get Started'),
                array(
                    ['id'=>'register','title'=>'Register'],
                    ['id'=>'help','title'=>'Get Help'],
                    ['id'=>'faq','title'=>'FAQ']
                ));
        }
//        elseif($message == 'bye') {
//            $this->sendMsgList(array(
//                'header'=>'Restaurant Name',
//                'body'=>'Welcome '.$contact.' to Restaurant Name whatsapp chatbot. Where you can order food and have it delivered to your doorstep.',
//                'footer'=>'Get Started',
//                'button'=>'Food Menu'),
//            array(
//                [
//                    'id'=>'pizza',
//                    'title'=>'Pizza',
//                    'description'=>'Meat and Vegan pizzas for you to devour'
//                ],
//                [
//                    'id'=>'burger',
//                    'title'=>'Burgers',
//                    'description'=>'Mouth watering burgers'
//                ],
//                [
//                    'id'=>'deserts',
//                    'title'=>'Deserts',
//                    'description'=>'Tasty treats and sweets'
//                ],
//                [
//                    'id'=>'drink',
//                    'title'=>'Drinks',
//                    'description'=>'You thirsty huh?'
//                ]));
//            //$this->sendMsgText('Thank you for eating here. Have a nice day!!!!');
//        }

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
        if($arr['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['button_reply']['id']=='register'){
            $this->sendMsgInteractive(array(
                'Register Account',
                'Please select insurance type.',
                'Insurance Company Name'),
                array(
                    ['id'=>'vehicle','title'=>'Vehicle'],
                    ['id'=>'life','title'=>'Life'],
                    ['id'=>'funeral','title'=>'Funeral']
                ));
        }
        elseif($arr['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['button_reply']['id']=='vehicle'){
            $this->sendMsgInteractive(array(
                'Vehicle Menu',
                'Please select from our list of vehicle classes.',
                'Insurance Company Name'),
                array(
                    ['id'=>'class1','title'=>'Class 1'],
                    ['id'=>'class2','title'=>'Class 2'],
                    ['id'=>'class3','title'=>'Class 3']
                ));
        }
        elseif($arr['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['button_reply']['id']=='faq'){
            $this->sendMsgList(array(
                'header'=>'Frequently Asked Questions',
                'body'=>'Click the list below to see the top 5 most asked questions and their answers..',
                'footer'=>'Insurance Company Name',
                'button'=>'See Questions'),
                array(
                    [
                        'id'=>'q1',
                        'title'=>'Question 1',
                        'description'=>'How much does insurance cost?'
                    ],
                    [
                        'id'=>'q2',
                        'title'=>'Question 2',
                        'description'=>'Where can i find your offices?'
                    ],
                    [
                        'id'=>'q3',
                        'title'=>'Question 3',
                        'description'=>'Can i pay on my phone?'
                    ],
                    [
                        'id'=>'q4',
                        'title'=>'Question 4',
                        'description'=>'What types of insurance do you offer?'
                    ],
                    [
                        'id'=>'q5',
                        'title'=>'Question 5',
                        'description'=>'What are your contact details?'
                    ]));

        }
        elseif($arr['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['button_reply']['id']=='help'){
            $this->sendMsgText('Ndoziva kuti urikuda kubatsirwa chitora number idzi 077123456789 tikupe detail rese.');
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
        elseif($arr['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['button_reply']['id']=='life'){
            $this->sendMsgInteractive(array(
                'Life Insurance',
                'Please select a package.',
                'Insurance Company Name'),
                array(
                    ['id'=>'basic_life','title'=>'Basic'],
                    ['id'=>'classic_life','title'=>'Classic'],
                    ['id'=>'pro_life','title'=>'Life Pro Max']
                ));
        }
        elseif($arr['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['button_reply']['id']=='funeral'){
            $this->sendMsgInteractive(array(
                'Funeral Cover',
                'Please select a package.',
                'Insurance Company Name'),
                array(
                    ['id'=>'cheap','title'=>'Cheap'],
                    ['id'=>'less_cheap','title'=>'Less Cheap'],
                    ['id'=>'zvirinani','title'=>'Zvirinani']
                ));
        }

    }

    public function handleList($arr)
    {
        $this->phone=$arr['entry'][0]['changes'][0]['value']['contacts'][0]['wa_id'];
        if($arr['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['list_reply']['id']=='q1'){

            $this->sendMsgText('Insurance costs varies depending with your requirements.Life insurance has 3 packages namely a, b and c which cost x,y and z respectively. Funeral cover offers basic and pro which cost expensive and even more expensive dollars per month');
            }
        elseif($arr['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['list_reply']['id']=='q2'){

            $this->sendMsgText('You can find our offices at corner x and y street.');
        }
        elseif($arr['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['list_reply']['id']=='q3'){

            $this->sendMsgText('Yes we are integrated with paynow allowing you to make remote payments.');
        }
        elseif($arr['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['list_reply']['id']=='q4'){

            $this->sendMsgText('We offer Life Insurance, Medical Insurance, Funeral Cover and Car Insurance.');
        }
        elseif($arr['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['list_reply']['id']=='q5'){

            $this->sendMsgText('You can call us on our toll free number at 077123456789 or contact us at email@companyname.com');
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
