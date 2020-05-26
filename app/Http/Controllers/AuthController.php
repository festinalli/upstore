<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\User;
use App\Telemetria;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $this->validate($request,[
            'email' => 'required',
            'password' => 'required'
        ]);

        try{

            if(
                \Auth::attempt(['email'=> $request->email,'password' => $request->password,'tipo' => 'ADMIN','status' => 'ATIVO']) OR
                \Auth::attempt(['email'=> $request->email,'password' => $request->password,'tipo' => 'TECNICO','status' => 'ATIVO'])
            ){
                if(\Auth::user()->tipo == 'ADMIN'){
                    return redirect()->route('admin.usuarios');
                }
                else{
                    return redirect()->route('admin.perfil');
                }
            }else{
                return redirect()->back()->with('danger','Email ou senha inválidos')->withInput();
            }
            
            return redirect()->back()->with('danger','Email ou senha inválidos')->withInput();

        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = 0;
            $telemetria->metodo = 'AuthController@login';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger','Erro telemetria');
        }
    }
}