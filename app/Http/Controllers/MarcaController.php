<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Marca;
use App\Telemetria;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\UploadController;

class MarcaController extends Controller
{
    public function ativar($marca_id)
    {
        try{
            $marca = Marca::find($marca_id);

            $marca->status = 'ATIVO';
            $marca->update();

            return redirect()->back()->with('success','Marca ativada com sucesso');

        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'MarcaController@ativar';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger','Erro telemetria');
        }
    }

    public function desativar($marca_id)
    {
        try{
            $marca = Marca::find($marca_id);
            if($marca){
                foreach($marca->modelos as $m){
                    foreach($m->capacidades as $c){
                        $c->delete();
                    }
                    $m->delete();
                }
                $marca->delete();
                return redirect()->back()->with('success','Marca desativada com sucesso');
            }
            //$marca->status = 'INATIVO';
            //$marca->update();

            return redirect()->back()->with('success','Marca desativada com sucesso');

        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'MarcaController@desativar';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger','Erro telemetria');
        }
    }

    public function create(Request $request)
    {   
        $this->validate($request,[
            'nome' => 'required',
            'foto' => 'required'
        ]);

        try{
            

            $marca = Marca::where('nome',$request->nome)->first();

            if($marca){
                return redirect()->back()->with('danger','Já existe uma marca com esse nome');
            }
            
            $upload = new UploadController;

            $marca = new Marca;
            $marca->nome = $request->nome;
            $marca->foto = $upload->uploadS3($request->foto);
            $marca->status = 'INATIVO';
            
            $marca->save();

            return redirect()->back()->with('success','Marca cadastrada com sucesso');

        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'MarcaController@create';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger','Erro telemetria');
        }
    }

    public function update(Request $request)
    {
        $this->validate($request,[
            'nome' => 'required',
            'marca_id' => 'required'
        ]);
            
        try{

            $upload = new UploadController;

            $marca = Marca::where('id','!=',$request->marca_id)->where('nome',$request->nome)->first();

            if($marca){
                return redirect()->back()->with('danger','Já existe uma marca com esse nome');
            }

            $marca = Marca::find($request->marca_id);

            $marca->nome = $request->nome;
            
            if($request->foto){

                //SE TEM FOTO APAGA ELA
                if($marca->foto != 'Sem foto'){
                    $delete = $upload->deleteS3(explode('projetospuzzle/',$marca->foto)[1]);
                }

                $img = $request->foto->store('public/marcas');
                $imgAux = explode('/',$img);

                $marca->foto = $upload->uploadS3($request->foto);

            }

            $marca->update();

            return redirect()->back()->with('success','Marca atualizada com sucesso');

        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'MarcaController@update';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger',$e->getMessage());
        }
    }
}
