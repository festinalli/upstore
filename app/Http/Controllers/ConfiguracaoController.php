<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Marca;
use App\Telemetria;

class ConfiguracaoController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function marcas()
    {
        try{

            $marcas = Marca::all();

            return view('admin.configuracoes.marcas',compact('marcas'));

        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'ConfiguracaoController@marcas';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger','Erro telemetria');
        }
    }

    

    public function marca($marca_id)
    {   
        try{
            $marca = Marca::find($marca_id);

            if(!$marca){
                return redirect()->back()->with('danger','Marca inválida');
            }

            return view('admin.configuracoes.marca',compact('marca'));

        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'ConfiguracaoController@marcas';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger','Erro telemetria');
        }
    	
    }

    public function problemas()
    {
    	return view('admin.configuracoes.problemas');
    }

    public function servicos()
    {
    	return view('admin.configuracoes.servicos');
    }
}
