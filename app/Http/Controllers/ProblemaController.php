<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Problema;
use App\Telemetria;
use App\Marca;
use App\AparelhoProblema;

class ProblemaController extends Controller
{
    //

    public function problemas()
    {
        try{

            $problemas = Problema::all();
            $marcas_all = Marca::where("status","ATIVO")->get();

            $marcas     = array();
            $modelos    = array();
            $tipos       = array("Manutenção","Venda seu usado");

            foreach ($problemas as $problema) {
                if($problema->modelo)
                    array_push($modelos, $problema->modelo->nome);
                if($problema->modelo)
                    array_push($marcas, $problema->modelo->marca->nome);
            }

            $marcas   = array_unique($marcas);
            $modelos   = array_unique($modelos);

            return view('admin.configuracoes.problemas',compact('problemas','marcas','modelos','tipos','marcas_all'));

        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'ProblemaController@problemas';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger','Erro telemetria');
        } 
    }

    public function ativar($problema_id)
    {
        try{
            $problema = Problema::find($problema_id);

            $problema->status = 'ATIVO';
            $problema->update();

            return redirect()->back()->with('success','Problema ativado com sucesso');

        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'ProblemaController@ativar';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger','Erro telemetria');
        } 
    }

    public function desativar($problema_id)
    {
        try{
            $problema = Problema::find($problema_id);
            if($problema){
                $problema->delete();
                return redirect()->back()->with('success','Problema desativado com sucesso');
            }
            //$problema->status = 'INATIVO';
            //$problema->update();

            return redirect()->back()->with('danger','Problema não encontrado.');

        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'ProblemaController@ativar';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger','Erro telemetria');
        } 
    }

    public function create(Request $request)
    {
        $this->validate($request,[
            'nome' => 'required',
            'valor' => 'required',
            'tipo' => 'required'
        ]);

        try{
            $existe = Problema::where('nome',$request->nome)->first();

            if($existe){
                return redirect()->back()->with('danger','Já existe um problema com esse nome');
            }

            $problema = new Problema;
            $problema->nome = $request->nome;

            $valor = str_replace('R$','',$request->valor);
            $valor = str_replace(',','',$valor);
            $valor = str_replace('.','',$valor);

            $problema->valor = intval($valor);
            $problema->tipo = $request->tipo;
            $problema->status = 'ATIVO';
            $problema->save();

            return redirect()->back()->with('success','Problema cadastrado com sucesso');

        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'ProblemaController@create';
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
            'tipo' => 'required',
            'problema_id' => 'required',
            'marca_id' => 'exists:modelos',
            'modelo_id' => 'exists:modelos,id'
        ]);

        try{
            $existe = Problema::where('nome',$request->nome)->where('id','!=',$request->problema_id)->first();

            if(isset($existe)){
                return redirect()->back()->with('danger','Já existe um problema com esse nome');
            }

            $problema = Problema::find($request->problema_id);
            $problema->nome = $request->nome;

            $problema->modelo_id = $request->modelo_id;

            $valor = str_replace('R$','',$request->valor);
            $valor = str_replace(',','',$valor);
            $valor = str_replace('.','',$valor);

            $problema->valor = intval($valor);
            $problema->tipo = $request->tipo;
            $problema->update();

            return redirect()->back()->with('success','Problema atualizado com sucesso');

        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'ProblemaController@create';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger','Erro telemetria');
        }
    }

    public function updateValido($id)
    {
        try{
            $ap = AparelhoProblema::findOrFail($id);

            $ap->valido = $ap->valido == 1 ? 0 : 1;
            $ap->update();
            
            return redirect()->back();

        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'ProblemaController@create';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger',$e->getMessage());
        }
    }
}
