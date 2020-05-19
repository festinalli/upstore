<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\NotificacaoController;

use App\Servico;
use App\Codigo;
use App\Telemetria;
use App\User;
use Auth;

class AparelhoEntradaController extends Controller
{
    //
    public function entradas()
    {
        $clientes = array();
        $tecnicos= array();
        $status = array();

        if(Auth::user()->tipo == 'TECNICO'){
            $servicos = Servico::where('tipo','T')->where('tecnico_id',Auth::Id())->get();
        }
        else{
            $servicos = Servico::where('tipo','T')->get();
        }

        foreach ($servicos as $key => $servico) {
            if($servico->cliente)
                array_push($clientes, $servico->cliente->nome);
            if($servico->tecnico)
                array_push($tecnicos, $servico->tecnico->nome);
            if($servico['status'])
                array_push($status, $servico['status']);
        }
        $clientes   = array_unique($clientes);
        $tecnicos   = array_unique($tecnicos);
        $status     = array_unique($status);
        return view('admin.entrada.entradas',compact('servicos','clientes','tecnicos','status'));
    }

    public function entrada($id)
    {
        try{
            $servico = Servico::findOrFail($id);
            return view('admin.entrada.entrada',compact('servico'));
        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'AparelhoEntradaController@entrada';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger','Erro telemetria');
        }
    }

    public function mercadoriaChegou($id,Request $request){
        try{
            $servico = Servico::findOrFail($id);
            $responsavel = User::where('tipo','TECNICO')->where('id',$request->responsavel_id)->first();
            if($responsavel && $servico){
                $servico->tecnico_id = $responsavel->id;
                $servico->status = 'MERCADORIA_CHEGOU';
                $servico->data_entrega = $request->data_entrega;
                $servico->chegada_data = date('Y-m-d H:i:s');
                $servico->update();

                $notificacaoController = new NotificacaoController;
                $dados = [
                    'tecnico_id' => $responsavel->id,
                    'user_id' => $servico->cliente->id,
                    'link' => $servico->id,
                    'tipo' => 'T',
                    'status' => 'MERCADORIA_CHEGOU',
                    'servico_id' => $servico->id
                ];

                //Notifica o técnico
                $notificacaoController->geraNotificacaoTecnico($dados);

                //Notifica o usuário
                $notificacaoController->geraNotificacaoUsuarioTroca($dados);
            }
            
            return redirect()->route('admin.entrada',['id'=>$servico->id])->with('success','Status alterado com sucesso.');
        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'MarcaController@ativar';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger','Erro telemetria');
        }
    }

    public function analiseOrcamentoStatus($servico_id)
    {
        try{
            $servico = Servico::findOrFail($servico_id);

            $servico->status = 'ANALISE';
            $servico->update();

            $notificacaoController = new NotificacaoController;
            $dados = [
                'user_id'    => $servico->cliente->id,
                'link'       => $servico->id,
                'tipo'       => 'T',
                'status'     => 'ANALISE',
                'servico_id' =>$servico->id
            ];

            //Notifica o usuário
            $notificacaoController->geraNotificacaoUsuarioTroca($dados);

            return redirect()->back()->with('success','Status do serviço atualizado com sucesso');

        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'VendaUsadoController@analiseOrcamentoStatus';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger',$e->getMessage());
        }
    }

    public function enviarProposta($servico_id,Request $request)
    {
        try{
            $servico = Servico::findOrFail($servico_id);

            $valor = str_replace('R$','',$request->valor_orcamento);
            $valor = str_replace(',','',$valor);
            $valor = str_replace('.','',$valor);

            $servico->status = 'PROPOSTA_ENVIADA';
            $servico->orcamento_data = date('Y-m-d H:i:s');
            $servico->valor = $valor;
            $servico->update();

            $notificacaoController = new NotificacaoController;
            $dados = [
                'user_id'    => $servico->cliente->id,
                'link'       => $servico->id,
                'tipo'       => 'T',
                'status'     => 'PROPOSTA_ENVIADA',
                'servico_id' =>$servico->id
            ];

            //Notifica o usuário
            $notificacaoController->geraNotificacaoUsuarioTroca($dados);

            return redirect()->back()->with('success','Status do serviço atualizado com sucesso');

        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'AparelhoEntradaController@enviarProposta';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger',$e->getMessage());
        }
    }

    public function gerarCodigo($id,Request $request){
        try{
            $servico = Servico::findOrFail($id);
            $cod = $servico->codigo;
            if($request->input('porcentagem')){
                $servico->deposito_cupom = 1;
                $servico->status = 'CLIENTE_ACEITOU';
                $servico->autorizacao_data = date('Y-m-d H:i:s');
                $servico->update();
                //dd($cod);
                if($cod && $cod->status == 'ATIVO'){
                    if($cod->codigo == '-') $cod->codigo = str_random(20);
                    $cod->porcentagem = $request->porcentagem;
                    $cod->valor = 0;
                    $cod->update();
                }
                else{
                    $cod = new Codigo;
                    $cod->servico_id = $servico->id;
                    $cod->codigo = str_random(20);
                    $cod->user_id = $servico->cliente->id;
                    $cod->porcentagem = $request->porcentagem;
                    $cod->valor = 0;
                    $cod->save();
                }
            }
            else if($request->input('valor')){
                $servico->deposito_cupom = 0;
                $servico->status = 'CLIENTE_ACEITOU';
                $servico->autorizacao_data = date('Y-m-d H:i:s');
                $servico->update();
                
                $valor = str_replace('R$','',$request->valor);
                $valor = str_replace(',','',$valor);
                $valor = str_replace('.','',$valor);
                if($cod && $cod->status == 'ATIVO'){
                    $cod->valor = $valor;
                    $cod->porcentagem = 0;
                    $cod->codigo = '-';
                    $cod->update();
                }
                else{
                    $cod = new Codigo;
                    $cod->servico_id = $servico->id;
                    $cod->codigo = '-';
                    $cod->user_id = $servico->cliente->id;
                    $cod->valor = $valor;
                    $cod->porcentagem = 0;
                    $cod->save();
                }
            }

            $notificacaoController = new NotificacaoController;
            $dados = [
                'user_id'    => $servico->cliente->id,
                'link'       => $servico->id,
                'tipo'       => 'T',
                'status'     => 'CLIENTE_ACEITOU',
                'servico_id' =>$servico->id
            ];

            //Notifica o usuário
            $notificacaoController->geraNotificacaoUsuarioTroca($dados);

            return redirect()->back()->with('success','Codigo gerado.');
        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'AparelhoEntradaController@gerarCodigo';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger',$e->getMessage());
        }
    }
}
