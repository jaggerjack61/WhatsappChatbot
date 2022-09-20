<?php

namespace App\Http\Controllers;


use App\Models\LoanHistory;
use App\Models\WhatsappMessage;
use App\Models\WhatsappSetting;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class WebhookController extends Controller
{
    public $phone;
    public $company ='Company Name';


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

        $fptr = fopen('myfile.json', 'w');
        fwrite($fptr, json_encode($request->all()));
        fclose($fptr);
        $arr = $request->all();




        if(array_key_exists('messages',$arr['entry'][0]['changes'][0]['value'])){
            $this->phone=$arr['entry'][0]['changes'][0]['value']['messages'][0]['from'] ?? 'no number';

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
            elseif(array_key_exists('image',$arr['entry'][0]['changes'][0]['value']['messages'][0])) {
//                $fptr1 = fopen('myfilex.txt', 'w');
//                fwrite($fptr1, $arr['entry'][0]['changes'][0]['value']['messages'][0]['image']['id'].json_encode($arr['entry'][0]['changes'][0]['value']['messages'][0]['image']));
//                fclose($fptr1);

                $this->handleImage($arr);

                //return response('',200);
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



    public function handleImage($arr)
    {

        $mediaId=$arr['entry'][0]['changes'][0]['value']['messages'][0]['image']['id'];
        $url=$this->retrieveMediaUrl($mediaId);

        $client=\App\Models\Client::where('phone_no',$this->phone)->first();
        if($client){
            $saveMsg=WhatsappMessage::create([
                'client_id' => $client->id,
                'message' => 'image:'.$mediaId,

            ]);
        }
        else{
            $client=\App\Models\Client::create([
                'phone_no' => $this->phone
            ]);
        }

        if($client->message_status=='none'){

        $this->sendMsgText('Thanks for the pic ,but please select from our list of menu items');
        }
        elseif($client->message_status=='register_name'){

            $this->sendMsgText('Please write your name in full');
        }
        elseif($client->message_status=='register_id'){
            //save image of id here
            $this->downloadMedia($url,'id');

            $client->update([
                'message_status'=>'register_payslip'
            ]);
            $client->save();
            $this->sendMsgText('Please send a picture of your payslip');
        }
        elseif($client->message_status=='register_payslip'){
            //save image of id here
            $this->downloadMedia($url,'payslip');

            $client->update([
                'message_status'=>'none',
                'status' =>'pending'
            ]);
            $client->save();
            $this->sendMsgText('Your registration is pending approval');
        }



    }


    public function handleMsg($arr)
    {


        $fptr = fopen('myfile2.txt', 'w');
        //$status = $arr['entry'][0]['changes'][0]['value']['statuses']['status'];
        $message=$arr['entry'][0]['changes'][0]['value']['messages'][0]['text']['body'];
//        if($arr['entry'][0]['changes'][0]['value']['messages'][0]['type']=='image'){

//        }
//        if($arr['entry'][0]['changes'][0]['value']['messages'][0]['type'] == 'image'){
//
//            $this->retrieveMediaUrl($arr['entry'][0]['changes'][0]['value']['messages'][0]['image']['id']);
//        }

        $client=\App\Models\Client::where('phone_no',$this->phone)->first();
        if($client){
            $saveMsg=WhatsappMessage::create([
                'client_id' => $client->id,
                'message' => $message,

            ]);
        }
        else{
            $client=\App\Models\Client::create([
                'phone_no' => $this->phone
            ]);
        }

        fwrite($fptr, $this->phone . ' ' . $message);
        fclose($fptr);
        $contact=$arr['entry'][0]['changes'][0]['value']['contacts'][0]['profile']['name'];
        if($client->message_status=='none'){
            $name=$client->name??$contact;
            if($client->status=='pending'){

                $this->sendMsgInteractive(array(
                    $this->company,
                    'Hie '.$name.' .Welcome to '.$this->company.' whatsapp chatbot.Your registration is pending review.',
                    'Getting Started'),
                    array(

                        ['id'=>'help','title'=>'Get Help'],
                        ['id'=>'faq','title'=>'FAQ']
                    ));
            }
            elseif($client->status=='registered'){

                $this->sendMsgInteractive(array(
                    $this->company,
                    'Hie '.$name.' .Welcome to '.$this->company.' whatsapp chatbot.',
                    'Getting Started'),
                    array(
                        ['id'=>'loan','title'=>'Apply for loan'],
                        ['id'=>'help','title'=>'Get Help'],
                        ['id'=>'faq','title'=>'FAQ']
                    ));
            }
            elseif($client->status=='denied'){

                $this->sendMsgInteractive(array(
                    $this->company,
                    'Hie '.$name.' .Welcome to '.$this->company.' whatsapp chatbot.Unfortunately it seems your registration has been denied.Call for more information.',
                    'Getting Started'),
                    array(
                        ['id'=>'register','title'=>'Re-register'],
                        ['id'=>'help','title'=>'Get Help'],
                        ['id'=>'faq','title'=>'FAQ']
                    ));
            }
            else{
                $this->sendMsgInteractive(array(
                    $this->company,
                    'Hie '.$name.' .Welcome to '.$this->company.' whatsapp chatbot.',
                    'Get Started'),
                    array(
                        ['id'=>'register','title'=>'Register'],
                        ['id'=>'help','title'=>'Get Help'],
                        ['id'=>'faq','title'=>'FAQ']
                    ));
            }


        }
        elseif($client->message_status=='register_name'){
            $client->update([
                'name'=>$message,
                'message_status'=>'register_id'
            ]);
            $client->save();
            $this->sendMsgText('Please send a picture of your national id or drivers licence');
        }
        elseif($client->message_status=='register_id'){
            //save image of id here

            $client->update([
                'message_status'=>'register_payslip'
            ]);
            $client->save();
            $this->sendMsgText('Please send a picture of your national id or drivers licence');
        }

        elseif($client->message_status=='loan_amount'){
            //save image of id here

            $client->message_status='loan_due';
            $client->save();
            $loan=LoanHistory::create([
                'client_id'=>$client->id,
                'amount'=>$message
            ]);
            $loan->save();
            $this->sendMsgText('Please enter the amount of time you will need to pay back the loan in months.');
        }
        elseif($client->message_status=='loan_due'){
            //save image of id here

            $client->update([
                'message_status'=>'none'
            ]);
            $client->save();
            $loan=LoanHistory::where('client_id',$client->id)->latest()->first();
            $loan->due_date=$message;
            $loan->status='pending';
            $loan->save();
            $this->sendMsgText('Your loan application is pending review');
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
            $this->sendMsgText('Please enter your full name.');
            $client=\App\Models\Client::where('phone_no',$this->phone)->first();
            $client->update([
                'message_status' =>'register_name'
            ]);
            $client->save();
        }
        elseif($arr['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['button_reply']['id']=='loan'){

            $client=\App\Models\Client::where('phone_no',$this->phone)->first();
            $loan=LoanHistory::where('client_id',$client->id)->latest()->first();
            if($loan){
                if($loan->status=='paid'){
                    $client->message_status='loan_amount';
                    $client->save();
                    $this->sendMsgText('Enter the full amount in United States Dollars.');
                }
                elseif($loan->status=='pending'){
                    $this->sendMsgText('You already have a loan that is currently under review. For '.$loan->amount.'USD');
                }
                elseif($loan->status=='approved'){
                    $this->sendMsgText('You already have a loan for '.$loan->amount.'USD that you are still in the process of paying back');
                }
            }
            else{
                $client->message_status='loan_amount';
                $client->save();
                $this->sendMsgText('Enter the full amount in United States Dollars.');
            }

        }
        elseif($arr['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['button_reply']['id']=='faq'){
            $this->sendMsgList(array(
                'header'=>'Frequently Asked Questions',
                'body'=>'Click the list below to see the top 5 most asked questions and their answers..',
                'footer'=>$this->company,
                'button'=>'See Questions'),
                array(
                    [
                        'id'=>'q1',
                        'title'=>'Question 1',
                        'description'=>'Whats the most i can borrow?'
                    ],
                    [
                        'id'=>'q2',
                        'title'=>'Question 2',
                        'description'=>'Where can i find your offices?'
                    ],
                    [
                        'id'=>'q3',
                        'title'=>'Question 3',
                        'description'=>'Can i pay back the loan on my phone on my phone?'
                    ],
                    [
                        'id'=>'q4',
                        'title'=>'Question 4',
                        'description'=>'Whats the longest i can take to pay back a loan?'
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


    }

    public function handleList($arr)
    {
        $this->phone=$arr['entry'][0]['changes'][0]['value']['contacts'][0]['wa_id'];
        if($arr['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['list_reply']['id']=='q1'){

            $this->sendMsgText('That will depend on x and y but generally speaking you can borrow z amount.');
            }
        elseif($arr['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['list_reply']['id']=='q2'){

            $this->sendMsgText('You can find our offices at corner x and y street.');
        }
        elseif($arr['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['list_reply']['id']=='q3'){

            $this->sendMsgText('Yes we are integrated with paynow allowing you to make remote payments.');
        }
        elseif($arr['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['list_reply']['id']=='q4'){

            $this->sendMsgText('X is the longest loan repayment period we offer.');
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

    public function retrieveMediaUrl($id)
    {


        $client = new Client();
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$this->webhookToken()
        ];

        $request = new \GuzzleHttp\Psr7\Request('GET', 'https://graph.facebook.com/v14.0/'.$id, $headers,'');
        $res = $client->sendAsync($request)->wait();
        $mediaArray=json_decode($res->getBody(),true);
        return $mediaArray['url'];


    }





    public function downloadMedia($url,$name)
    {
        $fptr = fopen('myfilex.txt', 'w');
        fwrite($fptr,$url);
        fclose($fptr);

        $client = new Client();
        if(!(file_exists('clients/'.$this->phone.'/'))){
            mkdir('clients/'.$this->phone,0755, true);
        }

        $resource = fopen('clients/'.$this->phone.'/'.$name.'.jpg', 'w');

        $response = $client->request('GET', $url, [
            'headers' => [
                'Authorization' => 'Bearer '.$this->webhookToken(),
                'Cache-Control' => 'no-cache',
                'Content-Type' => 'application/jpeg'
            ],
            'sink' => $resource,
        ]);
        fclose($resource);

    }
}
