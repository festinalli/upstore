<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\ApiController;
use App\Telemetria;
use App\Endereco;
use App\User;

use Auth;

class EnderecoController extends Controller
{
    private $user;
    private $token;
    public function __construct(Request $request)
    {
        if($request->header('token')){
            $this->token = $request->header('token');
            $apiController = new ApiController($request);
        }
    }

    public function novoEndereco(Request $request)
    {
        $this->validate($request,[
            'user_id' => 'required',
            'cep' => 'required',
            'numero' => 'required',
            'rua' => 'required',
            'bairro' => 'required',
            'cidade' => 'required',
            //'status' => 'required'
        ]);

        try{
            $request->status = 'ATIVO';
            $user = User::findOrFail($request->user_id);
            if($user->tipo == 'ADMIN' OR $user->status == 'INATIVO'){
                return response()->json([
                    'error' => 'Usuário inválido'
                ], 405);
            }
            $endereco = Endereco::create($request->all());
            if($request->input('principal')) {
                $this->setarEndereco($endereco->id);
            }

            if($user->enderecos->count() == 1){
                $this->setarEndereco($endereco->id);
            }

            return response()->json([
                'endereco' => $endereco
            ], 200);

        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = Auth::id();
            $telemetry->metodo = 'novoEndereco';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function validaEndereco($endereco_id,$user_id)
    {
        if(Endereco::where('user_id',$user_id)->where('id',$endereco_id)->first()){
            return true;
        }

        return false;
    }

    public function editarEndereco(Request $request)
    {
        $this->validate($request,[
            'endereco_id' => 'required',
            'cep' => 'required',
            'numero' => 'required',
            'rua' => 'required',
            'bairro' => 'required',
            'cidade' => 'required',
            'status' => 'required'
        ]);
            
        try{

            if(!$this->validaEndereco($request->endereco_id,Auth::id())){
                return response()->json([
                    'error' => 'Endereço inválido'
                ], 422);
            }

            $endereco = Endereco::findOrFail($request->endereco_id);

            $endereco->update($request->all());

            return response()->json([
                'endereco' => $endereco
            ], 200);

        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = Auth::id();
            $telemetry->metodo = 'editarEndereco';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function inativaEnderecos($user_id)
    {
        \Log::alert($user_id);
        $user = User::find($user_id);

        foreach($user->enderecos as $endereco){
            $endereco->status = 'INATIVO';
            $endereco->update();
        }

    }

    public function setarEndereco($endereco_id)
    {
        try{
            if(!$this->validaEndereco($endereco_id,Auth::id())){
                return response()->json([
                    'error' => 'Endereço inválido'
                ], 405);
            }

            $this->inativaEnderecos(Auth::id());

            $endereco = Endereco::find($endereco_id);

            $endereco->status = 'ATIVO';
            $endereco->update();

            return response()->json([
                'endereco' => $endereco
            ], 200);

        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = Auth::id();
            $telemetry->metodo = 'editarEndereco';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function buscarEnderecos(Request $request)
    {
        try{
            $apiController = new ApiController($request);

            $enderecos = Auth::user()->enderecos;

            return response()->json([
                'enderecos' => $enderecos
            ], 200);

        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = Auth::id();
            $telemetry->metodo = 'buscarEnderecos';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function enderecoAtual(Request $request)
    {
        try{
            $apiController = new ApiController($request);
            return response()->json([
                'endereco' => Auth::user()->enderecoAtual
            ], 200);

        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = Auth::id();
            $telemetry->metodo = 'enderecoAtual';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function getOneEndereco($id)
    {
        try{
            $endereco =  Endereco::findOrFail($id);
            return response()->json([
                'endereco' => $endereco
            ], 200);

        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = Auth::id();
            $telemetry->metodo = 'enderecoAtual';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }
}
