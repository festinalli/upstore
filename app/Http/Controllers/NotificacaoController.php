<?php

namespace App\Http\Controllers;

use App\Servico;
use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;

use App\User;
use App\Notificacao;
use App\Telemetria;
use Auth;

class NotificacaoController extends Controller
{
    private $tipoTecnico = [
        'M' => 'adm/manutencao/',
        'T' => 'adm/entrada/',
        'V' => 'adm/usado/',
    ];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function geraNotificacaoTecnico($dados){
        try{
            $tecnico = User::findOrFail($dados['tecnico_id']);
            $titulo = 'Nova Tarefa';
            $descricao = 'Foi atribuida uma nova tarefa para você';
            $tipo = $dados['tipo'];
            $servico = Servico::findOrFail($dados['servico_id']);

            $notificacao = Notificacao::create([
                'user_id'   => $tecnico->id,
                'titulo'    => $titulo,
                'descricao' => $descricao,
                'link'      => $this->tipoTecnico[$tipo].$dados['link'],
                'tipo'      => $tipo,
                'servico_id' => $servico->id,
                'icones' => 'fa fa-bell',
                'enviado' =>true
            ]);

            return $notificacao;

        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'NotificacaoController@geraNotificacaoTecnico';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return false;
        }
    }

    public function geraNotificacaoUsuarioManutencao($dados){
        try{
            $usuario = User::findOrFail($dados['user_id']);
            $titulo = '';
            $descricao = '';
            $link = '';
            $servico = Servico::findOrFail($dados['servico_id']);

            if($dados['status'] == 'MERCADORIA_CHEGOU'){
                $titulo = 'Aparelho Chegou';
                $descricao = 'Seu aparelho enviado para manutenção chegou em nossa loja.';
                $link = 'acompanharmanutencao/'.$dados['link'];
            }
            elseif($dados['status'] == 'ANALISE'){
                $titulo = 'Aparelho em análise';
                $descricao = 'Seu aparelho foi enviado para análise.';
                $link = 'acompanharmanutencao/'.$dados['link'];
            }
            elseif($dados['status'] == 'PROPOSTA_ENVIADA'){
                $titulo = 'Proposta de manutenção';
                $descricao = 'Uma proposta de manutenção foi enviada.';
                $link = 'acompanharmanutencao/'.$dados['link'];
            }
            elseif($dados['status'] == 'EM_MANUTENCAO'){
                $titulo = 'Aparelho em manutenção';
                $descricao = 'Um aparelho está em manutenção.';
                $link = 'acompanharmanutencao/'.$dados['link'];
            }
            elseif($dados['status'] == 'MANUTENCAO_FINALIZADA'){
                $titulo = 'Aparelho em manutenção';
                $descricao = 'Um aparelho está em manutenção.';
                $link = 'acompanharmanutencao/'.$dados['link'];
            }
            $notificacao = Notificacao::create([
                'user_id'   => $usuario->id,
                'titulo'    => $titulo,
                'descricao' => $descricao,
                'link'      => $link,
                'tipo'      => 'M',
                'icones' => 'fa fa-bell',
                'servico_id' => $servico->id
            ]);

            return $notificacao;

        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'NotificacaoController@geraNotificacaoUsuarioManutencao';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return false;
        }
    }

    public function geraNotificacaoUsuarioTroca($dados){
        try{
            $usuario = User::findOrFail($dados['user_id']);
            $titulo = '';
            $descricao = '';
            $link = '';
            $servico = Servico::findOrFail($dados['servico_id']);

            if($dados['status'] == 'MERCADORIA_CHEGOU'){
                $titulo = 'Aparelho Chegou';
                $descricao = 'Seu aparelho enviado para troca chegou em nossa loja.';
                $link = 'acompanharvenda/'.$dados['link'];
            }
            elseif($dados['status'] == 'ANALISE'){
                $titulo = 'Aparelho em análise';
                $descricao = 'Seu aparelho foi enviado para análise.';
                $link = 'acompanharvenda/'.$dados['link'];
            }
            elseif($dados['status'] == 'PROPOSTA_ENVIADA'){
                $titulo = 'Proposta de troca';
                $descricao = 'Uma proposta de troca foi enviada.';
                $link = 'acompanharvenda/'.$dados['link'];
            }
            elseif($dados['status'] == 'CLIENTE_ACEITOU'){
                $titulo = 'Troca Aceita';
                $descricao = 'A troca do seu telefone foi aceita.';
                $link = 'acompanharvenda/'.$dados['link'];
            }
            
            $notificacao = Notificacao::create([
                'user_id'   => $usuario->id,
                'titulo'    => $titulo,
                'descricao' => $descricao,
                'link'      => $link,
                'tipo'      => 'T',
                'icones' => 'fa fa-bell',
                'servico_id' => $servico->id
            ]);

            return $notificacao;

        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'NotificacaoController@geraNotificacaoUsuarioTroca';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return false;
        }
    }

