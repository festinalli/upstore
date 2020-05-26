<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\CorreiosController;
use PhpSigep;
use App\Servico;
use App\Order;
use App\Problema;
use App\AparelhoProblema;
use App\Envio;
use App\Telemetria;
use App\Marca;
use App\Modelo;

class RelatorioController extends Controller
{
    //

    public function vendas()
    {
        try{

            $clientes = array();
            $status = array();
            $vendas = Order::all();

            foreach ($vendas as  $venda) {
                if($venda->usuario)
                    array_push($clientes, $venda->usuario->nome);
                if($venda['status'])   
                    array_push($status, $venda['status']);
            }

            $clientes   = array_unique($clientes);
            $status     = array_unique($status);

            return view('admin.relatorios.vendas',compact('vendas','clientes','status'));

        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'RelatorioController@vendas';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger',$e->getMessage());
        }
    }

    public function venda($id)
    {
        $venda = Order::find($id);
        if($venda) return view('admin.relatorios.venda',compact('venda'));

        return redirect()->route('admin.vendas');
    }

    public function geraChancela($order_id,Request $request){
        try{
            $order = Order::findOrFail($order_id);

            $correioController = new CorreiosController;
            
            $plp = $correioController->fecharPlpVariosServicos($order->id,$order->frete_codigo);
            if($plp == -1){
                return redirect()->back()->with('danger','Parece que esse produto já faz parte de uma pré lista de postagem. Entre em contato com o administrador da plataforma para mais detalhes.');
            }
            if($plp == -2){
                return redirect()->back()->with('danger','Segundo erro.');                
            }
            return redirect()->route('admin.venda',['id'=>$order_id])->with('seccess','Etiquetas geradas.');
        }catch(\Exception $e){
            \Log::alert($e);
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'RelatorioController@geraChancela';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger','Erro telemetria');
        }
    }

    public function downloadChancela($id){
        try{
            $o = Order::findOrFail($id);
            $correioController = new CorreiosController;
            $plp = $correioController->downloadChancela($o->id);

            if(!$plp) {
                return redirect()->route('admin.venda', ['id'=>$id])->with('danger','Falha ao realizar download da chancela');

            }            


            return redirect()->route('admin.venda',['id'=>$id]);
        }catch(\Exception $e){
            throw($e);
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'RelatorioController@downloadChancela';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger','Erro telemetria');
        }
    }

    public function downloadPlp($id){
        try{
            $o = Order::findOrFail($id);
            $correioController = new CorreiosController;
            $plp = $correioController->downloadPlp($o->id);

            return redirect()->route('admin.venda',['id'=>$id]);
        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'RelatorioController@downloadChancela';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger','Erro telemetria');
        }
    }

    public function relatorioManutencoes()
    {
        $marcas = array();
        $status = array();
        $manutencoes = Servico::all();
        //dd($manutencoes);
        
        foreach ($manutencoes as $key => $manu) {
            if($manu->aparelho)
                array_push($marcas, $manu->aparelho->capacidade->modelo->marca->nome);
            if($manu['status'])   
                array_push($status, $manu['status']);
        }

        $marcas   = array_unique($marcas);
        $status   = array_unique($status);
        
        return view('admin.relatorios.manutencoes',compact('manutencoes','marcas','status'));
    }

    
    public function relatoriosProblemas(Request $request)
    {
        $problemas = AparelhoProblema::all()->groupBy('problema_id');
        $marcas = Marca::all();

    
        return view('admin.relatorios.problemas',compact('problemas','marcas'));
    }

    
}
