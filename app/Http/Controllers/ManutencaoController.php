<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\NotificacaoController;
use App\Http\Controllers\CorreioServicoController;
use App\Servico;
use App\Aparelho;
use App\Telemetria;
use App\User;
use App\Modelo;
use App\AparelhoAcessorio;
use App\AparelhoProblema;
use App\ObservacaoManutencao;
use App\MidiaServico;
use Carbon\Carbon;
use Auth;
use Dompdf\Dompdf;
use PDF;
class ManutencaoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function manutencoes()
    {
        $clientes = array();
        $tecnicos= array();
        $status = array();

         if(Auth::user()->tipo == 'TECNICO'){
             $manutencoes = Servico::where('tipo','M')->where('tecnico_id',Auth::Id())->get();
         }
         else{
            $manutencoes = Servico::where('tipo','M')->get();
         }

        foreach ($manutencoes as $key => $manutecao) {
            if($manutecao->cliente)
                array_push($clientes, $manutecao->cliente->nome);
            if($manutecao->tecnico)
                array_push($tecnicos, $manutecao->tecnico->nome);
            if($manutecao['status'])
                array_push($status, $manutecao['status']);
        }

        $clientes   = array_unique($clientes);
        $tecnicos   = array_unique($tecnicos);
        $status     = array_unique($status);

        return view('admin.manutencao.todas',compact('manutencoes','clientes','tecnicos','status'));
    }

    public function manutencao($id)
    {   
        $manutencao = Servico::find($id);
        dd($manutecao);
       $marcas =  Modelo::all();
        if($manutencao) return view('admin.manutencao.manutencao',compact('manutencao'));

        return redirect()->route('admin.manutencoes');
    }

    public function mercadoriaChegou($id,Request $request){
        try{
            $manutencao = Servico::findOrFail($id);
            $responsavel = User::where('tipo','TECNICO')->where('id',$request->responsavel_id)->first();
            if($responsavel && $manutencao){

                $manutencao->tecnico_id = $responsavel->id;
                $manutencao->status = 'MERCADORIA_CHEGOU';
                $manutencao->data_entrega = $request->data_entrega;
                $manutencao->chegada_data = Carbon::now();
                $manutencao->update();

                $notificacaoController = new NotificacaoController;
                $dados = [
                    'tecnico_id' => $responsavel->id,
                    'user_id' => $manutencao->cliente->id,
                    'link' => $manutencao->id,
                    'tipo' => 'M',
                    'status' => 'MERCADORIA_CHEGOU',
                    'servico_id' =>$manutencao->id
                ];

                //Notifica o técnico
                $notificacaoController->geraNotificacaoTecnico($dados);

                //Notifica o usuário
                $notificacaoController->geraNotificacaoUsuarioManutencao($dados);

                return redirect()->route('admin.manutencao',['id'=>$manutencao->id])->with('success','Status alterado com sucesso.');
                
            }
            return redirect()->back()->with('danger','Responsável não existe');
        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'MarcaController@ativar';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger','Erro telemetria');
        }
    }

    public function colocarEmAnalise($id){
        try{
            $manutencao = Servico::findOrFail($id);
            $manutencao->orcamento_data = Carbon::now();
            $manutencao->status = 'ANALISE';
            $manutencao->update();

            $notificacaoController = new NotificacaoController;
            $dados = [
                'user_id'    => $manutencao->cliente->id,
                'link'       => $manutencao->id,
                'tipo'       => 'M',
                'status'     => 'ANALISE',
                'servico_id' => $manutencao->id
            ];

            //Notifica o usuário
            $notificacaoController->geraNotificacaoUsuarioManutencao($dados);

            return redirect()->route('admin.manutencao',['id'=>$manutencao->id])->with('success','Status alterado com sucesso');
        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'MarcaController@ativar';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger','Erro telemetria');
        }
    }

    public function enviarProposta($id,Request $request){
        try{
            $manutencao = Servico::findOrFail($id);
            $valor = str_replace('R$','',$request->valor_orcamento);
            $valor = str_replace(',','',$valor);
            $valor = str_replace('.','',$valor);
            $manutencao->valor = $valor;
            $manutencao->status = 'PROPOSTA_ENVIADA';
            $manutencao->autorizacao_data = Carbon::now();
            $manutencao->update();

            $notificacaoController = new NotificacaoController;
            $dados = [
                'user_id'    => $manutencao->cliente->id,
                'link'       => $manutencao->id,
                'tipo'       => 'M',
                'status'     => 'PROPOSTA_ENVIADA',
                'servico_id' => $manutencao->id
            ];

            //Notifica o usuário
            $notificacaoController->geraNotificacaoUsuarioManutencao($dados);

            return redirect()->route('admin.manutencao',['id'=>$manutencao->id])->with('success','Status alterado com sucesso.');
        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'MarcaController@ativar';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger','Erro telemetria');
        }
    }

    public function clientePagou($id){
        try{
            $manutencao = Servico::findOrFail($id);
            $manutencao->status = 'EM_MANUTENCAO';
            $manutencao->manutencao_data = Carbon::now();
            $manutencao->update();

            $notificacaoController = new NotificacaoController;
            $dados = [
                'user_id'    => $manutencao->cliente->id,
                'link'       => $manutencao->id,
                'tipo'       => 'M',
                'status'     => 'EM_MANUTENCAO',
                'servico_id' =>$manutencao->id
            ];

            //Notifica o usuário
            $notificacaoController->geraNotificacaoUsuarioManutencao($dados);

            return redirect()->route('admin.manutencao',['id'=>$manutencao->id])->with('success','Status alterado com sucesso.');
        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'MarcaController@ativar';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger','Erro telemetria');
        }
    }

    public function finalizarManutencao($id){
        try{
            $manutencao = Servico::findOrFail($id);
            $manutencao->status = 'MANUTENCAO_FINALIZADA';
            $manutencao->entrega_data = Carbon::now();
            $manutencao->update();

            $notificacaoController = new NotificacaoController;
            $dados = [
                'user_id'    => $manutencao->cliente->id,
                'link'       => $manutencao->id,
                'tipo'       => 'M',
                'status'     => 'MANUTENCAO_FINALIZADA',
                'servico_id' =>$manutencao->id
            ];

            //Notifica o usuário
            $notificacaoController->geraNotificacaoUsuarioManutencao($dados);

            return redirect()->route('admin.manutencao',['id'=>$manutencao->id])->with('success','Status alterado com sucesso.');
        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'MarcaController@ativar';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger','Erro telemetria');
        }
    }

    public function geraChancela($servico_id,Request $request){
        try{
            $servico = Servico::findOrFail($servico_id);

            $correioController = new CorreioServicoController;
            
            //Padrao para sedex 40096
            $plp = $correioController->fecharPlpVariosServicos($servico->id);

            if($plp == -1){
                return redirect()->back()->with('danger','Parece que esse produto já faz parte de uma pré lista de postagem. Entre em contato com o administrador da plataforma para mais detalhes.');
            }
            if($plp == -2){
                return redirect()->back()->with('danger','Segundo erro.');                
            }
            return redirect()->back()->with('success','Etiquetas geradas.');
        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'RelatorioController@geraChancela';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger','Erro telemetria');
        }
    }

    public function retirarLoja($servico_id){
        try{
            $servico = Servico::findOrFail($servico_id);

            $servico->retirar_loja = 1;
            $servico->update();

            return redirect()->back();
        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'RelatorioController@retirarLoja';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger','Erro telemetria');
        }
    }

    public function downloadChancela($id){
        try{
            $servico = Servico::findOrFail($id);
            $correioController = new CorreioServicoController;
            // return 1;
            return $plp = $correioController->downloadChancela($id);

            // return redirect()->route('admin.venda',['id'=>$id]);
        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'RelatorioController@downloadChancela';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger','Erro telemetria');
        }
    }
    public function downloadOs($id){
        try{
            
            $manutencao = Servico::find($id);
            $pdf = new PDF();
            $pdf->AddPage();

            $pdf->SetXY(50, $pdf->GetY()+17);
            $pdf->Cell(60, 20, "TESTE",0);
            
            $pdf->SetXY(12, $pdf->GetY()+20);
            
            $pdf->Cell(37, 10, "Teste: ",0,0,'R');
            $pdf->Cell(85, 10, $a.'<br>',0,0,'L');
            
            $pdf->Output($a.'_2014.pdf','F');
           
          return redirect()->route('admin.venda',['id'=>$id]);
     }catch(\Exception $e){
         $telemetria = new Telemetria;
         $telemetria->user_id = \Auth::user()->id;
         $telemetria->metodo = 'RelatorioController@downloadChancela';
         $telemetria->descricao = $e->getMessage();
         $telemetria->save();

         return redirect()->back()->with('danger','Erro telemetria');
     }
    }

    public function downloadPlp($id){
        try{
            $servico = Servico::findOrFail($id);
            $correioController = new CorreioServicoController;
            $plp = $correioController->downloadPlp($id,40096);

            return redirect()->route('admin.venda',['id'=>$id]);
        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'RelatorioController@downloadChancela';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger','Erro telemetria');
        }
    }
}
