<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Modelo;
use App\Capacidade;
use App\Telemetria;
use App\Marca;
use App\Problema;

class ModeloController extends Controller
{
    //

    public function create(Request $request)
    {
        $this->validate($request,[
            'nome' => 'required',
            'capacidades_memoria' => 'required | array',
            'capacidades_valor' => 'required | array',
            'problemas_modelo_md' => 'required| array',
            'problemas_modelo_md_valor' => 'required| array',
            'problemas_modelo_md_tipo' => 'required| array',
            'marca_id' => 'required'
        ]);

        try{
            if(!Marca::find($request->marca_id)){
                return redirect()->back();
            }

            $modelo = new Modelo;
            $modelo->nome = $request->nome;
            $modelo->status = 'ATIVO';
            $modelo->marca_id = $request->marca_id;
            $modelo->foto = 'Sem foto';
            $modelo->save();

            foreach($request->capacidades_memoria as $key => $memoria){

                $capacidade = new Capacidade;
                $capacidade->memoria = $memoria;

                $valor = str_replace('R$','',$request->capacidades_valor[$key]);
                $valor = str_replace(',','',$valor);
                $valor = str_replace('.','',$valor);

                $capacidade->valor = intval($valor);
                $capacidade->modelo_id = $modelo->id;
                $capacidade->status = 'ATIVO';
                $capacidade->save();
            }

            foreach($request->problemas_modelo_md as $key => $problema){
                $problema = new Problema;
                $problema->nome = $request->problemas_modelo_md[$key];

                $valor = str_replace('R$','',$request->problemas_modelo_md_valor[$key]);
                $valor = str_replace(',','',$valor);
                $valor = str_replace('.','',$valor);

                $problema->valor = intval($valor);
                $problema->tipo = $request->problemas_modelo_md_tipo[$key];
                $problema->status = 'ATIVO';
                $problema->modelo_id = $modelo->id;
                $problema->save();
            }

            return redirect()->back()->with('success','Modelo registrado com sucesso');

        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'ModeloController@create';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger','Erro telemetria');
        }
    }

    public function update($id,Request $request){
        $this->validate($request,[
            'nome' => 'required',
            'capacidades_memoria' => 'required | array',
            'capacidades_valor' => 'required | array',
            'problemas_modelo_md' => 'required| array',
            'problemas_modelo_md_valor' => 'required| array',
            'problemas_modelo_md_tipo' => 'required| array',
            'marca_id' => 'required'
        ]);
        try{
            if(!Marca::find($request->marca_id)){
                return redirect()->back();
            }
            $modelo = Modelo::findOrFail($id);

            $modelo->nome = $request->nome;
            $modelo->update();

            foreach($request->capacidades_memoria as $key => $memoria){

                $capacidade = Capacidade::find($request->ids[$key]);
                if($capacidade){
                    $capacidade->memoria = $memoria;
                    
                    $valor = str_replace('R$','',$request->capacidades_valor[$key]);
                    $valor = str_replace(',','',$valor);
                    $valor = str_replace('.','',$valor);

                    $capacidade->valor = intval($valor);
                    $capacidade->update();
                }
                else{
                    $capacidade = new Capacidade;
                    $capacidade->memoria = $memoria;
                    
                    $valor = str_replace('R$','',$request->capacidades_valor[$key]);
                    $valor = str_replace(',','',$valor);
                    $valor = str_replace('.','',$valor);

                    $capacidade->valor = intval($valor);
                    $capacidade->modelo_id = $modelo->id;
                    $capacidade->status = 'ATIVO';
                    $capacidade->save();
                }
            }

            foreach($request->problemas_modelo_md as $key => $problema){
                $problema = Problema::find($request->ids_problemas[$key]);
                if($problema){
                    $problema->nome = $request->problemas_modelo_md[$key];
    
                    $valor = str_replace('R$','',$request->problemas_modelo_md_valor[$key]);
                    $valor = str_replace(',','',$valor);
                    $valor = str_replace('.','',$valor);
    
                    $problema->valor = intval($valor);
                    $problema->tipo = $request->problemas_modelo_md_tipo[$key];
                    $problema->update();
                }
                else{
                    $problema = new Problema;
                    $problema->nome = $request->problemas_modelo_md[$key];
    
                    $valor = str_replace('R$','',$request->problemas_modelo_md_valor[$key]);
                    $valor = str_replace(',','',$valor);
                    $valor = str_replace('.','',$valor);
    
                    $problema->valor = intval($valor);
                    $problema->tipo = $request->problemas_modelo_md_tipo[$key];
                    $problema->status = 'ATIVO';
                    $problema->modelo_id = $modelo->id;
                    $problema->save();
                }
            }
            return redirect()->back()->with('success','Modelo alterado com sucesso');

        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'ModeloController@update';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger','Erro telemetria');
        }
    }

    public function removerCapacidade(Request $request){
        try{
            $capacidade = Capacidade::find($request->capacidade_id);
            if($capacidade){
                $capacidade->status = 'INATIVO';
                $capacidade->update();
                return response()->json('success',200);
            }
            return response()->json('error: Capacidade não encontrada.',405);
        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'ModeloController@removerCapacidade';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return response()->json('error: '+$e->getMessage(),405);
        }
    }

    public function removerProblema(Request $request){
        try{
            $problema = Problema::find($request->problema_id);
            if($problema){
                $problema->status = 'INATIVO';
                $problema->update();
                return response()->json('success',200);
            }
            return response()->json('error: Problema não encontrado.',405);
        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'ModeloController@removerProblema';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return response()->json('error: '+$e->getMessage(),405);
        }
    }

    public function ativarModelo($id){
        try{
            $modelo = Modelo::findOrFail($id);
            if($modelo){
                $modelo->status = 'ATIVO';
                $modelo->update();
                return redirect()->back()->with('success','Modelos ativado com sucesso');
            }
            return redirect()->back()->with('error','Modelos não encontrado');
        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'ModeloController@ativarModelo';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('error','Ocorreu um erro ao ativar: '+$e->getMessage());
        }
    }

    public function desativarModelo($id){
        try{
            $modelo = Modelo::findOrFail($id);
            if($modelo){
                $modelo->status = 'INATIVO';
                $modelo->update();
                return redirect()->back()->with('success','Modelos desativado com sucesso');
            }
            return redirect()->back()->with('error','Modelos não encontrado');
        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'ModeloController@desativarModelo';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('error','Ocorreu um erro ao desativar: '+$e->getMessage());
        }
    }


    public function getModelosPorMarca(Request $request){

        try{
            $id = $request->codigo;
            $modelos = Modelo::where('marca_id',$id)->where('status','ATIVO')->get();
            return response()->json($modelos,200);
        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'ModeloController@getModelosPorMarca';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return response()->json("error",400);
        }
    }
}