    public function geraNotificacaoUsuarioVenda($dados){
        try{
            $usuario = User::findOrFail($dados['user_id']);
            $titulo = '';
            $descricao = '';
            $link = '';
            $servico = Servico::findOrFail($dados['servico_id']);

            if($dados['status'] == 'MERCADORIA_CHEGOU'){
                $titulo = 'Aparelho Chegou';
                $descricao = 'Seu aparelho enviado para venda chegou em nossa loja.';
                $link = 'acompanharvenda/'.$dados['link'];
            }
            elseif($dados['status'] == 'ANALISE'){
                $titulo = 'Aparelho em análise';
                $descricao = 'Seu aparelho foi enviado para análise.';
                $link = 'acompanharvenda/'.$dados['link'];
            }
            elseif($dados['status'] == 'PROPOSTA_ENVIADA'){
                $titulo = 'Proposta de troca';
                $descricao = 'Uma proposta de venda foi enviada.';
                $link = 'acompanharvenda/'.$dados['link'];
            }
            elseif($dados['status'] == 'PAGAMENTO_REALIZADO'){
                $titulo = 'Depósito Feito';
                $descricao = 'O depósito do aparelho foi feito em sua conta bancária.';
                $link = 'acompanharvenda/'.$dados['link'];
            }
            
            $notificacao = Notificacao::create([
                'user_id'   => $usuario->id,
                'titulo'    => $titulo,
                'descricao' => $descricao,
                'link'      => $link,
                'tipo'      => 'V',
                'icones' => 'fa fa-bell',
                'servico_id' => $servico->id
            ]);

            return $notificacao;

        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'NotificacaoController@geraNotificacaoUsuarioVenda';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return false;
        }
    }

    public function geraNotificacaoUsuarioPromocao($dados){
        try{
            $usuario = User::findOrFail($dados['user_id']);
            $titulo = 'Produto em Promoção';
            $descricao = 'Foi adicionado uma promoção a um produto.';
            $link = 'produto/'.$dados['produto_id'];
            $servico = Servico::findOrFail($dados['servico_id']);
            
            $notificacao = Notificacao::create([
                'user_id'   => 0,
                'titulo'    => $titulo,
                'descricao' => $descricao,
                'link'      => $link,
                'tipo'      => 'P',
                'icones' => 'fa fa-bell',
                'servico_id' => $servico->id
            ]);

            return $notificacao;

        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'NotificacaoController@geraNotificacaoUsuarioPromocao';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return false;
        }
    }

    public function geraNotificacaoUsuarioFotoVideo($dados){
        try{
            $servico = Servico::findOrFail($dados['servico_id']);
            $usuario = User::findOrFail($dados['user_id']);
            $titulo = '';
            $descricao = '';
            if($dados['tipo'] == 'M'){
                $link = 'acompanharmanutencao/'.$dados['link'];
            }
            else{
                $link = 'acompanharvenda/'.$dados['link'];
            }

            if($dados['status'] == 'FOTO_ADICIONADA'){
                $titulo = 'Foto adicionada';
                $descricao = 'Nova foto adicionada de seu aparelho.';
            }
            elseif($dados['status'] == 'VIDEO_ADICIONADO'){
                $titulo = 'Vídeo adicionado';
                $descricao = 'Novo vídeo adicionado de seu aparelho.';
            }
            
            $notificacao = Notificacao::create([
                'user_id'   => $usuario->id,
                'titulo'    => $titulo,
                'descricao' => $descricao,
                'link'      => $link,
                'tipo'      => 'U',
                'icones' => 'fa fa-bell',
                'servico_id' => $servico->id
            ]);

            return $notificacao;

        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'NotificacaoController@geraNotificacaoUsuarioVenda';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return false;
        }
    }

    public function visualizarNotificacao(Request $request){
        
        try{
            $notificacao = Notificacao::findOrFail($request->notificacao_id);
            $notificacao->lido = 1;
            $notificacao->update();
            $rota = url('/').'/'.$notificacao->link;
            return redirect($rota);

        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'NotificacaoController@visualizarNotificacao';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            return redirect()->back()->with('error','Erro telemetria');
        }
    }
}
