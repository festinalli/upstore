<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\ApiController;
use App\Telemetria;
use App\ProdutoCategoria;
use App\Produto;
use App\Foto;
use App\Desconto;
use App\EstoqueLoja;
use App\Categoria;

class ProdutoController extends Controller
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

    public function isInArray($produtos,$produto_id){
        foreach($produtos as $p){
            if($p->id == $produto_id) return true;
        }
        return false;
    }

    public function verificaEstoqueProduto($produto)
    {
        if(EstoqueLoja::where('produto_id',$produto->id)->where('quantidade','>',0)->first()){
            return true;
        }

        return false;
    }

    public function verificaEstoqueProdutoQtd($produto,$quantidade)
    {
        if(EstoqueLoja::where('produto_id',$produto->id)->where('quantidade','>=',$quantidade)->first()){
            return true;
        }

        return false;
    }

    public function retiraProdutosSemEstoque($produtos)
    {
        try{
            $response = array();
            foreach($produtos as $produto){
                if($produto->hasEstoque() > 0){
                    $response[] = $produto;
                }
            }
            return $response;
        }catch(\Exception $e){

            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'retiraProdutosSemEstoque';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function buscarTodosProdutos(Request $request)
    {
        try{
            if($request->all() != [] && $request->categorias != [""]){
                $produtosCategorias = ProdutoCategoria::whereIn('categoria_id',$request->categorias)->get();
                $produtos = [];
                foreach($produtosCategorias as $c){
                    if($c->produto->status == 'ATIVO'){
                        if(!$this->isInArray($produtos,$c->produto->id)) $produtos[] = $c->produto;
                    }
                }
            }
            else{
                $produtosTodos = Produto::where('status','ATIVO')->get();
                $produtos = [];
                foreach($produtosTodos as $p){
                    if(!$this->isInArray($produtos,$p->id)) $produtos[] = $p;
                }
            }

            $produtos = $this->retiraProdutosSemEstoque($produtos);
            
            foreach($produtos as $p){
                $p->foto_principal = Foto::where('produto_id',$p->id)->where('principal',true)->first()->diretorio;
                if($p->descontos->count() > 0){
                    $p->desconto = Desconto::where('produto_id',$p->id)->where('status','ATIVO')->first()->desconto;
                    $p->valor = $p->valor*(100-$p->desconto)/100;
                }
                else $p->desconto = null;
            }

            return response()->json([
                'produtos' => $produtos
            ], 200); 

        }catch(\Exception $e){

            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = '/buscar/todos';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function produtosPorCategoria($categoria_id)
    {
        $relations = ProdutoCategoria::where('categoria_id',$categoria_id)->get();
        
        $response = array();

        foreach($relations as $relation){
            
            if(!in_array($relation->produto,$response)){
                $response[] = $relation->produto;
            }

        }
        return $response;
    }

    public function buscarPorCategoria($categoria_id)
    {
        try{
            $produtos = $this->produtosPorCategoria($categoria_id);

            $produtos = $this->retiraProdutosSemEstoque($produtos);

            return response()->json([
                'produtos' => $produtos
            ], 200);

        }catch(\Exception $e){

            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = '/buscar/por/categoria/{categoria_id}';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function buscar(Request $request)
    {
        $this->validate($request,[
            'search'
        ]);

        try{
            $produtos = Produto::where('nome','like','%'.$request->search.'%')
                        ->orWhere('descricao','like','%'.$request->search.'%')
                        ->where('status','ATIVO')
                        ->get();

            $produtos = $this->retiraProdutosSemEstoque($produtos);
            
            foreach($produtos as $p){
                $p->foto_principal = Foto::where('produto_id',$p->id)->where('principal',true)->first()->diretorio;
                if($p->descontos->count() > 0){
                    $p->desconto = Desconto::where('produto_id',$p->id)->where('status','ATIVO')->first()->desconto;
                    $p->valor = $p->valor*(100-$p->desconto)/100;
                }
                else $p->desconto = null;
            }
            
            return response()->json([
                'produtos' => $produtos
            ], 200);

        }catch(\Exception $e){

            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = '/produtos/destaque';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function produtosEmDestaque()
    {
        try{

            $produtos = Produto::where('status','ATIVO')->where('destaque',1)->get();

            $produtos = $this->retiraProdutosSemEstoque($produtos);

            foreach($produtos as $p){
                $p->foto_principal = Foto::where('produto_id',$p->id)->where('principal',true)->first()->diretorio;

                if($p->descontos->count() > 0){
                    $p->desconto = Desconto::where('produto_id',$p->id)->where('status','ATIVO')->first()->desconto;
                    $p->valor = $p->valor*(100-$p->desconto)/100;
                }
                else $p->desconto = null;
            }

            return response()->json([
                'produtos' => $produtos
            ], 200);

        }catch(\Exception $e){

            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = '/produtos/destaque';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function produtosRelacionadosCarrinho(){
        try{
            if($this->user!=null){
                $comprados = Order::where('user_id',Auth::id())->inRandomOrder()->first();
                if($comprados){
                    $produtosCategoria = ProdutoCategoria::where('produto_id',$comprados->vendas->first()->produto_id)->inRandomOrder()->first();
                    $produtos = $produtosCategoria->produto->inRandomOrder()->limit(12)->get();
                }
                else{
                    $produtosCategoria = ProdutoCategoria::inRandomOrder()->first();
                    $produtos = $produtosCategoria->produto->inRandomOrder()->limit(12)->get();
                }
            }
            else{
                $produtosCategoria = ProdutoCategoria::inRandomOrder()->first();
                $produtos = $produtosCategoria->produto->inRandomOrder()->limit(12)->get();
            }

            foreach($produtos as $p){
                $p->foto_principal = Foto::where('produto_id',$p->id)->where('principal',true)->first()->diretorio;

                if($p->descontos->count() > 0){
                    $p->desconto = Desconto::where('produto_id',$p->id)->where('status','ATIVO')->first()->desconto;
                    $p->valor = $p->valor*(100-$p->desconto)/100;
                }
                else $p->desconto = null;
            }

            return response()->json([
                'produtos' => $produtos
            ], 200);

        }catch(\Exception $e){
            \Log::alert($e);
            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'produtosRelacionadosCarrinho';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function produtosRelacionados($produto_id){
        try{
            $produtosCategoria = ProdutoCategoria::where('produto_id',$produto_id)->inRandomOrder()->first();

            $produtos = $produtosCategoria->produto->inRandomOrder()->limit(12)->get();
            foreach($produtos as $p){
                $p->foto_principal = Foto::where('produto_id',$p->id)->where('principal',true)->first()->diretorio;

                if($p->descontos->count() > 0){
                    $p->desconto = Desconto::where('produto_id',$p->id)->where('status','ATIVO')->first()->desconto;
                    $p->valor = $p->valor*(100-$p->desconto)/100;
                }
                else $p->desconto = null;
            }

            return response()->json([
                'produtos' => $produtos
            ], 200);

        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'produtosRelacionados';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function getProdutosCarrinho(Request $request){
        $this->validate($request,[
            'produtos'=>'required|array'
        ]);
        try{
            $produtos = [];
            foreach($request->produtos as $p){
                $produto = Produto::findOrFail($p['id']);
                if($produto->descontos->count() > 0){
                    $desconto = Desconto::where('produto_id',$p['id'])->where('status','ATIVO')->first()->desconto;
                }
                else $desconto = 0;

                $produtos[] = [
                    'id'   => $produto->id,
                    'nome' => $produto->nome,
                    'foto' => Foto::where('produto_id',$p['id'])->where('principal',true)->first()->diretorio,
                    'preco' => $produto->valor*(100-$desconto)/100,
                    'quantidade' => intval($p['qtd'])
                ];
            }
            return response()->json(['produtos'=>$produtos],200);
        }catch(\Exception $e){

            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'getProdutoCarrinho';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function getQuantidadeEstoque($produto){
        try{
            $estoques = $produto->estoques;
            $produto->estoque_110 = false;
            $produto->estoque_220 = false;
            $produto->estoque_qtd = false;

            foreach($estoques as $e){
                if($e->quantidade > 0){
                    if($e->tipo == '1') $produto->estoque_110 = true;
                    if($e->tipo == '2') $produto->estoque_220 = true;
                    if($e->tipo == 'q') $produto->estoque_qtd = true;
                }
            }

            return $produto;

        }catch(\Exception $e){

            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'getQuantidadeEstoque';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function getProduto($produto_id)
    {
        try{
            $produto = Produto::findOrFail($produto_id);
            $produto = $this->getQuantidadeEstoque($produto);
            $fotos = $produto->fotos;
            
            if($this->user AND ($this->user) >= 0){
                $this->createViewProduto($produto->id,$this->user);
            }

            if($produto->descontos->count() > 0){
                $produto->desconto = Desconto::where('produto_id',$produto->id)->where('status','ATIVO')->first()->desconto;
                $produto->valor = $produto->valor*(100-$produto->desconto)/100;
            }
            else $produto->desconto = null;

            return $produto;
            
        }catch(\Exception $e){

            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'getProduto';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function verificaQuantidadeProduto($produto_id,$quantidade)
    {
        try{
            $produto = Produto::findOrFail($produto_id);

            $estoque = EstoqueLoja::where('produto_id',$produto_id)->where('quantidade','>=',$quantidade)->first();

            if($estoque){
                return response()->json([
                    'loja' => $estoque->loja,
                    'estoque' => $estoque->quantidade
                ], 200);
            }

            return response()->json([
                'error' => 'Quantidade insuficiente desse produto.'
            ], 405);

        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'verificaQuantidadeProduto';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function createViewProduto($produto_id,$user_id)
    {
        try{
            $view = View::where('produto_id',$produto_id)->where('user_id',$user_id)->first();

            if(!$view){
                $view = new View;
                $view->user_id = $user_id;
                $view->produto_id = $produto_id;
                $view->save();
            }

        }catch(\Exception $e){

            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'validaViewProduto';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function podeRetirar($produto,$categoria_id,$categorias)
    {
        foreach($produto->categorias as $categoria){
            if(in_array($categoria->categoria_id,$categorias) AND $categoria->categoria_id != $categoria_id ){
                return false;
            }
        }

        return true;
    }

    public function retirarCategoriaFiltro($produtos,$categorias,$categoria_id)
    {
        $this->validate($request,[
            'produtos' => 'required|array',
            'categorias' => 'required|array',
            'categoria_id' => 'required'
        ]);

        try{
            $response = array();

            foreach($request->produtos as $produto){

                if(ProdutoCategoria::where('produto_id',$produto->id)->where('categoria_id',$request->categoria_id)->first()){

                    if(!$this->podeRetirar($produto,$categoria_id,$categorias)){
                        $response[] = $produto;
                    }

                }else{
                    $response[] = $produto;
                }
            }

            return response()->json([
                'produtos' => $response
            ], 200);

        }catch(\Exception $e){

            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = '/retirar/categoria/filtro';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function getRelacionados($produto)
    {
        try{
            $response = array();

            foreach($produto->views as $view){
                
                if(count($view->user->views) > 0){
                    $response[] = $view->user->views[0]->produto;
                }
            }

            return $response;

        }catch(\Exception $e){

            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'getRelacionados';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function produtoRelacionados($produto_id)
    {
        try{
            $produto = Produto::findOrFail($produto_id);
            
        }catch(\Exception $e){

            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = '/produtos/relacionados';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function getCategoriasAtivas()
    {
        try{
            return response()->json([
                'categorias' => Categoria::where('status','ATIVO')->get()
            ], 200);

        }catch(\Exception $e){

            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'getCategorias';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }
}
