<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Categoria;
use App\Telemetria;

class CategoriaController extends Controller
{
    public function categorias()
    {
        try{
            $categorias = Categoria::all();

            return view('admin.ecommerce.categorias',compact('categorias'));

        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'CategoriaController@categorias';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger',$e->getMessage());
        }

    }

    public function create(Request $request)
    {
        $this->validate($request,[
            'nome' => 'required|unique:categorias'
        ]);

        try{
            $categoria = new Categoria;
            $categoria->nome = $request->nome;
            $categoria->status = 'ATIVO';
            $categoria->save();

            return redirect()->back()->with('success','Categoria cadastrada com sucesso');
            
        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'CategoriaController@create';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger',$e->getMessage());
        }
    }

    public function ativar($categoria_id)
    {

        try{
            $categoria = Categoria::find($categoria_id);
            $categoria->status = 'ATIVO';
            $categoria->update();

            return redirect()->back()->with('success','Categoria ativada com sucesso');
            
        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'CategoriaController@ativar';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger',$e->getMessage());
        }
    }

    public function desativar($categoria_id)
    {

        try{
            $categoria = Categoria::find($categoria_id);
            $categoria->status = 'INATIVO';
            $categoria->update();

            return redirect()->back()->with('success','Categoria desativada com sucesso');
            
        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'CategoriaController@desativar';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger',$e->getMessage());
        }
    }

    public function update(Request $request)
    {
        $this->validate($request,[
            'nome' => 'required',
            'categoria_id' => 'required'
        ]);

        try{
            $categoria = Categoria::where('id','!=',$request->categoria_id)->where('nome',$request->nome)->first();

            if($categoria){
                return redirect()->back()->with('danger','Já existe uma categoria com esse nome');
            }   

            $categoria = Categoria::find($request->categoria_id);
            $categoria->nome = $request->nome;
            $categoria->update();

            return redirect()->back()->with('success','Categoria cadastrada com sucesso');
            
        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'CategoriaController@update';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger',$e->getMessage());
        }
    }
}
