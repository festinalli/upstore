<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Envio;
use App\Telemetria;
use App\Exports\EnviosExport;
use Maatwebsite\Excel\Facades\Excel;

class EnvioController extends Controller
{
    //
    
    public function envios()
    {
        $clientes = array();
        $envios = Envio::all();


        foreach ($envios as  $envio) {
            if($envio->servico && $envio->servico->cliente)
                array_push($clientes, $envio->servico->cliente->nome);
            elseif($envio->order && $envio->order->cliente) 
                array_push($clientes, $envio->servico->cliente->nome);
        }

        $clientes   = array_unique($clientes);
        return view('admin.envios',compact('envios', 'clientes'));
    }

    public function exportar()
    {
        try{

            return Excel::download(new EnviosExport, 'envios.xlsx');

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
