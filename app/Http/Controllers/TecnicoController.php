<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Telemetria;

class TecnicoController extends Controller
{
    public function tecnicos()
    {
        $tecnicos = User::where('tipo','TECNICO')->get();

        return view('admin.configuracoes.tecnicos',compact('tecnicos'));
    }

    public function create(Request $request)
    {   
        $this->validate($request,[
            'email' => 'required|unique:users',
            'nome' => 'required',
            'password' => 'required',
            'conf_password' => 'required'
        ]);

        try{

            if($request->password != $request->conf_password){
                return redirect()->back()->with('danger','Senha e confirmar senha não conferem');
            }

            $user = new User;
            $user->nome = $request->nome;
            $user->sobrenome = $request->sobrenome;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->status = 'ATIVO';
            $user->tipo = 'TECNICO';
            $user->save();

            return redirect()->back()->with('success','Técnico cadastrado com sucesso');

        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'TecnicoController@create';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger',$e->getMessage());
        }
    }

    public function ativar($user_id)
    {  
        try{

           
            $user = User::where('id',$user_id)->where('tipo','TECNICO')->first();

            if(!$user){
                return redirect()->back()->with('danger','Técnico inválido');
            }

            $user->status = 'ATIVO';
            $user->update();

            return redirect()->back()->with('success','Técnico ativado com sucesso');

        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'TecnicoController@ativar';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger',$e->getMessage());
        }
    }

    public function desativar($user_id)
    {  
        try{

           
            $user = User::where('id',$user_id)->where('tipo','TECNICO')->first();

            if(!$user){
                return redirect()->back()->with('danger','Técnico inválido');
            }

            $user->status = 'INATIVO';
            $user->update();

            return redirect()->back()->with('success','Técnico desativado com sucesso');

        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'TecnicoController@desativar';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger',$e->getMessage());
        }
    }

    public function update(Request $request)
    {   
        $this->validate($request,[
            'email' => 'required',
            'nome' => 'required',
            'user_id' => 'required'
        ]);

        try{

            $existe = User::where('id','!=',$request->user_id)->where('email',$request->email)->first();

            if($existe){
                return redirect()->back()->with('danger','Já existe um usuário com esse email');
            }

            $user = User::find($request->user_id);
            $user->nome = $request->nome;
            $user->sobrenome = $request->sobrenome;
            $user->email = $request->email;
            $user->update();

            return redirect()->back()->with('success','Técnico editado com sucesso');

        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'TecnicoController@update';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger',$e->getMessage());
        }
    }
}
