<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Telemetria;
use App\Acessorio;
use App\AparelhoAcessorio;

class AcessorioController extends Controller
{
    public function acessorios()
    {
        try{
            $acessorios = Acessorio::all();

            return view('admin.configuracoes.acessorios',compact('acessorios'));

        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'AcessorioController@create';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger','Erro telemetria');
        }
    }


    public function create(Request $request)
    {
        $this->validate($request,[
            'nome' => 'required',
            'valor' => 'required'
        ]);

        try{

            $existe = Acessorio::where('nome',$request->nome)->first();

            if($existe){
                return redirect()->back()->with('danger','Já existe um acessório com esse nome');
            }

            $acessorio = new Acessorio;
            $acessorio->nome = $request->nome;

            $valor = str_replace('R$','',$request->valor);
            $valor = str_replace(',','',$valor);
            $valor = str_replace('.','',$valor);

            $acessorio->valor = intval($valor);
            $acessorio->status = 'ATIVO';
            $acessorio->save();

            return redirect()->back()->with('success','Acessório cadastrado com sucesso');

        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'AcessorioController@create';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger','Erro telemetria');
        }
    }

    public function desativar($acessorio_id)
    {
        try{
            $acessorio = Acessorio::find($acessorio_id);
            if($acessorio){
                $acessorio->delete();
                return redirect()->back()->with('success','Acessório desativado com sucesso');
            }
            //$acessorio->status = 'INATIVO';
            //$acessorio->update();

            return redirect()->back()->with('danger','Acessório não encontrado');

        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'AcessorioController@desativar';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger','Erro telemetria');
        }
    }

    public function ativar($acessorio_id)
    {
        try{
            $acessorio = Acessorio::find($acessorio_id);

            $acessorio->status = 'ATIVO';
            $acessorio->update();

            return redirect()->back()->with('success','Acessório ativado com sucesso');

        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'AcessorioController@ativar';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger','Erro telemetria');
        }
    }

    public function update(Request $request)
    {
        $this->validate($request,[
            'nome' => 'required',
            'valor' => 'required',
            'acessorio_id' => 'required'
        ]);

        try{

            $existe = Acessorio::where('nome',$request->nome)->where('id','!=',$request->acessorio_id)->first();

            if($existe){
                return redirect()->back()->with('danger','Já existe um acessório com esse nome');
            }

            $acessorio = Acessorio::find($request->acessorio_id);
            $acessorio->nome = $request->nome;

            $valor = str_replace('R$','',$request->valor);
            $valor = str_replace(',','',$valor);
            $valor = str_replace('.','',$valor);

            $acessorio->valor = intval($valor);
            $acessorio->update();
            
            return redirect()->back()->with('success','Acessório cadastrado com sucesso');
            
        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'AcessorioController@update';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger','Erro telemetria');
        }
    }

    public function updateValido($id)
    {
        try{
            $ap = AparelhoAcessorio::findOrFail($id);

            $ap->valido = $ap->valido == 1 ? 0 : 1;
            $ap->update();
            
            return redirect()->back();

        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'AcessorioController@updateValido';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger',$e->getMessage());
        }
    }
}
