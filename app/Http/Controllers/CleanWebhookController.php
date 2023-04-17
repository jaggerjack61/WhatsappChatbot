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


class CleanWebhookController extends Controller
{
    public $phone;
    public $company ='Virl Micro-Finance';


    public function webhookSetup(Request $request)
    {

        $mode=$request->hub_mode;
        $token=$request->hub_verify_token;
        $challenge=$request->hub_challenge;
        if($mode and $token){
            if(strlen($token)==13){
                return response ($challenge, 200);
            }

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


        $arr = $request->all();




        if(array_key_exists('messages',$arr['entry'][0]['changes'][0]['value'])){
            $this->phone=$arr['entry'][0]['changes'][0]['value']['messages'][0]['from'] ?? 'no number';
            $client=\App\Models\Client::where('phone_no',$this->phone)->first();
            if(!$client){
                \App\Models\Client::create([
                    'phone_no' => $this->phone
                ]);

            }

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
                $this->handleImage($arr);

            }

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
        WhatsappMessage::create([
            'client_id' => $client->id,
            'message' => 'image:'.$mediaId,

        ]);



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
                'message_status'=>'loan_amount',
                'status' =>'pending'
            ]);
            $client->save();
            $this->sendMsgText('Please enter the loan amount as a number eg 2500000.');
        }



    }


    public function handleMsg($arr)
    {





        $message=$arr['entry'][0]['changes'][0]['value']['messages'][0]['text']['body'];


        $client=\App\Models\Client::where('phone_no',$this->phone)->first();
        WhatsappMessage::create([
            'client_id' => $client->id,
            'message' => $message,

        ]);

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
            elseif($client->status=='registered' or $client->status=='guest'){

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
                    'Hie '.$name.' .Welcome to '.$this->company.' whatsapp chatbot.Unfortunately it seems your registration has been denied.Call  0716808509 for more information.',
                    'Getting Started'),
                    array(
                        ['id'=>'help','title'=>'Get Help'],
                        ['id'=>'faq','title'=>'FAQ']
                    ));
            }



        }
        elseif($client->message_status=='register_name'){
            $client->update([
                'name'=>$message,
                'message_status'=>'register_address'
            ]);
            $client->save();
            $this->sendMsgText('Please enter your full address');
        }
        elseif($client->message_status=='register_address'){
            $client->update([
                'address'=>$message,
                'message_status'=>'register_ec'
            ]);
            $client->save();
            $this->sendMsgText('Please enter your EC number');
        }
        elseif($client->message_status=='register_ec'){
            $client->update([
                'EC'=>$message,
                'message_status'=>'register_bank'
            ]);
            $client->save();
            $this->sendMsgText('Please enter the name of your bank.');
        }
        elseif($client->message_status=='register_bank'){
            $client->update([
                'bank'=>$message,
                'message_status'=>'register_account'
            ]);
            $client->save();
            $this->sendMsgText('Please enter your account number.');
        }
        elseif($client->message_status=='register_account'){
            $client->update([
                'account_number'=>$message,
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
            if(is_numeric($message)){
                $client->message_status='loan_due';
                $client->save();

                LoanHistory::create([
                    'client_id'=>$client->id,
                    'currency'=>$client->rough,
                    'amount'=>$message
                ]);
                $this->sendMsgList(array(
                    'header'=>'Loan Duration',
                    'body'=>'Click the list below and select the period of time you need to pay back the loan.',
                    'footer'=>$this->company,
                    'button'=>'See Time Periods'),
                    array(
                        [
                            'id'=>'one_month',
                            'title'=>'1 Month',
                            'description'=>'Pay back loan after a month.'
                        ],
                        [
                            'id'=>'two_months',
                            'title'=>'2 Months',
                            'description'=>'Pay back loan after 2 months.'
                        ],
                        [
                            'id'=>'three_months',
                            'title'=>'3 Months',
                            'description'=>'Pay back loan after 3 months.'
                        ],
                        [
                            'id'=>'four_months',
                            'title'=>'4 Months',
                            'description'=>'Pay back loan after 4 months.'
                        ],
                        [
                            'id'=>'five_months',
                            'title'=>'5 Months',
                            'description'=>'Pay back loan after 5 months.'
                        ],
                        [
                            'id'=>'six_months',
                            'title'=>'6 Months',
                            'description'=>'Pay back loan after 6 months.'
                        ]));


            }


        }

    }

    public function handleStatus($arr)
    {
        return true;
    }

    public function handleResponse($arr)
    {


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
                    $client->save();
                    $this->sendMsgInteractive(array(
                        'Loan Type',
                        'Select the type of loan you want to apply for.',
                        $this->company),
                        array(
                            ['id'=>'ssb_loan','title'=>'SSB Loan'],
                            ['id'=>'sme_loan','title'=>'SME Loan'],

                        ));
                }
                elseif($loan->status=='defaulted'){
                    $this->sendMsgText('This account can no longer apply for loans as you have previously defaulted on a loan.');
                }
                elseif($loan->status=='pending'){
                    $this->sendMsgText('You already have a loan that is currently under review. For '.$loan->amount.$loan->currency);
                }
                elseif($loan->status=='approved'){
                    $this->sendMsgText('You already have a loan for '.$loan->amount.$loan->currency.' that you are still in the process of paying back');
                }
            }
            else{

                $this->sendMsgInteractive(array(
                    'Loan Type',
                    'Select the type of loan you want to apply for.',
                    $this->company),
                    array(
                        ['id'=>'ssb_loan','title'=>'SSB Loan'],
                        ['id'=>'sme_loan','title'=>'SME Loan'],

                    ));
            }

        }
        elseif($arr['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['button_reply']['id']=='faq'){
            $this->sendMsgList(array(
                'header'=>'Frequently Asked Questions',
                'body'=>'Click the list below to see the top 7 most asked questions and their answers..',
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
                        'description'=>'How long does it take for a loan to be approved?'
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
                    ],
                    [
                        'id'=>'q6',
                        'title'=>'Question 6',
                        'description'=>'What can i apply for other type of loans?'
                    ],
                    [
                        'id'=>'q7',
                        'title'=>'Question 7',
                        'description'=>'Can i get more information on your solar systems?'
                    ]));

        }
        elseif($arr['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['button_reply']['id']=='help'){
            $this->sendMsgText('Contact us on wa.me/263716808509 for more info.');
        }
        elseif($arr['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['button_reply']['id']=='sme_loan'){
            $this->sendMsgText('Visit https://virlmicrofinance.co.zw/application-form-2/ to apply for SME Loans.');
        }
        elseif($arr['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['button_reply']['id']=='ssb_loan'){
            $this->sendMsgInteractive(array(
                'Loan Currency',
                'Select the currency of the loan.',
                $this->company),
                array(
                    ['id'=>'rtgs_loan','title'=>'RTGS'],
                    ['id'=>'usd_loan','title'=>'USD'],


                ));
        }
        elseif($arr['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['button_reply']['id']=='usd_loan'){
            $client=\App\Models\Client::where('phone_no',$this->phone)->first();
            if($client->status =='registered'){
                $client->message_status='loan_amount';
                $client->rough='USD';
                $client->save();
                $this->sendMsgText('Please enter the loan amount as a number only eg 250000.');

            }
            elseif($client->status =='guest'){
                $client->message_status='register_name';
                $client->rough='USD';
                $client->save();
                $this->sendMsgText('Please enter your full name.');
            }
        }
        elseif($arr['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['button_reply']['id']=='rtgs_loan'){
            $client=\App\Models\Client::where('phone_no',$this->phone)->first();
            if($client->status =='registered'){
                $client->message_status='loan_amount';
                $client->rough='RTGS';
                $client->save();
                $this->sendMsgText('Please enter the loan amount as a number only eg 250000.');

            }
            elseif($client->status =='guest'){
                $client->message_status='register_name';
                $client->rough='RTGS';
                $client->save();
                $this->sendMsgText('Please enter your full name.');
            }
        }

        elseif($arr['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['button_reply']['id']=='cancel_loan'){
            $client=\App\Models\Client::where('phone_no',$this->phone)->first();
            $client->message_status='none';
            $client->save();
            $res = new WebhookController();
            $res->save();
            $this->sendMsgText('Your loan application has been cancelled.');
        }


    }

    public function handleList($arr)
    {
        $this->phone=$arr['entry'][0]['changes'][0]['value']['contacts'][0]['wa_id'];
        if($arr['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['list_reply']['id']=='q1'){

            $this->sendMsgText('You can borrow a maximum amount of $5000 and minimum amount of $100.');
        }
        elseif($arr['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['list_reply']['id']=='q2'){

            $this->sendMsgText('*Harare* 67 Kwame Nkrumah, 3rd Floor, Takura Building, Harare Zimbabwe.');
            $this->sendMsgText('*Bulawayo* Masiye Business Suite Suite No 214 Fort Street/9th Avenue');
            $this->sendMsgText('*Nyanga* 4 Shonalanga Drive, Rochdale, Nyanga');
            $this->sendMsgText('*Mutasa* Number 221 Hauna Growth Point');
            $this->sendMsgText('*Rusape* 2455 Chimurenga Street, Rusape');
            $this->sendMsgText('*Bikita* Stand No 626 Bikita Rural District Council P. O. Box 431');
            $this->sendMsgText('*Headlands* Plot 1, Headlands Business Centre, Zimpost Headlands');
        }
        elseif($arr['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['list_reply']['id']=='q3'){

            $this->sendMsgText('It takes a maximum of up to 7 days for your loan to be approved.');
        }

        elseif($arr['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['list_reply']['id']=='q4'){

            $this->sendMsgText('6 months is the longest loan repayment period.');
        }
        elseif($arr['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['list_reply']['id']=='q5'){

            $this->sendMsgText('You can call us on 0716808509');
        }
        elseif($arr['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['list_reply']['id']=='q6'){

            $this->sendMsgText('You can apply for SME loans and USD loans at https://virlmicrofinance.co.zw/application-form-2/');
        }
        elseif($arr['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['list_reply']['id']=='q7'){

            $this->sendMsgText('For more information on our solar systems please follow this link:wa.me/263716808334');
        }
        elseif($arr['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['list_reply']['id']=='one_month'){

            $client=\App\Models\Client::where('phone_no',$this->phone)->first();
            if($client->message_status=='loan_due'){
                $loan=LoanHistory::where('client_id',$client->id)->latest()->first();
                $loan->due_date='1';
                $loan->status = 'pending';
                $loan->save();
                $client->message_status='none';
                $client->save();
                $this->sendMsgText('Your loan is pending approval.');
            }
            else{
                $this->sendMsgText('You can not change the date after the loan has been submitted');
            }

        }
        elseif($arr['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['list_reply']['id']=='two_months'){

            $client=\App\Models\Client::where('phone_no',$this->phone)->first();
            if($client->message_status=='loan_due'){
                $loan=LoanHistory::where('client_id',$client->id)->latest()->first();
                $loan->due_date='2';
                $loan->status = 'pending';
                $loan->save();
                $client->message_status='none';
                $client->save();
                $this->sendMsgText('Your loan is pending approval.');
            }
            else{
                $this->sendMsgText('You can not change the date after the loan has been submitted');
            }

        }
        elseif($arr['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['list_reply']['id']=='three_months'){

            $client=\App\Models\Client::where('phone_no',$this->phone)->first();
            if($client->message_status=='loan_due'){
                $loan=LoanHistory::where('client_id',$client->id)->latest()->first();
                $loan->due_date='3';
                $loan->status = 'pending';
                $loan->save();
                $res = new WebhookController();
                $res->save();
                $client->message_status='none';
                $client->save();
                $this->sendMsgText('Your loan is pending approval.');
            }
            else{
                $this->sendMsgText('You can not change the date after the loan has been submitted');
            }

        }
        elseif($arr['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['list_reply']['id']=='four_months'){

            $client=\App\Models\Client::where('phone_no',$this->phone)->first();
            if($client->message_status=='loan_due'){
                $loan=LoanHistory::where('client_id',$client->id)->latest()->first();
                $loan->due_date='4';
                $loan->status = 'pending';
                $loan->save();
                $res = new WebhookController();
                $res->save();
                $client->message_status='none';
                $client->save();
                $this->sendMsgText('Your loan is pending approval.');
            }
            else{
                $this->sendMsgText('You can not change the date after the loan has been submitted');
            }

        }
        elseif($arr['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['list_reply']['id']=='five_months'){

            $client=\App\Models\Client::where('phone_no',$this->phone)->first();
            if($client->message_status=='loan_due'){
                $loan=LoanHistory::where('client_id',$client->id)->latest()->first();
                $loan->due_date='5';
                $loan->status = 'pending';
                $loan->save();
                $res = new WebhookController();
                $res->save();
                $client->message_status='none';
                $client->save();
                $this->sendMsgText('Your loan is pending approval.');
            }
            else{
                $this->sendMsgText('You can not change the date after the loan has been submitted');
            }

        }
        elseif($arr['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['list_reply']['id']=='six_months'){

            $client=\App\Models\Client::where('phone_no',$this->phone)->first();
            if($client->message_status=='loan_due'){
                $loan=LoanHistory::where('client_id',$client->id)->latest()->first();
                $loan->due_date='6';
                $loan->status = 'pending';
                $loan->save();
                $client->message_status='none';
                $client->save();
                $this->sendMsgText('Your loan is pending approval.');
            }
            else{
                $this->sendMsgText('You can not change the date after the loan has been submitted');
            }

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

    public function sendMsgTemplate($template,$data)
    {

        $client = new Client();
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$this->webhookToken()
        ];
        $body = '{
          "messaging_product": "whatsapp",
          "to": "'.$this->phone.'",
          "type": "template",
          "template": {
            "name": "'.$template.'",
            "language": {
              "code": "en_GB"
            }
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
        }';

        $request = new \GuzzleHttp\Psr7\Request('POST', 'https://graph.facebook.com/v13.0/'.$this->webhookId().'/messages', $headers, $body);
        $res = $client->sendAsync($request)->wait();
    }
}
