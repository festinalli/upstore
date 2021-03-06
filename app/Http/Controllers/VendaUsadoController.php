<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\NotificacaoController;
use App\Servico;
use App\MidiaServico;
use App\User;
use App\Telemetria;
use Auth;

class VendaUsadoController extends Controller
{
    //
    public function vendaSeuUsado()
    {
        $clientes = array();
        $tecnicos= array();
        $status = array();

        if(\Auth::user()->tipo == 'ADMIN'){
            
            $servicos = Servico::where('tipo','V')->get();

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

            return view('admin.usados.vendaSeuUsado',compact('servicos','clientes','tecnicos','status'));
        }
        
        $servicos = Servico::where('tipo','V')->where('tecnico_id',\Auth::user()->id)->get();
        foreach ($servicos as $key => $servico) {
            array_push($clientes, $servico->cliente->nome);
            if($servico['status'])
                array_push($status, $servico['status']);
        }

        $clientes   = array_unique($clientes);

        $status     = array_unique($status);

        return view('admin.usados.vendaSeuUsado',compact('servicos','clientes','tecnicos','status'));

        
    }

    public function seuUsado($servico_id)
    {
        $servico = Servico::find($servico_id);
        
        if($servico->tipo != 'V'){
            return redirect()->back();
        }

        $midiasMercadoria = MidiaServico::where('servico_id',$servico->id)->where('status','MERCADORIA')->get();
        
        return view('admin.usados.seuUsado',compact('servico','midiasMercadoria'));
    }

    public function mercadoriaChegou($servico_id,Request $request)
    {
        try{
            $servico = Servico::findOrFail($servico_id);
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
                    'tipo' => 'V',
                    'status' => 'MERCADORIA_CHEGOU',
                    'servico_id'=>$servico->id
                ];

                //Notifica o técnico
                $notificacaoController->geraNotificacaoTecnico($dados);

                //Notifica o usuário
                $notificacaoController->geraNotificacaoUsuarioVenda($dados);

                return redirect()->route('admin.usado',['id'=>$servico->id])->with('success','Status alterado com sucesso.');
            }

            return redirect()->back()->with('danger','Responsável não existe');

        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'VendaUsadoController@marcadoriaChegou';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger',$e->getMessage());
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
                'tipo'       => 'V',
                'status'     => 'ANALISE',
                'servico_id' => $servico->id
            ];

            //Notifica o usuário
            $notificacaoController->geraNotificacaoUsuarioVenda($dados);

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
                'tipo'       => 'V',
                'status'     => 'PROPOSTA_ENVIADA',
                'servico_id' =>$servico->id
            ];

            //Notifica o usuário
            $notificacaoController->geraNotificacaoUsuarioVenda($dados);

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

    public function pagamentoRealizado($servico_id)
    {
        try{
            $servico = Servico::findOrFail($servico_id);

            $servico->status = 'PAGAMENTO_REALIZADO';
            $servico->entrega_data = date('Y-m-d H:i:s' );
            $servico->update();

            $notificacaoController = new NotificacaoController;
            $dados = [
                'user_id'    => $servico->cliente->id,
                'link'       => $servico->id,
                'tipo'       => 'V',
                'status'     => 'PAGAMENTO_REALIZADO',
                'servico_id' => $servico->id
            ];

            //Notifica o usuário
            $notificacaoController->geraNotificacaoUsuarioVenda($dados);

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

}
