<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\API\CartaoController;
use App\Http\Controllers\API\EnderecoController;
use App\Order;
use App\Produto;
use App\Venda;
use App\Telemetria;
use App\Marca;
use App\Modelo;
use App\Capacidade;
use App\Servico;
use App\Endereco;
use App\ProdutoEstoqueLog;
use App\Desconto;
use App\EstoqueLoja;
use Carbon\Carbon;

use Auth;

class CarrinhoController extends Controller
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

    public function iniciar()
    {
        $token = str_random(100);
        $carrinho = Order::where('token',$token)->first();
        if(!$carrinho){
            $carrinho = Order::create([
                'token' => str_random(100)
            ]);
            return $carrinho;
        }

        return null;
    }

    public function iniciaCarrinho(){
        try{
            $carrinho = $this->iniciar();
            return response()->json([
                'carrinho'=>$carrinho,
                'vendas'=>$carrinho->vendas
            ],200);
        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'CarrinhoController@iniciaCarrinho';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function atualizarCarrinho(Request $request)
    {
        $token = $request->carrinho_token;
        $carrinho = Order::where('token',$token)->first();

        if($carrinho && $carrinho->status == "CARRINHO"){

            $diferenca = intval( time() - strtotime($carrinho->updated_at) );

            if($diferenca <= 60*60*6){ // < que 3 horas   
                $carrinho->token = $token;
                $carrinho->update();
                return response()->json([
                    'carrinho'=>$carrinho,
                    'vendas'=>$carrinho->vendas
                ],200);
            }
        }
        
        return response()->json([
            'mensagem'=>'Carrinho inexistente',
        ], 400);
    }

    public function validaToken($token)
    {
        try{
            $carrinho = Order::where('token',$token)->first();
            if($carrinho){
                return $carrinho;
            }
            else{
                return null;
            }
        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'CarrinhoController@validaToken';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function validaValor($produto,$valor){
        try{

            if($produto->descontos->count() > 0){
                $produto->desconto = Desconto::where('produto_id',$produto->id)->where('status','ATIVO')->first()->desconto;
                $produto->valor = $produto->valor*(100-$produto->desconto)/100;
            }

            if($produto->valor != $valor) return false;
            return true;
        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'CarrinhoController@validaValor';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function validaEstoque($produto,$quantidade){
        try{
            foreach($produto->estoques as $e){
                if($e->quantidade >= $quantidade) return true;
            }
            return false;
        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'CarrinhoController@validaValor';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }
    
    /**
     * ADICIONAR PRODUTO NO CARRINHO
     * 
     * 
     */
    public function adicionaProduto(Request $request)
    {
        $this->validate($request,[
            'produto_id'=>'required',
            'valor'=>'required',
            'quantidade'=>'required',
            'carrinho_token' => 'required',
            'estoque_tipo' => 'required',
        ]);
        try{
            //$token = $request->header('carrinho_token');
            $carrinho = $this->validaToken($request->carrinho_token);
            
            if(!$carrinho){
                return response()->json([
                    'mensagem'=>'Carrinho inexistente',
                ], 400);
            }

            $produto = Produto::findOrFail($request->produto_id);

            if($this->validaValor($produto,$request->valor) == false){
                return response()->json(['error'=>'Erro no valor do produto.'],405);
            }

            $loja_id = 1;
            $venda = null;
            foreach($carrinho->vendas as $v){
                if($v->estoque_tipo == $request->input('estoque_tipo')) {
                    if($v->produto_id == $produto->id){
                        $loja_id = $v->loja_id;
                        $venda = $v;
                    }
                }
            }

            //Se ainda não tem produto adicionado no carrinho
            if($venda == null){
                foreach($produto->estoques as $e){
                    if($e->quantidade >= $request->quantidade && $e->tipo == $request->input('estoque_tipo')){
                        $p = ProdutoEstoqueLog::create([
                            'quantidade' => $request->quantidade,
                            'quantidade_anterior'=> $e->quantidade,
                            'tipo' => 'ADICIONOU_CARRINHO',
                            'produto_id' => $produto->id,
                            'carrinho_id' => $carrinho->id,
                            'loja_id' => $e->loja->id
                        ]);
                        $loja_id = $e->loja->id;
                        $e->quantidade -= $request->quantidade;
                        $e->update();

                        $venda = new Venda;
                        $venda->produto_id = $request->produto_id;
                        $venda->loja_id = $loja_id;
                        $venda->nome = $produto->nome;
                        $venda->descricao = $produto->descricao;
                        $venda->order_id = $carrinho->id;
                        $venda->valor_unitario = $request->valor;
                        $venda->quantidade = $request->quantidade;
                        $venda->user_id = 0;
                        $venda->status = 'CARRINHO';
                        $venda->estoque_tipo = $e->tipo;
                        $venda->save();

                        $carrinho->vendas->push($venda);

                        return response()->json([
                            'success'=>'Produto adicionado',
                            'carrinho'=>$carrinho,
                            'vendas'=> $carrinho->vendas
                        ],200);
                    }
                }
                return response()->json([
                    'error'=>'O estoque deste produto esgotou, tente novamente mais tarde'
                ],405);
            }
            else{
                //se tem venda adicionado no carrinho, pega a loja que já está no carrinho
                $estoque = EstoqueLoja::where('loja_id',$loja_id)->where('produto_id',$venda->produto->id)->where('tipo', $venda->estoque_tipo)->first();
                if($estoque && $estoque->quantidade >= $request->quantidade && $estoque->tipo == $venda->estoque_tipo){
                    $p = ProdutoEstoqueLog::create([
                        'quantidade' => $request->quantidade,
                        'quantidade_anterior'=> $estoque->quantidade,
                        'tipo' => 'ADICIONOU_CARRINHO',
                        'produto_id' => $produto->id,
                        'carrinho_id' => $carrinho->id,
                        'loja_id' => $loja_id
                    ]);
                    $estoque->quantidade -= $request->quantidade;
                    $estoque->update();

                    $venda->quantidade += $request->quantidade;
                    $venda->valor_unitario = $request->valor;
                    $venda->update();
        
                    return response()->json([
                        'success'=>'Produto adicionado',
                        'carrinho'=>$carrinho->toArray(),
                        'vendas'=>$carrinho->vendas->toArray()
                    ],200);
                }
                return response()->json([
                    'error'=>'O estoque deste produto esgotou, tente novamente mais tarde'
                ],405);

            }
        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'CarrinhoController@adicionaProduto';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            \Log::alert($e);

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    /**
     * ALTERAR QUANTIDADE DE PRODUTO NO CARRINHO
     * 
     * 
     */
    public function alteraQuantidadeProduto(Request $request)
    {
        $this->validate($request,[
            'produto_id'=>'required',
            'valor'=>'required',
            'quantidade'=>'required',
            'carrinho_token' => 'required'
        ]);
        try{
            //$token = $request->header('carrinho_token');
            $carrinho = $this->validaToken($request->carrinho_token);
            
            if(!$carrinho){
                return response()->json(['error'=>'Erro no Token'],405);
            }

            $produto = Produto::findOrFail($request->produto_id);

            if($this->validaValor($produto,$request->valor) == false){
                return response()->json(['error'=>'Erro no valor do produto.'],405);
            }

            /*if($this->validaEstoque($produto,$request->quantidade) == false){
                return response()->json(['error'=>'Erro na Quantidade.'],405);
            }*/

            foreach($carrinho->vendas as $v){
                if($v->produto_id == $produto->id){
                    //achou o produto
                    $estoque = EstoqueLoja::where('loja_id',$v->loja_id)->where('produto_id',$v->produto->id)->first();
                    if($estoque){
                        if($v->quantidade > $request->quantidade){
                            //vai remover quantidade do carrinho
                            $p = ProdutoEstoqueLog::create([
                                'quantidade' => $request->quantidade,
                                'quantidade_anterior'=> $estoque->quantidade,
                                'tipo' => 'REMOVEU_CARRINHO',
                                'produto_id' => $produto->id,
                                'carrinho_id' => $carrinho->id,
                                'loja_id' => $v->loja_id
                            ]);
    
                            //Se removeu todos os produtos, remove a venda
                            if($v->quantidade - $request->quantidade == 0){
                                $v->delete();
                            }
                            else{
                                $v->quantidade -= $request->quantidade;
                                $v->valor_unitario = $request->valor;
                                $v->update();
                                //$carrinho = $this->validaToken($request);
                            }
                            $estoque->quantidade += $request->quantidade;
                            $estoque->update();
                            return response()->json([
                                'success'=>'Produto adicionado',
                                'carrinho'=>$carrinho,
                                'vendas'=>$carrinho->vendas
                            ],200);
                        }
                        else{
                            //vai adicionar quantidade
                            if($estoque->quantidade >= $request->quantidade){
                                $p = ProdutoEstoqueLog::create([
                                    'quantidade' => $request->quantidade,
                                    'quantidade_anterior'=> $estoque->quantidade,
                                    'tipo' => 'ADICIONOU_CARRINHO',
                                    'produto_id' => $produto->id,
                                    'carrinho_id' => $carrinho->id,
                                    'loja_id' => $v->loja_id
                                ]);

                                $estoque->quantidade -= $request->quantidade;
                                $estoque->update();

                                $v->quantidade += $request->quantidade;
                                $v->valor_unitario = $request->valor;
                                $v->update();
                    
                                return response()->json([
                                    'success'=>'Produto adicionado',
                                    'carrinho'=>$carrinho->toArray(),
                                    'vendas'=>$carrinho->vendas->toArray()
                                ],200);
                            }
                            return response()->json([
                                'error'=>'O estoque deste produto esgotou, tente novamente mais tarde'
                            ],405);
                        }
                    }
                    return response()->json([
                        'error'=>'O estoque deste produto esgotou, tente novamente mais tarde'
                    ],405);
                }
            }
            
            return response()->json([
                'error'=>'Produto não encontrado'
            ],405);

        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'CarrinhoController@alterarQuantidadeProduto';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    /**
     * REMOVER UM PRODUTO DO CARRINHO
     * 
     * 
     */
    public function removerProduto(Request $request)
    {
        $this->validate($request,[
            'carrinho_token' => 'required',
            'produto_id' => 'required',
            'quantidade' => 'required',
            'estoque_tipo' => 'required',
        ]);

        try{

            $carrinho = Order::where('token',$request->carrinho_token)->first();

            if(!$carrinho){
                return response()->json([
                    'mensagem' => 'Carrinho inválido'
                ], 400);
            }

            $venda = Venda::where('order_id',$carrinho->id)
                ->where('produto_id',$request->produto_id)
                ->where('estoque_tipo', $request->estoque_tipo)
                ->first();

            if(!$venda){
                return response()->json([
                    'mensagem' => 'Produto não está no carrinho'
                ], 400);
            }

            if($venda->quantidade > 0 AND $venda->quantidade >= $request->quantidade){
                $estoque = EstoqueLoja::where('loja_id',$venda->loja_id)
                    ->where('tipo', $venda->estoque_tipo)
                    ->where('produto_id',$request->produto_id)->first();

                $p = ProdutoEstoqueLog::create([
                    'quantidade' => $request->quantidade,
                    'quantidade_anterior'=> $estoque->quantidade,
                    'tipo' => 'REMOVEU_CARRINHO',
                    'produto_id' => $request->produto_id,
                    'carrinho_id' => $carrinho->id,
                    'loja_id' => $venda->loja_id
                ]);

                $estoque->quantidade += $request->quantidade;
                $estoque->update();

                $venda->quantidade -= $request->quantidade;
                $venda->update();

                if($venda->quantidade == 0){
                    $venda->delete();

                    return response()->json([
                        'mensagem' => 'Produto removido do carrinho'
                    ], 200);
                }else{
                    return response()->json([
                        'mensagem' => true
                    ], 200);
                }

            }

            return response()->json([
                'mensagem' => 'Quantidade inválida'
            ], 400);

        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'CarrinhoController@carrinhoProdutos';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    /**
     * REMOVER VENDA DO CARRINHO
     * 
     * 
     */
    public function removerProdutoCarrinho(Request $request)
    {
        $this->validate($request,[
            'carrinho_token' => 'required',
            'produto_id' => 'required',
            'estoque_tipo' => 'required',
        ]);

        try{

            $carrinho = Order::where('token',$request->carrinho_token)
                ->first();

            if(!$carrinho){
                return response()->json([
                    'mensagem' => 'Carrinho inválido'
                ], 400);
            }

            $venda = Venda::where('order_id',$carrinho->id)
                ->where('produto_id',$request->produto_id)
                ->where('estoque_tipo', $request->estoque_tipo)
                ->first();

            if(!$venda){
                return response()->json([
                    'mensagem' => 'Produto não está no carrinho'
                ], 400);
            }

            $estoque = EstoqueLoja::where('loja_id',$venda->loja_id)
                ->where('produto_id',$venda->produto_id)
                ->where('tipo', $venda->estoque_tipo)
                ->first();

            $p = ProdutoEstoqueLog::create([
                'quantidade' => $venda->quantidade,
                'quantidade_anterior'=> $estoque->quantidade,
                'tipo' => 'REMOVEU_CARRINHO',
                'produto_id' => $request->produto_id,
                'carrinho_id' => $carrinho->id,
                'loja_id' => $venda->loja_id
            ]);

            $estoque->quantidade += $venda->quantidade;
            $estoque->update();

            $venda->delete();
            
            return response()->json([
                'mensagem' => true
            ], 200);

        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'CarrinhoController@removerProdutoCarrinho';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }


    /**
     * FINALIZAR PEDIDO
     * 
     * 
     */
    public function finalizarPedido(Request $request)
    {
        $this->validate($request,[
            'endereco_id' => 'required',
            'frete_identificador' => 'required',
            'frete_valor' => 'required',
            'frete_prazo' => 'required',
            'frete_tipo' => 'required',
            'forma_pagamento' => 'required',
            'parcelamento' => 'required',
            'cartao_id' => 'required',
            'salva_cartao' => 'required',
            'troca'=>'required',
            'servico_id'=>'numeric',
            'carrinho_token'=>'required'
        ]);

        try{
            $apiController = new ApiController($request);
            $carrinhoToken = $request->carrinho_token;
            $carrinho = Order::where('token',$carrinhoToken)->first();
            
            if(!$carrinho){
                return response()->json([
                    'error' => 'Carrinho não existe'
                ], 405);
            }
            
            if($request->forma_pagamento == 'cartao'){
                $cartaoController = new CartaoController($request);
                $cartao_id = $request->cartao_id;
                if($cartao_id <= 0){
                    $cartao = $cartaoController->createCartao($request);
                    if($cartao){
                        $cartao_id = $cartao->id;
                    }
                    else{
                        return response()->json([
                            'error' => 'Problema ao salvar o cartao'
                        ], 405);
                    }
                }else{
                    if(!$cartaoController->verificaDonoCartao($cartao_id)){
                        return response()->json([
                            'error' => 'Você não é o dono desse cartão'
                        ], 405);
                    }
                }
                $carrinho->forma_pagamento = 'CARTAO';
            }
            elseif($request->forma_pagamento == 'boleto'){
                $carrinho->forma_pagamento = 'BOLETO';
                $cartao_id = 0;
            }
            else{
                return response()->json([
                    'error' => 'Forma de pagamento não encontrada.'
                ], 405);
            }

            $troca = false;
            if($request->input('servico_id')){
                $servico = Servico::findOrFail($request->servico_id);
                if($servico->user_id != Auth::id()){
                    return response()->json([
                        'error' => 'Servico inválido'
                    ], 405);
                }

                if($servico->order){
                    return response()->json([
                        'error' => 'Servico inválido'
                    ], 405);
                }

                if($servico->tipo != 'T'){
                    return response()->json([
                        'error' => 'Servico inválido'
                    ], 405);
                }

                $carrinho->servico_id = $request->servico_id;
                $troca = true;
            }

            
            $enderecoController = new EnderecoController($request);
            if(!$enderecoController->validaEndereco($request->endereco_id,Auth::id())){
                return response()->json([
                    'error' => 'Endereco inválido'
                ], 405);
            }
            
            $valor_total = 0;
            foreach($carrinho->vendas as $v){
                $produto = Produto::findOrFail($v->produto_id);
                
                /*if($this->validaEstoque($produto,$v->quantidade) == false){
                    return response()->json(['error'=>'Erro na Quantidade do produto '.$produto->nome],405);
                }*/

                $valor_total += $v->valor_unitario * $v->quantidade;
            }

            $carrinho->user_id = Auth::id();
            $endereco = Endereco::findOrFail($request->endereco_id);
            $carrinho->cep = $endereco->cep;
            $carrinho->rua = $endereco->rua;
            $carrinho->numero = $endereco->numero;
            $carrinho->complemento = $endereco->complemento;
            $carrinho->bairro = $endereco->bairro;
            $carrinho->cidade = $endereco->cidade;
            $carrinho->estado = $endereco->estado;
            $carrinho->parcelamento = $request->parcelamento;

            $carrinho->cartao_id = $cartao_id;
            if($request->input('codigo_id')){
                $carrinho->codigo_id = $request->codigo_id;
            }
            else{
                $carrinho->codigo_id = 0;
            }
            $carrinho->status = 'ANALISE';
            $carrinho->frete_valor = $request->frete_valor;
            $carrinho->frete_prazo = $request->frete_prazo;
            $carrinho->valor_total = $valor_total;
            $carrinho->frete_tipo = $request->frete_tipo;
            $carrinho->frete_codigo = $request->frete_identificador;
            $carrinho->desconto_valor = 0;
            $carrinho->troca = $troca;
            $carrinho->update();

            foreach($carrinho->vendas as $v){
                if($v->produto->fotos && $v->produto->fotos->first()){
                    $v->foto = $v->produto->fotos->first()->diretorio;
                }
            }

            return response()->json([
                'pedido' => 'Pedido criado com sucesso: #P'.$carrinho->id,
                'carrinho'=>$carrinho->toArray(),
                'vendas'=> $carrinho->vendas->toArray()
            ], 200);
        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'finalizarCompra';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function minhasCompras(Request $request)
    {
        try{
            $apiController = new ApiController($request);
            $pedidos = Order::where('user_id',Auth::id())->get();
            $ped = [];
            foreach($pedidos as $p){
                $ped[] = [
                    'pedido_id'=>$p->id,
                    'status'=>$p->status,
                    'atualizacao'=>date('d/m/Y',strtotime($p->updated_at)),
                ];
            }
            return response()->json(['compras'=>$ped],200);
        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'minhasCompras';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function detalhesCompra($order_id)
    {
        try{
            $order = Order::findOrFail($order_id);

            $order->vendas->load('produto');

            return response()->json($order,200);
            
        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'detalhesCompra';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function detalhesEnvio($order_id){
        try{
            $order = Order::findOrFail($order_id);
            if($order->envio){
                if($order->envio->logsEnvio){
                    foreach($order->envio->logsEnvio as $e){
                        $date = Carbon::parse($e->data);
                        $e->hora = $date->copy()->format('H:i');
                        $e->data_formatada = $date->format('d \d\e F');
                    }
                }
                $order->envio->data_criacao = $order->envio->created_at->format('d/m/Y');
            }
            if($order->vendas){
                $order->vendas->load('produto');
                foreach($order->vendas as $venda){
                    if($venda->produto){
                        if($venda->produto->fotos){
                            $venda->produto->img = $venda->produto->fotos->first()->diretorio;
                        }
                        
                        if($venda->produto->marca_id != 0){
                            $venda->produto->marca = Marca::findOrFail($venda->produto->marca_id)->foto;
                        }

                        if($venda->produto->modelo_id != 0){
                            $venda->produto->modelo = Modelo::findOrFail($venda->produto->modelo_id)->nome;
                        }
        
                        if($venda->produto->capacidade_id != 0){
                            $cap = Capacidade::find($venda->produto->capacidade_id);
                            if($cap){
                                $venda->produto->capacidade = $cap->memoria;
                            }
                            else{
                                $venda->produto->capacidade = $venda->produto->capacidade_id;
                            }
                        }
                    }
                }
            }

            return response()->json($order,200);

        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'detalhesCompra';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function carrinhoProdutos($token)
    {
        try{   
            $carrinho = Order::where('token',$token)->first();
            
            $produtos = array();

            foreach($carrinho->vendas as $venda){
                $produto = array();
                $produto['id'] = $venda->produto->id;
                $produto['foto'] = $venda->produto->fotos[0]->diretorio;
                $produto['valor'] = $venda->valor_unitario;
                $produto['quantidade'] = $venda->quantidade;
                $produto['nome'] = $venda->produto->nome;
                $produto['estoque_tipo'] = $venda->estoque_tipo;
                $produtos[] = $produto;
            }

            if($carrinho){
                return response()->json([
                    'carrinho' => $carrinho,
                    'produtos' => $produtos,
                    'quantidade' => count($carrinho->vendas)
                ], 200);
            }

            return response()->json([
                'mensagem' => 'Carrinho inválido'
            ], 400);

        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'CarrinhoController@carrinhoProdutos';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function removeProduto(Request $request)
    {
        $this->validate($request,[
            'produto_id'=>'required',
        ]);
        try{
            //$token = $request->header('carrinho_token');
            $carrinho = $this->validaToken($request);
            
            if(!$carrinho){
                return response()->json(['error'=>'Erro no Token'],405);
            }

            $produto = Produto::findOrFail($request->produto_id);

            foreach($carrinho->vendas as $v){
                if($v->produto_id == $produto->id){
                    $v->delete();
                    $carrinho = $this->validaToken($request);
                    return response()->json([
                        'success'=>'Produto removido',
                        'carrinho'=>$carrinho,
                        'vendas'=>$carrinho->vendas
                    ],200);
                }
            }

            return response()->json([
                'error'=>'Produto não encontrado',
                'carrinho'=>$carrinho,
                'vendas'=>$carrinho->vendas
            ],405);

        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'CarrinhoController@adicionaProduto';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }
}
