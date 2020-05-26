<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\NotificacaoController;
use App\Produto;
use App\Telemetria;
use App\Foto;
use App\Categoria;
use App\ProdutoCategoria;
use App\Marca;
use App\Loja;
use App\Desconto;

use App\EstoqueLoja;

use App\Http\Controllers\UploadController;

class ProdutoController extends Controller
{
    public function produtos()
    {
        try{

            $produtos = Produto::all();
            $categorias = Categoria::all();
            $marcas = Marca::all();

            foreach ($produtos as $produto) {
                $total = 0;

                foreach ($produto->estoques as $e) {
                    $total += $e->quantidade;
                }

                $produto->qtd_estoque = $total;
                
            }

            return view('admin.ecommerce.produtos',compact('produtos','categorias','marcas'));

        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'ProdutoController@produtos';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger',$e->getMessage());
        }
    }

    public function create(Request $request)
    {   

        $this->validate($request,[
            'nome' => 'required|unique:produtos',
            'valor' => 'required',
            'descricao' => 'required',
            'foto' => 'required',
            'semi_novo' => 'required',
            'marca_id' => 'required',
        ]);

        try{
            
            $valor = str_replace('R$','',$request->valor);
            $valor = str_replace(',','',$valor);
            $valor = str_replace('.','',$valor);    
            $upload = new UploadController;

            $produto = new Produto;
            $produto->nome = $request->nome;
            $produto->valor = $valor;
            $produto->descricao = $request->descricao;
            $produto->semi_novo = $request->semi_novo;
            $produto->marca_id = $request->marca_id;
            $produto->destaque = 0;
            $produto->voltagem = 'voltagem';
            
            // Dimensões
            $produto->peso           = str_replace(',', '.', $request->peso);
            $produto->altura         = str_replace(',', '.', $request->altura);
            $produto->largura        = str_replace(',', '.', $request->largura);
            $produto->comprimento    = str_replace(',', '.', $request->comprimento);

            if(!$request->capacidade){
                $produto->capacidade_id = 0;
            }else{
                $produto->capacidade_id = $request->capacidade;
            }

            $produto->status = 'INATIVO';
            $produto->quantidade = 0;
            $produto->save();

            $foto = new Foto;
            $foto->diretorio = $upload->uploadS3($request->foto);
            $foto->principal = 1;
            $foto->produto_id = $produto->id;
            $foto->save();

            if($request->categorias){
                foreach($request->categorias as $categoria){
                    $pc = new ProdutoCategoria;
                    $pc->produto_id = $produto->id;
                    $pc->categoria_id = $categoria;
                    $pc->save();
                }
            }
            
            return redirect()->back()->with('success','Produto cadastrado com sucesso');

        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'ProdutoController@create';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger',$e->getMessage());
        }
    }

    public function ativar($id){
        try{
            $produto = Produto::findOrFail($id);
            if($produto && $produto->status != 'ATIVO'){
                $produto->status = 'ATIVO';
                $produto->update();
                return redirect()->back()->with('success','Produto ativado com sucesso');
            }
            return redirect()->back()->with('danger','Produto não encontrado ou já ativado.');
        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'ProdutoController@ativar';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger',$e->getMessage());
        }
    }

    public function desativar($id){
        try{
            $produto = Produto::findOrFail($id);
            if($produto && $produto->status != 'INATIVO'){
                $produto->status = 'INATIVO';
                $produto->update();
                return redirect()->back()->with('success','Produto desativado com sucesso');
            }
            return redirect()->back()->with('danger','Produto não encontrado ou já desativado.');
        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'ProdutoController@desativar';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger',$e->getMessage());
        }
    }

    public function produto($id){
        try{
            $produto = Produto::findOrFail($id);
            $marcas = Marca::all();
            $categorias = Categoria::all();


            // Dimensões fix
            $produto->peso = str_replace('.', ',', $produto->peso + 0);
            $produto->altura = str_replace('.', ',', $produto->altura + 0);
            $produto->comprimento = str_replace('.', ',', $produto->comprimento + 0);
            $produto->largura = str_replace('.', ',', $produto->largura + 0);

            return view('admin.ecommerce.produto',compact('produto','marcas','categorias'));
        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'ProdutoController@produto';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger',$e->getMessage());
        }
    }

    public function atualizar($id,Request $request)
    {   

        $this->validate($request,[
            'nome' => 'required',
            'valor' => 'required',
            'descricao' => 'required',
            'semi_novo' => 'required',
            'marca_id' => 'required|numeric',
        ]);

        try{
            $produto = Produto::findOrFail($id);

            $valor = str_replace('R$','',$request->valor);
            $valor = str_replace(',','',$valor);
            $valor = str_replace('.','',$valor);

            $produto->nome = $request->nome;
            $produto->valor = $valor;
            $produto->descricao = $request->descricao;
            $produto->semi_novo = $request->semi_novo;
            $produto->marca_id = $request->marca_id;
            $produto->capacidade_id = $request->capacidade;
            $produto->destaque = $request->destaque ? 1 : 0;

            // Dimensões
            $produto->peso           = str_replace(',', '.', $request->peso);
            $produto->altura         = str_replace(',', '.', $request->altura);
            $produto->largura        = str_replace(',', '.', $request->largura);
            $produto->comprimento    = str_replace(',', '.', $request->comprimento);

            $produto->update();

            foreach($produto->categorias as $c){
                if(!in_array($c->id,$request->categorias)){
                    $c->delete();
                }
            }
            foreach($request->categorias as $categoria){
                $pc = ProdutoCategoria::where('produto_id',$produto->id)->where('categoria_id',$categoria)->first();
                if(!$pc){
                    $pc = new ProdutoCategoria;
                    $pc->produto_id = $produto->id;
                    $pc->categoria_id = $categoria;
                    $pc->save();
                }
            }

            return redirect()->back()->with('success','Produto cadastrado com sucesso');

        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'ProdutoController@create';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger',$e->getMessage());
        }
    }

