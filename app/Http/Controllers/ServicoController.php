<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\NotificacaoController;
use App\Servico;
use App\Telemetria;
use App\MidiaServico;
use App\Observacao;
use App\AparelhoAcessorio;
use App\AparelhoProblema;
use App\User;
use App\Http\Controllers\UploadController;

class ServicoController extends Controller
{
    public function midiaCreateFoto($id,Request $request)
    {
        $this->validate($request,[
            'foto' => 'required'
        ]);
        try{
            $servico = Servico::findOrFail($id);
            
            $upload = new UploadController;

            $status = $servico->status;

            $midia = new MidiaServico;
            $midia->foto = $upload->uploadS3($request->foto);
            $midia->video = null;
            $midia->status = $status;
            $midia->servico_id = $servico->id;
            $midia->save();

            $notificacaoController = new NotificacaoController;
            $dados = [
                'user_id'    => $servico->cliente->id,
                'link'       => $servico->id,
                'tipo'       => $servico->tipo,
                'status'     => 'FOTO_ADICIONADA',
                'servico_id' => $servico->id,
            ];

            //Notifica o usuário
            $notificacaoController->geraNotificacaoUsuarioFotoVideo($dados);

            return redirect()->back()->with('success','Mídia criada com sucesso.');

        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'ServicoController@midiaCreateFoto';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger',$e->getMessage());
        }
    }

    public function midiaCreateVideo($id,Request $request)
    {
        $this->validate($request,[
            'status' => 'required',
            'video' => 'required'
        ]);

        try{
            $servico = Servico::findOrFail($id);
            
            $upload = new UploadController;

            $status = $servico->status;

            $midia = new MidiaServico;
            $midia->foto = null;
            $midia->video = $upload->uploadS3($request->video);
            $midia->status = $status;
            $midia->servico_id = $servico->id;
            $midia->save();

            $notificacaoController = new NotificacaoController;
            $dados = [
                'user_id'    => $servico->cliente->id,
                'link'       => $servico->id,
                'tipo'       => $servico->tipo,
                'status'     => 'FOTO_ADICIONADA',
                'servico_id' => $servico->id,
            ];

            //Notifica o usuário
            $notificacaoController->geraNotificacaoUsuarioFotoVideo($dados);

            return redirect()->back()->with('success','Mídia criada com sucesso.');

        }catch(\Exception $e){
            dd($e);
            throw($e);

            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'ServicoController@midiaCreateVideo';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger',$e->getMessage());
        }
    }

    public function removerMidia($id){
        try{
            $midia = MidiaServico::findOrFail($id);
            $upload = new UploadController;
            if($midia->foto!=null) $upload->deleteS3($midia->foto);
            elseif($midia->video!=null) $upload->deleteS3($midia->video);

            $midia->delete();
            return redirect()->back()->with('success','Mídia removida com sucesso.');

        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'ServicoController@midiaCreateFoto';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger',$e->getMessage());
        }
    }

    public function setarResponsavel(Request $request){
        try{
            $responsavel = User::where('tipo','TECNICO')->where('id',$request->responsavel_id)->first();
            $manutencao = Servico::find($request->servico_id);
            if($responsavel && $manutencao){
                $manutencao->tecnico_id = $responsavel->id;
                $manutencao->update();
                return response()->json('success',200);
            }
            
            return response()->json('Erro ao remover',400);
        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = \Auth::user()->id;
            $telemetry->metodo = 'ManutencaoController@setarResponsavel';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json('Erro ao remover',400);
        }
    }

    public function alterarStatusAcessorio(Request $request){
        try{
            $aparelhoAcessorio = AparelhoAcessorio::find($request->id);
            
            if($aparelhoAcessorio){
                if($request->status == 'true') $aparelhoAcessorio->valido = true;
                else $aparelhoAcessorio->valido = false;
                $aparelhoAcessorio->update();
                return response()->json('success',200);
            }
            
            return response()->json('erro',400);
            
        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'ManutencaoController@alterarStatusAcessorio';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return response()->json(['erro'=>$e->getMessage()],400);
        }
    }

    public function alterarStatusProblema(Request $request){
        try{
            $aparelhoProblema = AparelhoProblema::find($request->id);
            
            if($aparelhoProblema){
                if($request->status == 'true') $aparelhoProblema->valido = true;
                else $aparelhoProblema->valido = false;
                $aparelhoProblema->update();
                return response()->json('success',200);
            }
            
            return response()->json('erro',400);
            
        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'ManutencaoController@alterarStatusProblema';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return response()->json(['erro'=>$e->getMessage()],400);
        }
    }
    
    public function adicionarObservacao($id,Request $request){
        $this->validate($request,[
            'descricao'=>'required|string|max:10000'
        ]);
        try{
            $servico = Servico::findOrFail($id);
            if($servico){
                $obs = Observacao::create([
                    'descricao'=>$request->descricao,
                    'servico_id'=>$servico->id,
                    'status'=>'STATUS'
                ]);
            }

            return redirect()->back()->with('success','Observação Adicionada.');
        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'ServicoController@adicionarObservacao';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger','Erro telemetria');
        }
    }

    public function removerObservacao($id){
        try{
            $obs = Observacao::findOrFail($id);
            if($obs){
                $obs->delete();
                return redirect()->back()->with('success','Observação removida.');
            }

            return redirect()->back()->with('error','Observação não encontrada.');
        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'MarcaController@removerObservacao';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger','Erro telemetria');
        }
    }


    public function reenviar(Request $request, $id){
        $this->validate($request,[
            'retorno_rastreio' => 'required'
        ], [
            'retorno_rastreio.required' => "Digite o código de rastreio"
        ]);

        try{

            $servico = Servico::findOrFail($id);
            $servico->retorno_rastreio = $request->retorno_rastreio; 
            $servico->update();

            $notificacaoController = new NotificacaoController;
            $dados = [
                'user_id'    => $servico->cliente->id,
                'link'       => $servico->id,
                'tipo'       => $servico->tipo,
                'status'     => 'CLIENTE_RECUSOU',
                'servico_id' => $servico->id
            ];

            //Notifica o usuário
            $notificacaoController->geraNotificacaoUsuarioManutencao($dados);

            if($servico->tipo == 'M') {
                return redirect()->route('admin.manutencao',['id'=>$servico->id])->with('success','Status alterado com sucesso.');
            }
            
            if($servico->tipo == 'V') {
                return redirect()->route('admin.servico',['id'=>$servico->id])->with('success','Status alterado com sucesso.');
            }
        }catch(\Exception $e){
            \Log::alert($e);
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'ServicoController@reenviar';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('danger','Erro telemetria');
        }
    }
}
