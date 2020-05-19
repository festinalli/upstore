<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\ApiController;
use App\Telemetria;
use App\Cartao;

use Auth;

class CartaoController extends Controller
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

    public function cartaoAtual(Request $request)
    {
        try{
            $apiController = new ApiController($request);
            return response()->json([
                'cartao' => Auth::user()->cartaoAtual
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

    public function getCartoes(Request $request)
    {
        try{
            $apiController = new ApiController($request);
            return response()->json(['cartoes'=>Auth::user()->cartoes],200);
        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = Auth::id();
            $telemetry->metodo = 'getCartoes';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function createCartao(Request $request)
    {
        $this->validate($request,[
            'hash' => 'required',
            'holder' => 'required|array'
            /*'mes' => 'required',
            'ano' => 'required',
            'holder' => 'required|array',
            'ultimos4'=> 'required',
            'bandeira'=>'required'*/
        ]);

        try{
            $cartao = new Cartao;
            $cartao->hash = $request->hash;
            $apiController = new ApiController($request);
            $usuario = Auth::user();
            $cartao->ultimos4 = $request->holder['ultimos4'] ?? '';//rand(1000,9999);
            $cartao->bandeira = $request->holder['bandeira'] ?? '';//'VISA';
            $cartao->holder_nome = $request->holder['nome'];
            $cartao->holder_data_nascimento = $usuario->data_nascimento;
            $cartao->holder_telefone = $usuario->telefone;
            $cartao->holder_cpf = $request->holder['cpf'];
            $cartao->user_id = Auth::id();
            if($request->salva_cartao == 1){
                $cartao->status = 'ATIVO';   
            }
            else{
                $cartao->status = 'DELETA';
            }

            $cartao->save();

            return $cartao;

        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = Auth::id();
            $telemetry->metodo = 'createCartao';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();
            return null;
            /*return response()->json([
                'error' => $e->getMessage()
            ], 405);*/
        }
    }

    public function verificaDonoCartao($cartao_id)
    {
        try{
            if(Cartao::where('id',$cartao_id)->where('user_id',Auth::id())->first()){
                return true;
            }
            return false;
        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = Auth::id();
            $telemetry->metodo = 'verificaDonoCartao';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function deletarCartao($cartao_id)
    {
        try{
            if(!$this->verificaDonoCartao($cartao_id)){
                return response()->json([
                    'error' => 'Você não é o dono desse cartão'
                ], 405);
            }

            $cartao = Cartao::findOrFail($cartao_id);
            $cartao->status = 'OCULTO';
            $cartao->update();

            return response()->json([
                'delete' => 'Cartão deletado'
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
    
}
