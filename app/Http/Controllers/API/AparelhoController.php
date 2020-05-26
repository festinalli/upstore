<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\CronController;
use App\Telemetria;
use App\Marca;
use App\Modelo;
use App\Capacidade;
use App\Acessorio;
use App\Problema;
use App\Servico;
use App\Aparelho;
use App\Loja;
use App\AparelhoProblema;
use App\AparelhoAcessorio;
use App\OrderServico;
use App\Endereco;
use App\MidiaServico;
use App\Banco;
use App\User;

use Carbon\Carbon;
use Auth;

class AparelhoController extends Controller
{
    private $user;
    private $token;
    public function __construct(Request $request)
    {
        if($request->header('token')){
            $this->token = $request->header('token');
            $apiController = new ApiController($request);
        }
        
        if(auth('api')->user()) {
            $this->user = auth('api')->user()->id;
        } else {
            $this->user = null;
        }
    }

    public function getMarcas()
    {
        try{
            return response()->json([
                'marcas' => Marca::where('status','ATIVO')->get()
            ], 200);

        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'getMarcas';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function getMarcaPorId($id)
    {
        try{
            $marca = Marca::where('id',$id)->where('status','ATIVO')->first();
            if($marca){
                return response()->json([
                    'marca' => [
                        'id'=>$marca->id,
                        'nome'=>$marca->nome,
                        'foto'=>$marca->foto
                    ]
                ], 200);
            }

            return response()->json([
                'error' => 'Essa marca não está cadastrada ou está desativada.'
            ], 405);

        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'getMarcas';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function getModeloPorId($id)
    {
        try{
            $modelo = Modelo::where('id',$id)->where('status','ATIVO')->first();
            if($modelo){
                return response()->json([
                    'modelo' => [
                    'id'=>$modelo->id,
                    'nome'=>$modelo->nome,
                    'marca_id'=>$modelo->marca_id
                    ]
                ], 200);
            }

            return response()->json([
                'error' => 'Esse modelo não está cadastrada ou está desativada.'
            ], 405);

        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'getModeloPorId';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function getModelos($marca_id)
    {
        try{

            $marca = Marca::find($marca_id);

            if(!$marca){
                return response()->json([
                    'error' => 'Marca não existe'
                ], 405);
            }

            if($marca->status == 'INATIVO'){
                return response()->json([
                    'error' => 'Marca inválida'
                ], 405);
            }

            return response()->json([
                'modelos' => $marca->modelosAtivos
            ], 200);

        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'getModelos';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function getCapacidades($modelo_id)
    {
        try{

            $modelo = Modelo::find($modelo_id);

            if(!$modelo){
                return response()->json([
                    'error' => 'Modelo não existe'
                ], 405);
            }

            if($modelo->status == 'INATIVO'){
                return response()->json([
                    'error' => 'Modelo inválido'
                ], 405);
            }

            if($modelo->marca->status == 'INATIVO'){
                return response()->json([
                    'error' => 'Modelo com marca inválida'
                ], 405);
            }

            return response()->json([
                'capacidades' => $modelo->capacidadesAtivas
            ], 200);

        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'getCapacidades';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function getCapacidadePorId($id)
    {
        try{

            $capacidade = Capacidade::find($id);

            if($capacidade){
                return response()->json([
                    'capacidades' => [
                        'id' => $capacidade->id,
                        'memoria' => $capacidade->memoria
                    ]
                ], 200);
            }
            return response()->json([
                'error' => 'Capacidade não existe ou não está cadastrada.'
            ], 405);
        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'getCapacidadePorId';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function getAcessorios()
    {
        try{

            return response()->json([
                'acessorios' => Acessorio::where('status','ATIVO')->get()
            ], 200);

        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'getAcessorios';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function getAcessoriosPorId(Request $request)
    {
        try{
            $acessorios = [];
            foreach($request->acessorios as $a){
                $acessorio = Acessorio::find($a);
                if($acessorio){
                    $acessorios[] = [
                        'id'    => $acessorio->id,
                        'nome'  => $acessorio->nome
                    ];
                }
            }
            return response()->json([
                'acessorios' => $acessorios
            ], 200);
        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'getAcessoriosPorId';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function getProblemasManutencao($modelo_id)
    {
        try{

            $modelo = Modelo::where('id',$modelo_id)
                                ->where('status','ATIVO')
                                ->select('id')
                                ->first();

            if(!$modelo):
                return response()->json([
                'msg' => "Modelo não encontrado"
                ], 404);
            endif;

            $problemas = Problema::where('status','ATIVO')
                                    ->where('modelo_id',$modelo->id)
                                    ->where('tipo','MANUTENCAO')
                                    ->get();

            if(!$problemas):
                return response()->json([
                'msg' => "Não contem problemas cadastrados para este modelo"
                ], 400);
            endif;

            return response()->json([
            'problemas' => $problemas
            ], 200);

        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'getProblemasManutencao';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function getProblemasPorId(Request $request)
    {
        try{
            $problemas = [];
            foreach($request->problemas as $p){
                $problema = Problema::find($p);
                if($problema){
                    $problemas[] = [
                        'id'    => $problema->id,
                        'nome'  => $problema->nome
                    ];
                }
            }
            return response()->json([
                'problemas' => $problemas
            ], 200);
        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'getProblemas';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function getProblemasUsado($modelo_id)
    {
        try{

            $modelo = Modelo::where('id',$modelo_id)
                                ->where('status','ATIVO')
                                ->select('id')
                                ->first();
            if(!$modelo):
                return response()->json([
                    'msg' => "Modelo não encontrado"
                ], 404);
            endif;

            $problemas = Problema::where('status','ATIVO')
                                    ->where('modelo_id',$modelo->id)
                                    ->where('tipo','VENDA')->get();

            if(!$problemas):
                return response()->json([
                    'msg' => "Não contem problemas cadastrados para este modelo"
                ], 400);
            endif;

            return response()->json([
                'problemas' => $problemas
            ], 200);

        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'getProblemasUsado';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function validaUser(Request $request)
    {
        $this->validate($request,[
            'nome'=>'required|string|max:100',
            'email'=>'required|string|max:100',
            'telefone'=>'max:20',
            'cpf'=>'required|string|max:14',

            'cep'=>'required|string|max:9',
            'estado'=>'required|string|max:20',
            'cidade'=>'required|string|max:50',
            'bairro'=>'required|string|max:20',
            'endereco'=>'required|string|max:100',
            'numero'=>'required|numeric|min:1',
            'complemento'=>'max:100'
        ]);
        try{
            $apiController = new ApiController($request);
            $user = User::where('email',$request->email)->first();
            if($user->email != Auth::user()->email){
                return false;
            }

            $endereco = Endereco::where('user_id',$user->id)->where('cep',$request->cep)->where('numero',$request->numero)->first();
            if($endereco){
                if($endereco->status=='OCULTO' || $endereco->status=='INATIVO'){
                    $enderecoController = new EnderecoController($request);
                    $enderecoController->inativaEnderecos($user->id);
                    $endereco->status = 'ATIVO';
                    $endereco->update();
                }
            }
            else{
                $enderecoController = new EnderecoController($request);
                $enderecoController->inativaEnderecos($user->id);
                $endereco = Endereco::create([
                    'cep'=>$request->cep,
                    'estado'=>$request->estado,
                    'cidade'=>$request->cidade,
                    'bairro'=>$request->bairro,
                    'rua'=>$request->endereco,
                    'numero'=>$request->numero,
                    'complemento'=>(isset($request->complemento)) ?? null,
                    'status'=>'ATIVO'
                ]);
            }

            return $endereco->id;

        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'validaUser';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function validaMarca($marca_id)
    {
        try{
            if(Marca::find($marca_id)){
                return true;
            }
            return false;

        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'validaMarca';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function validaModelo($modelo_id)
    {
        try{
            if(Modelo::find($modelo_id)){
                return true;
            }
            return false;

        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'validaModelo';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function validaMarcaModelo($marca_id,$modelo_id)
    {
        try{
            if(Modelo::where('id',$modelo_id)->where('marca_id',$marca_id)->first()){
                return true;
            }
            return false;

        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'validaMarcaModelo';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function validaCapacidade($capacidade_id)
    {
        try{
            if(Capacidade::find($capacidade_id)){
                return true;
            }
            return false;

        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'validaCapacidade';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function validaModeloCapacidade($modelo_id,$capacidade_id)
    {
        try{
            if(Capacidade::where('id',$capacidade_id)->where('modelo_id',$modelo_id)->first()){
                return true;
            }
            return false;

        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'validaCapacidade';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function validaAcessorios($acessorios)
    {
        try{
            foreach($acessorios as $a){
                if(!Acessorio::find($a)){
                    return false;
                }
            }
            return true;

        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'validaAcessorios';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function validaProblemas($problemas)
    {
        try{
            foreach($problemas as $p){
                if(!Problema::find($p)){
                    return false;
                }
            }
            return true;
        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'validaProblemas';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function validaLoja($loja_id)
    {
        try{
            if(Loja::find($loja_id)) return true;
            return false;
        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'validaLoja';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function criaAparelho(Request $request){
        try{
            $aparelho = Aparelho::create([
                'capacidade_id' => $request->capacidade_id,
                'senha'=>$request->senha
            ]);
            if($request->input('problemas')){
                foreach($request->problemas as $p){
                    AparelhoProblema::create([
                        'problema_id'=>$p,
                        'aparelho_id'=>$aparelho->id
                    ]);
                }
            }

            if($request->input('acessorios')){
                foreach($request->acessorios as $a){
                    AparelhoAcessorio::create([
                        'acessorio_id'=>$a,
                        'aparelho_id'=>$aparelho->id
                    ]);
                }
            }
            return $aparelho;

        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'criaAparelho';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function criaServico($aparelho_id,$user_id,$loja_id,$tipo,$metodo){
        try{
            $os = str_random(9);

            $servico = Servico::create([
                'aparelho_id' => $aparelho_id,
                'user_id' => $user_id,
                'loja_id' => $loja_id,
                'tipo' => $tipo,
                'status' => 'CRIADO',
                'metodo' => $metodo,
                'os' => $os
            ]);
            
            return $servico;

        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'criaServico';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function listarInfoAparelho($id){
        try{
            $aparelho = Aparelho::findOrFail($id);
            $capacidade = Capacidade::findOrFail($aparelho->capacidade_id);
            $valorTotal = $capacidade->valor;
            $retorno['capacidade'] = $capacidade;
            $retorno['modelo'] = $capacidade->modelo;
            $retorno['marca'] = $capacidade->modelo->marca;

            foreach($aparelho->problemas as $p){
                $retorno['problemas'][] = [
                    'nome'=> $p->problema->nome,
                    'valor'=>$p->problema->valor,
                ];
                $valorTotal -= $p->problema->valor;
            }
            foreach($aparelho->acessorios as $a){
                $retorno['acessorios'][] = [
                    'nome'=> $a->acessorio->nome,
                    'valor'=>$a->acessorio->valor,
                ];
                $valorTotal += $a->acessorio->valor;
            }


            $retorno['valor_orcamento'] = $valorTotal;
            return response()->json(['aparelho'=>$retorno],200);
        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'listarInfoAparelho';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            throw($e);
            // return response()->json([
            //     'error' => $e->getMessage()
            // ], 405);
        }
    }

    public function preOrcamento(Request $request){
        $this->validate($request,[
            'marca_id'=>'required|numeric|min:1',
            'modelo_id'=>'required|numeric|min:1',
            'capacidade_id'=>'required|numeric|min:1',
            'problemas'=>'array',
            'acessorios'=>'array',
            'senha'=>'required|string',
            //'user_id'=>'required|numeric',
            'metodo'=>'string|required|max:100',
            'tipo'=>'required|string|max:1',//'M' - Manutenção, 'V' - Venda, 'T' - Troca
        ]);
        try{
            $valorTotal = Capacidade::findOrFail($request->capacidade_id)->valor;
            $retorno = [];
            if($request->input('problemas') && $request->problemas != ['']){
                foreach($request->problemas as $p){
                    $problema = Problema::findOrFail($p);
                    $retorno['problemas_valor'][] = [
                        'nome'=> $problema->nome,
                        'valor'=>$problema->valor,
                    ];
                    $valorTotal -= $problema->valor;
                }
            }
            if($request->input('acessorios') && $request->acessorios != ['']){
                foreach($request->acessorios as $a){
                    $acessorio = Acessorio::findOrFail($a);
                    $retorno['acessorios_valor'][] = [
                        'nome'=> $acessorio->nome,
                        'valor'=>$acessorio->valor,
                    ];
                    $valorTotal += $problema->valor;
                }
            }
            $endereco = Endereco::where('user_id',$this->user)->where('status','ATIVO')->first();
            if($endereco){
                $retorno['endereco'][] = [
                    'id' => $endereco->id,
                    'rua' => $endereco->rua,
                    'numero' => $endereco->numero,
                    'complemento' => $endereco->complemento,
                    'bairro' => $endereco->bairro,
                    'cidade' => $endereco->cidade,
                    'estado' => $endereco->estado,
                    'cep' => $endereco->cep,
                ];

            }
            else $retorno['endereco'] = [];
            $apiController = new ApiController($request);
            $usuario = Auth::user();
            $retorno['usuario'][] = [
                'nome' => $usuario->nome,
                'email' => $usuario->email,
                'telefone' => $usuario->telefone,
                'cpf' => $usuario->cpf,
            ];
            $retorno['marca_id'] = intval($request->marca_id);
            $retorno['modelo_id'] = intval($request->modelo_id);
            $retorno['capacidade_id'] = intval($request->capacidade_id);
            $retorno['problemas'] = $request->input('problemas') ?? null;
            $retorno['acessorios'] = $request->input('acessorios') ?? null;
            $retorno['senha'] = $request->senha;
            $retorno['user_id'] = intval($this->user);
            $retorno['metodo'] = $request->metodo;
            $retorno['valorTotal'] = $valorTotal;

            return response(['prevenda'=>$retorno],200);
        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'preVenda';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function criaServicos(Request $request){
        $this->validate($request,[
            'marca_id'=>'required|numeric|min:1',
            'modelo_id'=>'required|numeric|min:1',
            'capacidade_id'=>'required|numeric|min:1',
            'problemas'=>'array',
            'acessorios'=>'array',
            'senha'=>'required|string',
            //'user_id'=>'required|numeric',
            'metodo'=>'string|required|max:100',
            'loja_id'=>'required|numeric|min:1',
            'tipo'=>'required|string|max:1',
            'valor_frete'=>'required|numeric|min:1'
        ]);
        try{
            $validaUser = $this->validaUser($request);
            if($validaUser == false){
                return response()->json(['error'=>'Usuário não validado.'],405);
            }
            $endereco_id = $validaUser;//precisaria aqui??

            if(!$this->validaLoja($request->loja_id)){
                return response()->json(['error' => 'Loja não existe.'], 405);
            }

            if(!$this->validaMarca($request->marca_id)){
                return response()->json(['error' => 'Marca não existe.'], 405);
            }
            if(!$this->validaModelo($request->modelo_id)){
                return response()->json(['error' => 'Modelo não existe.'], 405);
            }
            else{
                if(!$this->validaMarcaModelo($request->marca_id,$request->modelo_id)){
                    return response()->json(['error' => 'Modelo não existe para essa marca.'], 405);
                }
            }
            if(!$this->validaCapacidade($request->capacidade_id)){
                return response()->json(['error' => 'Capacidade não existe.'], 405);
            }
            else{
                if(!$this->validaModeloCapacidade($request->modelo_id,$request->capacidade_id)){
                    return response()->json(['error' => 'Capacidade não existe para esse modelo.'], 405);
                }
            }
            if($request->input('problemas') && $request->problemas != ['']){
                if(!$this->validaProblemas($request->problemas)){
                    return response()->json(['error' => 'Problema não listado nos nossos dados.'], 405);
                }
            }

            if($request->input('acessorios') && $request->acessorios != ['']){
                if(!$this->validaAcessorios($request->acessorios)){
                    return response()->json(['error' => 'Acessório não listado nos nossos dados.'], 405);
                }
            }

            $aparelho = $this->criaAparelho($request);
            $servico = $this->criaServico($aparelho->id,$this->user,$request->loja_id,$request->tipo,$request->metodo);

            return response()->json([
                'success'=>'Serviço criado com sucesso',
                'servico'=>$servico
            ],200);

        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'criaServico';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }


    /**
     * MANUTENCOES
     */
    public function minhasManutencoes(){
        try{
            $manutencoes = Servico::where('tipo','M')->where('user_id',$this->user)->get();
            $man = [];
            foreach($manutencoes as $m){
                $aparelho = Aparelho::find($m->aparelho_id);
                $capacidade = Capacidade::find($aparelho->capacidade_id);
                $modelo = $capacidade->modelo;
                $marca = $modelo->marca;

                $man[] = [
                    'servico_id'=>$m->id,
                    'foto_marca'=>$marca->foto,
                    'modelo'=>$modelo->nome,
                    'capacidade'=>$capacidade->memoria,
                    'status'=>$m->status,
                    'data'=>date('d/m/Y H:i:s',strtotime($m->updated_at)),
                ];
            }

            return response()->json(['manutencoes'=>$man],200);
        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'minhasManutencoes';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function acompanharManutencao($id, Request $request){
        try{

            $ser = Servico::findOrFail($id);
            $ser->load("aparelho");
            $ser->load("observacoes");

            $current_status = 0;

            switch ($ser->status) {
                case 'MERCADORIA_CHEGOU':
                    $current_status = 1;
                    break;
                case 'ANALISE':
                    $current_status = 2;
                    break;
                case 'PROPOSTA_ENVIADA':
                case 'CLIENTE_RECUSOU':
                    $current_status = 3;
                    break;
                case 'CLIENTE_ACEITOU':
                case 'EM_MANUTENCAO':
                    $current_status = 4;
                    break;
                case 'MANUTENCAO_FINALIZADA':
                    $current_status = 5;
                    break;
            }

            if($request->input('status')) {
                $ser->status = $request->status;
            }

            if($ser->aparelho) {
                $ser->aparelho->load('capacidade');
                if($ser->aparelho->capacidade) {
                    $ser->aparelho->capacidade->load('modelo');
                    if($ser->aparelho->capacidade->modelo) {

                        $ser->aparelho->capacidade->modelo->load('marca');
                        $ser->aparelho->modelo = $ser->aparelho->capacidade->modelo;

                        if($ser->aparelho->capacidade->modelo->marca) {
                            $ser->aparelho->marca = $ser->aparelho->capacidade->modelo->marca;
                        }
                    }
                }
            }

            $ser->load('orderServico');

            $servico = [];
            $servico['servico'] = $ser;
            $servico['data'] = date('d/m/Y H:i:s',strtotime($ser->updated_at));
            $orcamento = 0;

            if($ser->status == 'MERCADORIA_CHEGOU' && $current_status >= 1){
                $fotos = MidiaServico::where('servico_id',$ser->id)->where('status','MERCADORIA_CHEGOU')->get();
                $videos = MidiaServico::where('servico_id',$ser->id)->where('status','MERCADORIA_CHEGOU')->get();
                foreach($fotos as $f){
                    $servico['fotos'][] = [
                        'foto'=>$f->foto,
                        'status'=>$f->status,
                    ];
                }
                foreach($fotos as $f){
                    $servico['videos'][] = [
                        'video'=>$f->video,
                        'status'=>$f->status,
                    ];
                }
            }
            elseif($ser->status == 'ANALISE' && $current_status >= 2){
                $aparelho = $ser->aparelho;

                foreach($aparelho->problemas as $p){
                    $servico['problemas'][] = [
                        'problema'=>$p->problema->nome,
                        'valido'=>$p->valido
                    ];
                    $orcamento += intval($p->problema->valor);
                }
                $fotos = MidiaServico::where('servico_id',$ser->id)->where('status','ANALISE')->get();
                $videos = MidiaServico::where('servico_id',$ser->id)->where('status','ANALISE')->get();
                foreach($fotos as $f){
                    $servico['fotos'][] = [
                        'foto'=>$f->foto,
                        'status'=>$f->status,
                    ];
                }
                foreach($fotos as $f){
                    $servico['videos'][] = [
                        'video'=>$f->video,
                        'status'=>$f->status,
                    ];
                }

                $servico['senha'] = $aparelho->senha;
                $servico['orcamento'] = $orcamento;
                $servico['validade_orcamento'] = strtotime(date('Y-m-d')) - strtotime(date('Y-m-d',strtotime($ser->updated_at)));
                $servico['parcelamento'] = $ser->parcelamento;
            }
            elseif($ser->status == 'PROPOSTA_ENVIADA' && $current_status >= 3){
                $aparelho = $ser->aparelho;

                foreach($aparelho->problemas as $p){
                    $servico['problemas'][] = [
                        'problema'=>$p->problema->nome,
                        'valido'=>$p->valido
                    ];
                }

                $orcamento += $ser->valor;
                $servico['senha'] = $aparelho->senha;
                $servico['orcamento'] = $orcamento;
                $servico['validade_orcamento'] = strtotime(date('Y-m-d')) - strtotime(date('Y-m-d',strtotime($ser->updated_at)));
                $servico['parcelamento'] = $ser->parcelamento;
                $servico['garantia'] = $ser->gerantia;
            }
            elseif(($ser->status == 'EM_MANUTENCAO' || $ser->status == 'CLIENTE_ACEITOU') && $current_status >= 4){
                $fotos = MidiaServico::where('servico_id',$ser->id)->where('status','EM_MANUTENCAO')->get();
                $videos = MidiaServico::where('servico_id',$ser->id)->where('status','EM_MANUTENCAO')->get();
                foreach($fotos as $f){
                    $servico['fotos'][] = [
                        'foto'=>$f->foto,
                        'status'=>$f->status,
                    ];
                }
                foreach($fotos as $f){
                    $servico['videos'][] = [
                        'video'=>$f->video,
                        'status'=>$f->status,
                    ];
                }
            }
            elseif($ser->status == 'MANUTENCAO_FINALIZADA' && $current_status >= 5){
                $servico['etiqueta'] = $ser->envio ? $ser->envio->codigo_rastreio : null;
            }

            return response()->json($servico,200);

        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'minhasManutencoes';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    /**
     * VENDAS
     */
    public function minhasVendas(){
        try{
            $manutencoes = Servico::where('tipo','V')->where('user_id',$this->user)->get();
            $man = [];
            foreach($manutencoes as $m){
                $aparelho = Aparelho::find($m->aparelho_id);
                $capacidade = Capacidade::find($aparelho->capacidade_id);
                $modelo = $capacidade->modelo;
                $marca = $modelo->marca;

                $man[] = [
                    'servico_id'=>$m->id,
                    'foto_marca'=>$marca->foto,
                    'modelo'=>$modelo->nome,
                    'foto_modelo'=>$modelo->foto,
                    'capacidade'=>$capacidade->memoria,
                    'valor'=>$m->valor,
                    'status'=>$m->status,
                    'data'=>$m->updated_at,
                ];
            }

            return response()->json(['vendas'=>$man],200);
        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'minhasManutencoes';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function acompanharVenda($id){
        try{
            $ser = Servico::findOrFail($id);
            $ser->load("aparelho");
            $servico = [];
            $orcamento = 0;
            $servico['servico'] = $ser;
            $servico['data'] = date('d/m/Y H:i:s',strtotime($ser->updated_at));

            if($ser->aparelho) {
                $ser->aparelho->load('capacidade');
                if($ser->aparelho->capacidade) {
                    $ser->aparelho->capacidade->load('modelo');
                    if($ser->aparelho->capacidade->modelo) {

                        $ser->aparelho->capacidade->modelo->load('marca');
                        $ser->aparelho->modelo = $ser->aparelho->capacidade->modelo;

                        if($ser->aparelho->capacidade->modelo->marca) {
                            $ser->aparelho->marca = $ser->aparelho->capacidade->modelo->marca;
                        }
                    }
                }
            }

            if($ser->status == 'CHEGOU'){
                $fotos = MidiaServico::where('servico_id',$ser->id)->where('status','CHEGOU')->get();
                $videos = MidiaServico::where('servico_id',$ser->id)->where('status','CHEGOU')->get();
                foreach($fotos as $f){
                    $servico['fotos'][] = [
                        'foto'=>$f->foto,
                        'status'=>$f->status,
                    ];
                }
                foreach($fotos as $f){
                    $servico['videos'][] = [
                        'video'=>$f->video,
                        'status'=>$f->status,
                    ];
                }
            }
            elseif($ser->status == 'ANALISE'){
                $aparelho = $ser->aparelho;

                foreach($aparelho->problemas as $p){
                    $servico['problemas'][] = [
                        'problema'=>$p->problema->nome
                    ];
                    $orcamento -= intval($p->problema->valor);
                }
                foreach($aparelho->acessorios as $a){
                    $servico['acessorios'][] = [
                        'acessorio'=>$a->acessorio->nome
                    ];
                    $orcamento += intval($a->acessorio->valor);
                }
                $fotos = MidiaServico::where('servico_id',$ser->id)->where('status','ANALISE')->get();
                $videos = MidiaServico::where('servico_id',$ser->id)->where('status','ANALISE')->get();
                foreach($fotos as $f){
                    $servico['fotos'][] = [
                        'foto'=>$f->foto,
                        'status'=>$f->status,
                    ];
                }
                foreach($fotos as $f){
                    $servico['videos'][] = [
                        'video'=>$f->video,
                        'status'=>$f->status,
                    ];
                }

                $servico['senha'] = $aparelho->senha;
                $servico['orcamento'] = $orcamento;
                $servico['validade_orcamento'] = strtotime(date('Y-m-d')) - strtotime(date('Y-m-d',strtotime($ser->updated_at)));
                $servico['parcelamento'] = $ser->parcelamento;
            }
            elseif($ser->status == 'APROVADO'){
                $aparelho = $ser->aparelho;

                foreach($aparelho->problemas as $p){
                    $servico['problemas'][] = [
                        'problema'=>$p->problema->nome
                    ];
                    $orcamento += intval($p->problema->valor);
                }

                $servico['senha'] = $aparelho->senha;
                $servico['orcamento'] = $orcamento;
                $servico['validade_orcamento'] = strtotime(date('Y-m-d')) - strtotime(date('Y-m-d',strtotime($ser->updated_at)));
                $servico['parcelamento'] = $ser->parcelamento;
                $servico['garantia'] = $ser->gerantia;
            }
            elseif($ser->status == 'MANUTENCAO_ENCERRADA'){
                $servico['etiqueta'] = $ser->envio->codigo_rastreio;
            }

            return response()->json($servico,200);

        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'minhasManutencoes';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    /**
     * TROCAS
     */
    public function minhasTrocas(){
        try{
            $manutencoes = Servico::where('tipo','T')->where('user_id',$this->user)->get();
            $man = [];
            foreach($manutencoes as $m){

                $aparelho = Aparelho::find($m->aparelho_id);
                $capacidade = Capacidade::find($aparelho->capacidade_id);
                $modelo = $capacidade->modelo;
                $marca = $modelo->marca;

                $man[] = [
                    'servico_id'=>$m->id,
                    'foto_marca'=>$marca->foto,
                    'modelo'=>$modelo->nome,
                    'foto_modelo'=>$modelo->foto,
                    'capacidade'=>$capacidade->memoria,
                    'valor'=>$m->valor,
                    'deposito_cupom'=>$m->deposito_cupom,//0 - deposito, 1 - cupom
                    'status'=>$m->status,
                    'data'=>$m->updated_at,
                    'order' => $m->order,
                ];
            }

            return response()->json(['trocas'=>$man],200);
        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'minhasManutencoes';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }


    public function registerAparelho(Request $request) {
        
        $this->validate($request, [
            'step' => 'required'
        ]);

        switch($request->step) {
            case 'STEP_1':
                $aparelho = new Aparelho();

                $this->validate($request, [
                    'capacidade_id' => 'required',
                ]);

                $capacidade = Capacidade::findOrFail($request->capacidade_id);

                $aparelho->capacidade_id = $capacidade->id;
                $aparelho->modelo_id = $capacidade->modelo->id;
                $aparelho->marca_id = $capacidade->modelo->marca->id;
                $aparelho->save();

                return response()->json($aparelho, 200);
            case 'STEP_2':
                $this->validate($request, [
                    'aparelho_id' => 'required',
                    'acessorios'  => 'array',
                ]);

                $aparelho = Aparelho::findOrFail($request->aparelho_id);

                if($request->input() AND count($request->acessorios) >= 1){
                    foreach($request->acessorios as $acessorio){
                        $acessorio = Acessorio::findOrFail($acessorio);
                        if($acessorio){
                            AparelhoAcessorio::create([
                                'aparelho_id' => $request->aparelho_id,
                                'acessorio_id' => $acessorio->id,
    
                            ]);
                        }
                    }
                }
                
                return response()->json($aparelho, 200);
            case 'STEP_3':
                $this->validate($request, [
                    'aparelho_id' => 'required',
                    'problemas'  => 'array',
                    'loja_login' => 'string',
                    'loja_senha' => 'string'
                ]);
                
                $aparelho = Aparelho::findOrFail($request->aparelho_id);
                $aparelho->problema_descricao = $request->input('problema_descricao');
                $aparelho->sequencia_senha = $request->input('sequencia_senha');
                $aparelho->senha = $request->input('senha');
                $aparelho->loja_login = $request->input('loja_login');
                $aparelho->loja_senha = $request->input('loja_senha');
                $aparelho->update();


                if($request->input('problemas')){
                    foreach($request->problemas as $problema){
                        $problema = Problema::findOrFail($problema);
                        
                        if($problema){

                            AparelhoProblema::create([
                                'aparelho_id' => $request->aparelho_id,
                                'problema_id' => $problema->id,
                                'modelo_id' => $problema->modelo->id,
                                'marca_id' => $problema->modelo->marca->id,
                                'capacidade_id' => $aparelho->capacidade_id
                            ]);

                            
                        }
                    }            
                }

                return response()->json($aparelho, 200);
            case 'STEP_4':
                $this->validate($request, [
                    'aparelho_id' => 'required',
                ]);

                if(!$this->user) {
                    return response()->json('Unauthorized', 403);
                }

                $aparelho = Aparelho::findOrFail($request->aparelho_id);

                return response()->json($aparelho);
            case 'STEP_5':
                $this->validate($request, [
                    'aparelho_id' => 'required',
                    'token' => 'requred',
                    'endereco_id' => 'required',
                    'tipo_frete' => 'required', // correios ou loja,
                    'type' => 'required',
                ]);

                if(!$this->user) {
                    return response()->json("Unauthorized", 401);
                }

                $aparelho = Aparelho::findOrFail($request->aparelho_id);

                $servico_existente = Servico::where('aparelho_id', $aparelho->id)->first();
                if($servico_existente) {
                    return response()->json('Já existe um serviço atrelado ao aparelho selecionado', 422);
                }

                $os = str_random(9);
                $servico = Servico::where('os',$os)->first();

                while($servico){
                    $os = str_random(9);
                    $servico = Servico::where('os',$os)->first();
                }

                $servico = new Servico();
                $servico->aparelho_id = $aparelho->id;
                $servico->loja_id = 0;
                $servico->descricao = $aparelho->problema_descricao ?: ' ';
                $servico->user_id = $this->user;
                $servico->status = 'CRIADO';
                $servico->metodo = $request->input('tipo_frete');
                $servico->tipo = $request->input('type'); // M ou V ou T
                $servico->loja_id = 0;
                $servico->os = strtoupper($os);

                if($request->input('loja_id')) {
                    $loja = Loja::findOrFail($request->loja_id);
                    $servico->loja_id = $request->loja_id;
                } else {
                    $servico->loja_id = 0;
                }

                $valor = $this->getValorAparelho($aparelho);

                $servico->valor = $valor;
                $servico->save();

                return response()->json($servico, 200);
                
                break;
            case 'STEP_6':
                break;
        }
    } 

    private function getValorAparelho(Aparelho $aparelho) {

        try{
            $capacidade = Capacidade::findOrFail($aparelho->capacidade_id);
            $valorTotal = $capacidade->valor;
            $retorno['capacidade'] = $capacidade;
            $retorno['modelo'] = $capacidade->modelo;
            $retorno['marca'] = $capacidade->modelo->marca;

            foreach($aparelho->problemas as $p){
                $retorno['problemas'][] = [
                    'nome'=> $p->problema->nome,
                    'valor'=>$p->problema->valor,
                ];
                $valorTotal -= $p->problema->valor;
            }
            foreach($aparelho->acessorios as $a){
                $retorno['acessorios'][] = [
                    'nome'=> $a->acessorio->nome,
                    'valor'=>$a->acessorio->valor,
                ];
                $valorTotal += $a->acessorio->valor;
            }


            $retorno['valor_orcamento'] = $valorTotal;

            return $valorTotal;
        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'getValorAparelho';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            throw($e);
            // return response()->json([
            //     'error' => $e->getMessage()
            // ], 405);
        }
    }

    public function aceitarServico($servico_id,Request $request){
        try{

            $servico = Servico::findOrFail($servico_id);

            if($servico->isManutencao()) {
                $servico->manutencao_data = Carbon::now();

                $order = OrderServico::where('servico_id', $servico->id)->first();

                if(!$order) {
                    $order = new OrderServico();
                    //CRIANDO ORDER DE PAGAMENTO
                    $order = new OrderServico;
                    $order->user_id = $servico->cliente->id;
                    $order->servico_id = $servico->id;
                    $order->status = 'CRIADO';
                    $order->valor_total = $servico->valor;
                    $order->hash_cartao = ' ';
                    $order->save();
                }

                $servico->update();
            } else {

            }
            

            return response()->json($servico,200);

        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'AparelhoController@aceitarServico';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function aceitarServicoVenda($servico_id,Request $request){
        $this->validate($request,[
            'banco'             => 'required|string|max:100',
            'agencia'           => 'required|string|max:20',
            'conta'             => 'required|string|max:20',
            'tipo_conta'        => 'required|string|max:100',
            'titular'           => 'required|string|max:191',
            'documento_titular' => 'required|string|max:20',
            'foto_comprovante'  => 'max:191',
        ]);
        try{
            $servico = Servico::findOrFail($servico_id);
            $servico->status = 'CLIENTE_ACEITOU';
            $servico->update();
            $banco = Banco::create([
                'banco'             => $request->banco,
                'agencia'           => $request->agencia,
                'conta'             => $request->conta,
                'tipo_conta'        => $request->tipo_conta,
                'titular'           => $request->titular,
                'documento_titular' => $request->documento_titular,
                'foto_comprovante'  => $request->foto_comprovante,
                'servico_id'        => $servico->id,
            ]);

            return response()->json('Cadastrado com sucesso',200);
        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'AparelhoController@aceitarServicoVenda';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function recusarServico($servico_id,Request $request){
        try{
            $servico = Servico::findOrFail($servico_id);

            $servico->status = 'CLIENTE_RECUSOU';
            $servico->update();

        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'AparelhoController@recusarServico';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function linkPagamento($servico_id,Request $request){
        try{
            $cronController = new CronController();
            $servico = Servico::findOrFail($servico_id);

            if($servico->orderServico && !$servico->orderServico->link_pagamento) {
                $cronController->geraPagamentoServicoManutencaoIndividual($servico_id);
            }

            return response()->json($servico->orderServico);

        }catch(\Exception $e){
            \Log::alert($e);
            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'AparelhoController@linkPagamento';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}