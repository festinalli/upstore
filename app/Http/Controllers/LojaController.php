<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Loja;
use App\Telemetria;

class LojaController extends Controller
{
    public function lojas()
    {
        try{
            $lojas = Loja::all();

            return view('admin.configuracoes.lojas',compact('lojas'));

        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'LojaController@lojas';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger','Erro telemetria');
        }
    }

    public function create(Request $request)
    {
        $this->validate($request,[
            'titulo' => 'required',
            'cnpj' => 'required|unique:lojas',
            'cep' => 'required',
            'rua' => 'required',
            'numero' => 'required',
            'bairro' => 'required',
            'cidade' => 'required',
            'estado' => 'required'
        ]);

        try{

            $loja = new Loja;
            $loja->titulo = $request->titulo;
            $loja->cnpj = $request->cnpj;
            $loja->cep = $request->cep;
            $loja->endereco = $request->rua;
            $loja->numero = $request->numero;
            $loja->bairro = $request->bairro;
            $loja->cidade = $request->cidade;
            $loja->estado = $request->estado;
            $loja->status = 'ATIVO';
            $loja->save();

            return redirect()->back()->with('success','Loja cadastrada com sucesso');
        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'LojaController@create';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger',$e->getMessage());
        }
    }

    public function edit (Request $request)
    {
        $this->validate($request,[
            'titulo' => 'required',
            'cnpj' => 'required|unique:lojas',
            'cep' => 'required',
            'rua' => 'required',
            'numero' => 'required',
            'bairro' => 'required',
            'cidade' => 'required',
            'estado' => 'required'
        ]);

        try{
            $loja = Loja::where('id','!=',$request->loja_id)->where('titulo',$request->titulo)->first();

            if($loja){
                return redirect()->back()->with('danger','Já existe um loja com esse titulo');
            }

            $loja = Loja::find($request->loja_id);
            $loja->titulo = $request->titulo;
            $loja->cnpj = $request->cnpj;
            $loja->cep = $request->cep;
            $loja->endereco = $request->rua;
            $loja->numero = $request->numero;
            $loja->bairro = $request->bairro;
            $loja->cidade = $request->cidade;
            $loja->estado = $request->estado;
            $loja->status = 'ATIVO';
            $loja->save();

            return redirect()->back()->with('success','Loja editar com sucesso');
        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'LojaController@edit';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger',$e->getMessage());
        }
    }

    public function desativar($loja_id){
        try{
            $loja = Loja::find($loja_id);
            if($loja){
                $loja->delete();
                return redirect()->back()->with('success','Loja desativada com sucesso.');
            }
            return redirect()->back()->with('danger','Loja não encontrada');
        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'LojaController@desativar';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger',$e->getMessage());
        }
    }

}