    public function adicionarEstoque($id,Request $request){
        $this->validate($request,[
            'quantidade' => 'required|numeric|min:1',
            'loja_id' => 'required|numeric|min:1',
            'tipo'=>'required|string|max:1'
        ]);

        try{
            $loja = Loja::findOrFail($request->loja_id);
            $produto = Produto::findOrFail($id);
            $estoque = EstoqueLoja::where('produto_id',$id)->where('loja_id',$loja->id)->where('tipo', $request->tipo)->first();
            if(!$estoque){
                $estoque = new EstoqueLoja;
                $estoque->loja_id = $loja->id;
                $estoque->produto_id = $produto->id;
                $estoque->quantidade = $request->quantidade;
                $estoque->tipo = $request->tipo;
                $estoque->save();
            }
            else{
                $estoque->quantidade += $request->quantidade;
                $estoque->tipo = $request->tipo;
                $estoque->update();
            }
            return redirect()->back()->with('success','Estoque cadastrado com sucesso');
        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'ProdutoController@adicionarEstoque';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger',$e->getMessage());
        }
    }

    public function atualizarEstoque($id,Request $request){
        $this->validate($request,[
            'quantidade' => 'required|numeric|min:0',
        ]);

        try{
            $estoque = EstoqueLoja::findOrFail($id);
            $estoque->quantidade = $request->quantidade;
            $estoque->update();
            return redirect()->back()->with('success','Estoque alterado com sucesso');
        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'ProdutoController@atualizarEstoque';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger',$e->getMessage());
        }
    }

    public function removerEstoque($id){
        try{
            $estoque = EstoqueLoja::findOrFail($id);
            $estoque->delete();
            return redirect()->back()->with('success','Estoque removido com sucesso');
        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'ProdutoController@removerEstoque';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger',$e->getMessage());
        }
    }

    public function removerFoto($id){
        try{

            $foto = Foto::findOrFail($id);

            $produtoFotos = Foto::where('produto_id',$foto->produto_id)->get();
            if(count($produtoFotos) <= 1){
                return redirect()->back()->with('danger','O produto precisa ter pelo menos uma foto cadastrada');
            }

            $upload = new UploadController;

            $delete = $upload->deleteS3(explode('projetospuzzle/',$foto->diretorio)[1]);
            $foto->delete();

            return redirect()->back()->with('success','Foto removida com sucesso');
        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'ProdutoController@removerFoto';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger',$e->getMessage());
        }
    }

    public function alterarStatusPromo(Request $request)
    {
        $produto = Produto::findOrFail($request->produto_id);

        $promo = $produto->descontos->first();

        if($promo){
            $promo->status = $request->status;
            $promo->desconto = $request->desconto;
            $promo->update();
        }else{
            $desconto = new Desconto;
            $desconto->desconto = $request->desconto;
            $desconto->produto_id = $produto->id;
            $desconto->status = $request->status;
            $desconto->save();
        }

        $notificacaoController = new NotificacaoController;
        $dados = [
            'user_id'    => 0,
            'link'       => $produto->id,
            'tipo'       => 'P',
            'status'     => 'NOVA_PROMOCAO'
        ];

        //Notifica o usuário
        $notificacaoController->geraNotificacaoUsuarioPromocao($dados);

        return redirect()->back()->with('success','Promoção atualizada com sucesso!');
    }

    public function createFoto(Request $request)
    {   
        $this->validate($request,[
            'foto' => 'required',
            'produto_id' => 'required'
        ]);

        try{

            $upload = new UploadController;

            $produto = Produto::findOrFail($request->produto_id);

            $foto = new Foto;
            $foto->produto_id = $request->produto_id;
            $foto->diretorio = $upload->uploadS3($request->foto);
            $foto->save();

            return redirect()->back()->with('success','Foto adicionada com sucesso');

        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'ProdutoController@createFoto';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger',$e->getMessage());
        }
    }

    public function updateFoto(Request $request)
    {   
        $this->validate($request,[
            'foto' => 'required',
            'foto_id' => 'required'
        ]);

        try{

            $upload = new UploadController;

            $foto = Foto::findOrFail($request->foto_id);

            $delete = $upload->deleteS3(explode('projetospuzzle/',$foto->diretorio)[1]);

            $foto->diretorio = $upload->uploadS3($request->foto);
            $foto->update();

            return redirect()->back()->with('success','Foto atualizada com sucesso');

        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'ProdutoController@updateFoto';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger',$e->getMessage());
        }
    }
}
