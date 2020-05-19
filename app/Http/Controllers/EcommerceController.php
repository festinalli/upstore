<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Categoria;
use App\Telemetria;

class EcommerceController extends Controller
{
    //
    public function categorias()
    {
        try{
            $categorias = Categoria::all();

            return view('admin.ecommerce.categorias',compact('categorias'));

        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'EcommerceController@categorias';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger','Erro telemetria');
        }

    }

    public function produtos()
    {
        return view('admin.ecommerce.produtos');
    }

    public function produto()
    {
        return view('admin.ecommerce.produto');
    }
}
