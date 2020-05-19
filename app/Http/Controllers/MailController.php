<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\User;
use App\Jobs\ConfirmationMailJob;
use Carbon\Carbon;

class MailController extends Controller
{
   /* public function basic_email($data){
        Mail::send(['text'=>'mail'], $data, function($message) {
            $message->to('abc@gmail.com', 'Tutorials Point')->subject
            ('Laravel Basic Testing Mail');
            $message->from('xyz@gmail.com','Virat Gandhi');
        });
        echo "Basic Email Sent. Check your inbox.";
    }*/
    public function html_email($data){

        // Mail::send('mail', ['data'=>$data], function($message) use ($data) {
        //     $message->to($data['email'], $data['name'])->subject($data['titulo']);
        //     $message->from('contato@upstore.com','UPSTORE');
        // });
    }
  /*  public function attachment_email($data){

        Mail::send('mail', $data, function($message) {
            $message->to('abc@gmail.com', 'Tutorials Point')->subject
            ('Laravel Testing Mail with Attachment');
            $message->attach('C:\laravel-master\laravel\public\uploads\image.png');
            $message->attach('C:\laravel-master\laravel\public\uploads\test.txt');
            $message->from('xyz@gmail.com','Virat Gandhi');
        });
        echo "Email Sent with attachment. Check your inbox.";
    }*/


    public function confirmation($user = null) {
        if(!$user) {
            $user = User::where('email', 'cassianogf2@hotmail.com')->first();
        }

        $now = Carbon::now();

        $job = (new ConfirmationMailJob($user->id))
            ->delay($now);

        dispatch($job);

        // if($user)
        return true;
        // else 
            // return response()->json(true);
    }
}
