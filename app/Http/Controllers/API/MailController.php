<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Telemetria;
use App\User;



class MailController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function MailSend(Request $request)
    {     

        $this->validate($request,[
            'nome' => 'required',
            'email' => 'required|email',
            'mensagem' => 'required',
        ]);

        try{

            $contato = [
                'nome' => $request->nome,
                'email' => $request->email,
                'mensagem' => $request->mensagem,  
            ];

            $contato = (object)$contato;

             Mail::send('api.mail', ['contato' => $contato], function ($m) use ($contato) {
                    $m->from($contato->email, 'Contato UPSTORE');
                    $m->to('contato@upstoreexpress.com')->subject('UPSTORE.COM!');
            });

            return response()->json([
                'success' => 'Email enviado com sucesso, aguarde nossa equipe entrar em contato!'
            ], 200);

        }catch(\Exception $e){

            \Log::alert($e);

            $telemetry = new Telemetria;
            $telemetry->user_id = 0;
            $telemetry->metodo = 'MailSend';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => 'Error'
            ], 500);
        }

    }

    public function RecuperarSenha(Request $request)
    {   

        $this->validate($request,[
            'email' => 'required|email',
            'link' => 'required'
        ]);

        try{


            $user = User::where('email',$request->email)
                            ->where('status','ATIVO')
                            ->where('tipo','CLIENTE')
                            ->first();

            if(!$user):
                return response()->json([
                    'error' => 'Email não encontrado!'
                ], 200);
            endif;

            $token = str_random(32);
            $user->remember_token = $token;
            $user->update();

            $user->link = $request->link.'?token='.$token.'&id='.$user->id;

             Mail::send('recuperar', ['user' => $user], function ($m) use ($user) {
                    $m->from($user->email, 'E-mail de recuperação de senha');
                    $m->to('contato@upstoreexpress.com')->subject('UPSTORE.COM!');
            });

            return response()->json([
                 'success' => 'Enviamos um link de recuperação de senha para o seu email!'
            ], 201);

         }catch(\Exception $e){

            $telemetry = new Telemetria;
            $telemetry->user_id = 0;
            $telemetry->metodo = 'MailSend';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => 'Error'
            ], 405);
        }

    }


}
