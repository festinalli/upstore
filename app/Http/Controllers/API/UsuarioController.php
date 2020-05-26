<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\ApiController;
use Illuminate\Support\Facades\Hash;
use App\Telemetria;
use App\Telefone;
use App\Notificacao;
use App\User;

use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

use Auth;

class UsuarioController extends Controller
{
    private $user;
    private $token;
    public function __construct(Request $request)
    {
        if($request->header('token')){
            $this->token = $request->header('token');
            $apiController = new ApiController($request);
            $this->user = Auth::id();
        }
    }


    public function confirmarEmail(Request $request) {
        $user = Auth::user();

        if($user->confirmed) {
            return view('auth.confirm-success');
            
            return "O e-mail " . $user->email . "já foi confirmado";
        } else {
            $user->confirmed = 1;
            $user->save();

            return view('auth.confirm-success');
        }
    }

    public function alterarSenha(Request $request)
    {
        $this->validate($request,[
            'senha_atual' => 'required',
            'nova_senha' => 'required'
        ]);

        try{
            $apiController = new ApiController($request);
            $user = Auth::user();
            if (Hash::check($request->senha_atual, $user->password)) {
                $user->password = app('hash')->make($request->nova_senha);
                $user->update();

                return response()->json([
                    'user' => $user
                ], 200);
            }

            return response()->json([
                'error' => 'Senha atual inválida'
            ], 405);

        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = Auth::id();
            $telemetry->metodo = 'alterarSenha';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function alterarEmail(Request $request)
    {
        $this->validate($request,[
            'email' => 'required'
        ]);

        try{

            $user = User::where('email',$request->email)->first();
            
            if($user){
                return response()->json([
                    'error' => 'Email já cadastrado.'
                ], 405);
            }
            $apiController = new ApiController($request);
            $user = Auth::user();
            $user->email = $request->email;
            $user->update();

            return response()->json([
                'user' => $user
            ], 200);

        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = Auth::id();
            $telemetry->metodo = 'alterarEmail';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function lerTudo() {
        $notificacoes = Notificacao::where('user_id', Auth::id())->where('lido', 0)->get();

        foreach($notificacoes as $notificacao){ 
            $notificacao->lido = 1;
            $notificacao->update();
        }

        return response()->json(['status' => true], 200);
    }

    public function lerNotificacao($id){
        try{
            $notificacao = Notificacao::findOrFail($id);

            $notificacao->lido = 1;
            $notificacao->update();

            return response()->json('success', 200);
        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = Auth::id();
            $telemetry->metodo = 'lerNotificacao';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function createTelefone(Request $request)
    {
        $this->validate($request,[
            'numero' => 'required|string'
        ]);

        try{

            $telefone = new Telefone;
            $apiController = new ApiController($request);
            $telefone->user_id = Auth::id();
            $telefone->numero = $request->numero;
            $telefone->save();

            return response()->json([
                'telefone' => $telefone
            ], 200);

        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = Auth::id();
            $telemetry->metodo = 'createTelefone';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function updateTelefone(Request $request)
    {
        $this->validate($request,[
            'telefone' => 'required'
        ]);

        try{

            $user = User::where('telefone',$request->telefone)->first();

            if($user){
                return response()->json([
                    'error' => 'telefone já cadastrado.'
                ], 405);
            }
            $apiController = new ApiController($request);
            $user = Auth::user();
            $user->telefone = $request->telefone;
            $user->update();

            return response()->json([
                'user' => $user
            ], 200);

        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = Auth::id();
            $telemetry->metodo = 'UsuarioController@updateTelefone';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function deleteTelefone(Request $request,$telefone_id)
    {
        try{
            $telefone = Telefone::findOrFail($telefone_id);
            $apiController = new ApiController($request);

            if($telefone->user_id != Auth::id()){
                return response()->json([
                    'error' => 'Telefone inválido'
                ], 405);
            }

            $telefone->delete();

            return response()->json([
                'telefone' => 'Telefone deletado com sucesso'
            ], 200);

        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = Auth::id();
            $telemetry->metodo = 'deleteTelefone';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function telefones(Request $request)
    {
        try{
            $apiController = new ApiController($request);
            return response()->json([
                'telefones' => Auth::user()->telefones
            ], 200);

        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = Auth::id();
            $telemetry->metodo = 'deleteTelefone';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function verificaEmailLogin(Request $request)
    {
        try{
            $user = User::where('email',$request->email)->where('tipo','CLIENTE')->first();

            if(!$user){
                return response()->json([
                    'error' => 'Email não cadastrado'
                ], 405);
            }

            if($user->status == 'INATIVO'){
                return response()->json([
                    'error' => 'Conta inativa'
                ], 405);
            }

            return response()->json([
                'msg' => 'Email aprovado para login'
            ], 200);
        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = Auth::id();
            $telemetry->metodo = 'verificaEmailLogin';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function verificaEmail(Request $request)
    {
        try{

            if(User::where('email',$request->email)->first()){
                return response()->json([
                    'email' => 'Já existe um usuário com esse email'
                ], 401);
            }

            return response()->json([
                'email' => 'Email disponí­vel'
            ], 200);

        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = Auth::id();
            $telemetry->metodo = 'verificaEmail';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function verificaCpf(Request $request)
    {
        try{
            if(User::where('cpf',$request->cpf)->first()){
                return response()->json([
                    'cpf' => 'Já existe um usuário com esse cpf'
                ], 401);
            }

            return response()->json([
                'cpf' => 'Cpf disponível'
            ], 200);

        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = Auth::id();
            $telemetry->metodo = 'verificaCpf';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function listarNotificacoes(){
        try{
            $notificacoes = Notificacao::where('user_id', Auth::id())->where('lido',0)->get();

            return response()->json($notificacoes,200);
        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = Auth::id();
            $telemetry->metodo = 'UsuarioController@listarNotificacoes';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function visualizarNotificacao(Request $request){
        try{
            $notificacao = Notificacao::findOrFail($request->notificacao_id);
            $notificacao->lido = 1;
            $notificacao->update();
            $rota = url('/').'/'.$notificacao->link;

            return response()->json($rota,200);
        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = Auth::id();
            $telemetry->metodo = 'UsuarioController@visualizarNotificacao';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function recuperarSenha(Request $request)
    {   

        $this->validate($request,[
            'password' => 'required',
            'password_confirm' => 'required',
            'token' => 'required',
            'user_id' => 'required',
        ]);

        try{


            if($request->password != $request->password_confirm):
                 return response()->json([
                    'error' => 'Senhas não correspondem!'
                ], 405);
            endif;

            $user = User::where('id',$request->user_id)
                            ->where('remember_token',$request->token)
                            ->first();

            if( !$user ):
                  return response()->json([
                    'error' => 'Acesso negado!'
                ], 401);
            endif;

            $user->password = Hash::make($request->password);
            $user->remember_token = "";
            $user->update();

            return response()->json([
                    'success' => 'Sua senha foi alterada com sucesso!'
            ], 201);


        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = Auth::id();
            $telemetry->metodo = 'UsuarioController@visualizarNotificacao';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }

    }
}
