<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Telemetria;
use App\Http\Controllers\UploadController;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;

class UsuarioController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
    
    public function usuarios()
    {
        $usuarios = User::where('tipo','CLIENTE')->get();
    	return view('admin.usuarios.usuarios',compact('usuarios'));
    }

    public function updatePerfil(Request $request)
    {
        // $this->validate($request,[
        //     'nome' => 'required',
        //     'email' => 'required'
        // ]);
        $userAdm = \Auth::user();
        if ($userAdm->tipo == 'ADMIN') {
        $novo = $request->input('passnovo');
        $nome = $request->input('nome');
        $conf = $request->input('passconf');
        $id = $request->input('id');

        if(is_null($novo) && is_null($conf)){
        	return redirect()->back();
        }elseif ($novo != $conf) {
             return redirect()->back()->with('danger','A nova senha e a senha de confirmação não conferem.');
        }else{
             $user = User::where('id',$id)->first();
             $user->nome = $nome;
             $user->password = \Hash::make($novo);
             $user->update();
            return redirect()->back()->with('success','Senha atualizada com sucesso.');
        }
                
       
        }
        

       
        if(! $request->foto){
            try{
                $user = \Auth::user();
                $user->nome = $request->input('nome');
                $user->email = $request->input('email');
                $user->update();
                return redirect()->back()->with('success','Perfil atualizado com sucesso.');
            }catch(\Exception $e){
                $telemetria = new Telemetria;
                $telemetria->user_id = \Auth::user()->id;
                $telemetria->metodo = 'UsuarioController@updatePerfil';
                $telemetria->descricao = $e->getMessage();
                $telemetria->save();
                return redirect()->back()->with('danger','Erro telemetria');
            }
        }

        try{
            //$upload = new UploadController;
            //$url = $upload->uploadS3($request->foto);
            $user = \Auth::user();
            //$user->foto = $url;
            $user->nome = $request->input('nome');
            $user->email = $request->input('email');
            $user->update();
            return redirect()->back()->with('success','Perfil atualizado com sucesso.');
            //dd($teste);

        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'UsuarioController@updatePerfil';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger','Erro telemetria');
        }
    }
    public function updateSenha(Request $request){
        $this->validate($request,[
            'passatual' => 'required',
            'passnovo' => 'required',
            'passconf' => 'required'
        ]);

        try{
            $nome = $request->input('nome');
            $novo = $request->input('passnovo');
            $conf = $request->input('passconf');
            $atual = $request->input('passatual');
            if(\Hash::check(\Auth::user()->password, $atual))
                return redirect()->back()->with('danger','A senha atual não confere.');
            if($novo != $conf)
                return redirect()->back()->with('danger','A nova senha e a senha de confirmação não conferem.');

            $user = \Auth::user();
            $user->nome = $nome;
            $user->password = \Hash::make($novo);
            $user->update();
            return redirect()->back()->with('success','Senha atualizada com sucesso.');

        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'UsuarioController@updateSenha';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger','Erro telemetria');
        }
    }
    public function usuario($id)
    {
        try{
            $usuario = User::where('tipo','CLIENTE')->where('id',$id)->first();

            if(!$usuario){
                return redirect()->back()->with('error','Usuário não encontrado.');
            } 

            return view('admin.usuarios.usuario',compact('usuario'));

        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'UsuarioController@usuario';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger','Erro telemetria');
        }   
        
    }

    public function ativar($user_id)
    {
        try{

            $user = User::where('id',$user_id)->where('tipo','CLIENTE')->first();

            $user->status = 'ATIVO';
            $user->update();

            return redirect()->back()->with('success','Usuário ativado com sucesso');

        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'UsuarioController@ativar';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger','Erro telemetria');
        }   
    }

    public function desativar($user_id)
    {
        try{

            $user = User::where('id',$user_id)->where('tipo','CLIENTE')->first();

            $user->status = 'INATIVO';
            $user->update();

            return redirect()->back()->with('success','Usuário desativado com sucesso');

        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'UsuarioController@desativar';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger','Erro telemetria');
        }
    }

    public function desativarTecnico($user_id)
    {
        try{

            $user = User::where('id',$user_id)->where('tipo','TECNICO')->first();

            if($user){
                $user->delete();
                return redirect()->back()->with('success','Técnico desativado com sucesso');
            }

            return redirect()->back()->with('danger','Técnico não encontrado');

        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'UsuarioController@desativar';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger','Erro telemetria');
        }
    }

    public function status($status)
    {
        try{

            $usuarios = User::where('tipo','CLIENTE')->where('status',$status)->get();
    	    return view('admin.usuarios.usuarios',compact('usuarios'));

        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'UsuarioController@status';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger','Erro telemetria');
        }
    }

    public function exportar()
    {
        try{

            return Excel::download(new UsersExport, 'users.xlsx');

        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'UsuarioController@exportar';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger',$e->getMessage());
        }
    }
}
