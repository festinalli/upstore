@extends('admin.layout')

@section('css')
    <style type="text/css">
        .bootstrap-switch .bootstrap-switch-handle-on{
            background-color: green !important;
        }
        .bootstrap-switch .bootstrap-switch-handle-off{
            background-color: red !important;
        }

        .bootstrap-switch.bootstrap-switch-off .bootstrap-switch-label {
            background-color: #fff !important;
        }
    </style>
@endsection

@section('main')
<div class="main-panel">
    <!-- Navbar -->
    @include('admin.navbar', ['title' => '#'.$manutencao->id])
    <!-- End Navbar -->


<div class="panel-header panel-header-sm"></div>

<div class="content">
<div class="row">
<div class="col-md-12 ml-auto mr-auto">
    @if(session('success'))
        <div class="alert alert-success">
            <b>{{ session('success') }}</b>
        </div>
    @endif

    @if(session('danger'))
        <div class="alert alert-danger">
            <b>{{ session('danger') }}</b>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="card card-plain card-subcategories">
        <div class="card-body">
            <ul class="nav nav-pills nav-pills-primary nav-pills-icons justify-content-center" role="tablist">
                @if($manutencao->status == 'CRIADO' || $manutencao->status == 'MERCADORIA_CHEGOU' || $manutencao->status == 'ANALISE' 
                    || $manutencao->status == 'PROPOSTA_ENVIADA' || $manutencao->status == 'CLIENTE_ACEITOU' || $manutencao->status == 'CLIENTE_RECUSOU' || $manutencao->status == 'EM_MANUTENCAO' || $manutencao->status == 'MANUTENCAO_FINALIZADA')
                <li class="nav-item">
                    <a class="nav-link @if($manutencao->status == 'CRIADO') active @endif" data-toggle="tab" href="#link7" role="tablist">
                        <i class="now-ui-icons tech_mobile"></i> Aparelho e Cliente
                        </a>
                    </li>
                @endif
                @if($manutencao->status == 'MERCADORIA_CHEGOU' || $manutencao->status == 'ANALISE' 
                || $manutencao->status == 'PROPOSTA_ENVIADA' || $manutencao->status == 'CLIENTE_ACEITOU' || $manutencao->status == 'CLIENTE_RECUSOU' || $manutencao->status == 'EM_MANUTENCAO' || $manutencao->status == 'MANUTENCAO_FINALIZADA')
                <li class="nav-item">
                    <a class="nav-link @if($manutencao->status == 'MERCADORIA_CHEGOU') active @endif" data-toggle="tab" href="#link10" role="tablist">
                        <i class="now-ui-icons shopping_box"></i> Chegada da mercadoria
                        </a>
                    </li>
                @endif
                @if($manutencao->status == 'ANALISE' || $manutencao->status == 'PROPOSTA_ENVIADA' || $manutencao->status == 'CLIENTE_ACEITOU' || $manutencao->status == 'CLIENTE_RECUSOU' || $manutencao->status == 'EM_MANUTENCAO' || $manutencao->status == 'MANUTENCAO_FINALIZADA')
                <li class="nav-item">
                    <a class="nav-link @if($manutencao->status == 'ANALISE') active @endif" data-toggle="tab" href="#link8" role="tablist">
                        <i class="now-ui-icons shopping_delivery-fast"></i> Análise e orçamento
                        </a>
                    </li>
                @endif
                @if($manutencao->status == 'PROPOSTA_ENVIADA' || $manutencao->status == 'CLIENTE_ACEITOU' || $manutencao->status == 'CLIENTE_RECUSOU' || $manutencao->status == 'EM_MANUTENCAO' || $manutencao->status == 'MANUTENCAO_FINALIZADA')
                <li class="nav-item">
                    <a class="nav-link @if($manutencao->status == 'PROPOSTA_ENVIADA' || $manutencao->status == 'CLIENTE_ACEITOU' || $manutencao->status == 'CLIENTE_RECUSOU') active @endif" data-toggle="tab" href="#link9" role="tablist">
                        <i class="now-ui-icons ui-1_check"></i> Autorização do cliente
                        </a>
                    </li>
                   
                @endif
                @if($manutencao->status == 'EM_MANUTENCAO' || $manutencao->status == 'MANUTENCAO_FINALIZADA')
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#link12" role="tablist">
                        <i class="now-ui-icons ui-2_settings-90"></i> Manutenção
                        </a>
                    </li>
                @endif
            </ul>

            <div class="tab-content tab-space tab-subcategories">
<!-- ====================================== ENVIADO ====================================== -->
                <div class="tab-pane @if($manutencao->status == 'CRIADO') active @endif" id="link7">
                    <div class="row">
                        <div class="col-md-12">
                            <form action="{{ route('admin.manutencao.mercadoriachegou',['servico_id' => $manutencao->id]) }}" method="post" style="width:100%">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Técnico</label>
                                            <select class="selectpicker form-control" data-style="btn btn-default btn-round" title="Selecione o responsável" data-size="7" @if($manutencao->status != 'CRIADO') disabled @endif name="responsavel_id">
                                                @foreach(\App\User::where('tipo','TECNICO')->get() as $u)
                                                    <option value="{{$u->id}}" @if($manutencao->tecnico_id == $u->id) selected @endif>
                                                        {{$u->nome}} {{$u->sobrenome}}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Data Entrega</label>
                                            <input class="form-control" type="date" id="data_entrega" name="data_entrega" style="margin:10px 0; padding: 5px 10px;font-size:1.29em" @if($manutencao->status != 'CRIADO') readonly @endif @if($manutencao->data_entrega) value="{{date('Y-m-d',strtotime($manutencao->data_entrega))}}" @endif>
                                        </div>
                                    </div>
                                   
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Ordem de Serviço:</label>
                                            <a href="{{route('admin.manutencao.downloadOs',['id'=>$manutencao->id])}}" onclick="">Download OS </a>
                                                Download OS
                                            </a></p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            @if($manutencao->status == 'CRIADO')
                                            <button type="submit" class="btn btn-danger pull-right" style="color: white;margin:33px 20px 0px 33px">Mercadoria Chegou</button>
                                            @endif
                                        </div>
                                    </div>

                                </div>
                            </form>
                        </div>
                        <div class="col-md-12">
                            <form>
                            <div class="col-md-12 pr-1">
                                <div class="form-group">
                                <label>Cliente</label>
                                <p><a href="#">{{$manutencao->cliente->nome}} {{$manutencao->cliente->sobrenome}} </a></p>
                                </div>
                            </div>
                            <div class="col-md-12 pr-1">
                                <div class="form-group">
                                <label>Marca</label>
                                <input type="text" class="form-control" disabled="" value="{{$manutencao->aparelho->capacidade->modelo->marca->nome}}">
                                </div>
                            </div>
                            <div class="col-md-12 pr-1">
                                <div class="form-group">
                                <label>Modelo</label>
                                <input type="text" class="form-control" disabled="" value="{{$manutencao->aparelho->capacidade->modelo->nome}}">
                                </div>
                            </div>
                            <div class="col-md-12 pr-1">
                                <div class="form-group">
                                <label>Capacidade</label>
                                <input type="text" class="form-control" disabled="" value="R$ {{ $manutencao->aparelho->capacidade->valor ? str_replace('.', ',', $manutencao->aparelho->capacidade->valor/100) : 0,00 }}">
                                </div>
                            </div>
                            <div class="col-md-12 pr-1">
                                <div class="form-group">
                                <label>Pré-orçamento:</label>
                                <input type="text" class="form-control" disabled="" value="R$ {{ $manutencao->valor ? str_replace('.', ',', $manutencao->valor/100) : 0,00 }}">
                                </div>
                            </div>
                            <div class="col-md-12 pr-1">
                                <div class="form-group">
                                <label>Acessos e senhas:</label>
                                <p>{{$manutencao->aparelho->senha}}</p>
                                </div>
                            </div>
                            <div class="col-md-12 pr-1">
                                <div class="form-group">
                                <label>Login na loja (Google Play ou Apple Store):</label>
                                <p>{{ $manutencao->aparelho->loja_login ? $manutencao->aparelho->loja_login : 'Não informado' }}</p>
                                </div>
                            </div>
                            <div class="col-md-12 pr-1">
                                <div class="form-group">
                                <label>Senha na loja (Google Play ou Apple Store):</label>
                                <p>{{ $manutencao->aparelho->loja_senha ?  $manutencao->aparelho->loja_senha : 'Não infomado' }}</p>
                                </div>
                            </div>
                            <div class="col-md-12 pr-1">
                                <div class="form-group">
                                <label>Especificações de outros problemas:</label>
                                <p>{{$manutencao->descricao}}</p>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Método de coleta selecionado pelo cliente</label>
                                    <input type="text" class="form-control" disabled="" value="{{$manutencao->metodo}}">
                                </div>
                            </div>
                            @if($manutencao->loja_id)
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Loja selecionada</label>
                                    
                                    <input type="text" class="form-control" disabled="" value="{{$manutencao->loja->titulo}}">
                                    
                                </div>
                            </div>
                            @endif
                            </form>
                        </div>
                        <div class="col-md-6">
                            <table id="datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Acessórios</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($manutencao->aparelho->acessorios as $a)
                                    <tr>
                                        <td>{{$a->acessorio->nome}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table id="datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Problemas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($manutencao->aparelho->problemas as $p)
                                    <tr>
                                        <td>{{$p->problema->nome}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
<!-- ====================================== CHEGOU ====================================== -->
                <div class="tab-pane @if($manutencao->status == 'MERCADORIA_CHEGOU') active @endif" id="link10">
                    <div class="row">
                        
                        @if($manutencao->status == 'MERCADORIA_CHEGOU')
                        <div class="col-md-12">
                            <a href="{{route('admin.manutencao.emanalise',['id'=>$manutencao->id])}}" class="btn btn-danger pull-right" style="color: white;">Próximo >></a>
                        </div>
                        @endif
                        <div class="col-md-12">
                            <form>
                            <div class="row">
                                <div class="col-md-4"></div>
                                <div class="col-md-8">
                                    @if($manutencao->status == 'MERCADORIA_CHEGOU')
                                        <a class="btn btn-info pull-right" style="color: white;" data-toggle="modal" data-target="#foto">Adicionar nova foto</a>
                                        <a class="btn btn-info pull-right" style="color: white;" data-toggle="modal" data-target="#video">Adicionar novo video</a>
                                    @endif

                                    <table id="datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>Foto / Video</th>
                                                @if($manutencao->status == 'MERCADORIA_CHEGOU')
                                                    <th class="disabled-sorting text-right">Excluir</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($manutencao->midia as $f)
                                            @if($f->status=='MERCADORIA_CHEGOU')
                                            <tr>
                                                @if($f->foto!=null)
                                                <td>
                                                    <img src="{{$f->foto}}" style="max-width:150px">
                                                </td>
                                                @endif
                                                @if($f->video!=null)
                                                <td>
                                                    <a href="{{$f->video}}">{{$f->video}}</a>
                                                </td>
                                                @endif
                                                @if($manutencao->status == 'MERCADORIA_CHEGOU')
                                                <td class="text-right">
                                                <a href="{{route('admin.servico.removermidia',['id'=>$f->id])}}" class="btn btn-round btn-danger btn-icon btn-sm remove"><i title="Desativar" class="fas fa-times"></i></a>
                                                </td>
                                                @endif
                                            </tr>
                                            @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
<!-- ====================================== ANALISE ====================================== -->
                <div class="tab-pane @if($manutencao->status == 'ANALISE') active @endif" id="link8">
                    <div class="row">
                        <div class="col-md-6">
                            @if($manutencao->status == 'ANALISE')
                            <b>Pré-orçamento: R$ {{ $manutencao->valor ? number_format(str_replace('.', ',', $manutencao->valor/100),2,',','.') : 0,00 }}</b>
                            <a class="btn btn-danger pull-right" style="color: white;" data-toggle="modal" data-target="#proposta">Enviar proposta</a>
                            @else
                            <b>Orçamento: R$ {{number_format($manutencao->valor/100,2,',','.')}}</b>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Data Entrega</label>
                                <input class="form-control" type="date" id="data_entrega" name="data_entrega" style="margin:10px 0; padding: 5px 10px;font-size:1.29em" readonly @if($manutencao->data_entrega) value="{{date('Y-m-d',strtotime($manutencao->data_entrega))}}" @endif>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <table id="datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Acessórios</th>
                                        <th class="disabled-sorting text-right">Confirmar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($manutencao->aparelho->acessorios as $a)
                                    <tr>
                                        <td>{{$a->acessorio->nome}}</td>
                                        <td class="text-right">
                                            @if($a->valido == 1)
                                                <a href="{{ route('admin.acessorio.update.valido',['id' => $a->id]) }}" class="btn btn-danger btn-xs" style="color: white;"><b>Desconfirmar</b></a>
                                            @else
                                                <a href="{{ route('admin.acessorio.update.valido',['id' => $a->id]) }}" class="btn btn-info btn-xs" style="color: white;"><b>Confirmar</b></a>
                                            @endif
                                        </td>
                                    </tr>   
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table id="datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th>Problemas</th>
                                    <th>Confirmado?</th>
                                    <th class="disabled-sorting text-right">Confirmar</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($manutencao->aparelho->problemas as $p)
                                <tr>
                                    <td>{{$p->problema->nome}}</td>
                                    @if($p->valido == 1)
                                        <td class="text-center"><b style="color: green;">Sim</b></td>
                                    @else
                                        <td class="text-center"><b style="color: red;">Não</b></td>
                                    @endif
                                    <td class="text-right">
                                        @if($p->valido == 1)
                                            <a href="{{ route('admin.problema.update.valido',['id' => $p->id]) }}" class="btn btn-danger btn-xs" style="color: white;"><b>Desconfirmar</b></a>
                                        @else
                                            <a href="{{ route('admin.problema.update.valido',['id' => $p->id]) }}" class="btn btn-info btn-xs" style="color: white;"><b>Confirmar</b></a>
                                        @endif
                                    </td>
                                </tr>   
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            @if($manutencao->status == 'ANALISE')
                                <a class="btn btn-info pull-right" style="color: white;" data-toggle="modal" data-target="#observacao">Adicionar observação</a>
                            @endif
                            <table id="datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Observações</th>
                                        @if($manutencao->status == 'ANALISE')
                                        <th class="disabled-sorting text-right">Excluir</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($manutencao->observacoes as $o)
                                    <tr>
                                        <td>{{$o->descricao}}</td>
                                        @if($manutencao->status == 'ANALISE')
                                        <td class="text-right">
                                            <a href="{{route('admin.servico.removerobservacao',['id'=>$o->id])}}" class="btn btn-round btn-danger btn-icon btn-sm remove"><i title="Desativar" class="fas fa-times"></i></a>
                                        </td>
                                        @endif
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            @if($manutencao->status == 'ANALISE')
                                <a class="btn btn-info pull-right" style="color: white;" data-toggle="modal" data-target="#foto">Adicionar nova foto</a>
                                <a class="btn btn-info pull-right" style="color: white;" data-toggle="modal" data-target="#video">Adicionar novo video</a>
                            @endif

                            <table id="datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Foto / Video</th>
                                        @if($manutencao->status == 'ANALISE')
                                            <th class="disabled-sorting text-right">Excluir</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($manutencao->midia as $f)
                                    @if($f->status=='ANALISE')
                                    <tr>
                                        @if($f->foto!=null)
                                        <td>
                                            <img src="{{$f->foto}}" style="max-width:150px">
                                        </td>
                                        @endif
                                        @if($f->video!=null)
                                        <td>
                                            <a href="{{$f->video}}">{{$f->video}}</a>
                                        </td>
                                        @endif
                                        @if($manutencao->status == 'ANALISE')
                                        <td class="text-right">
                                        <a href="{{route('admin.servico.removermidia',['id'=>$f->id])}}" class="btn btn-round btn-danger btn-icon btn-sm remove"><i title="Desativar" class="fas fa-times"></i></a>
                                        </td>
                                        @endif
                                    </tr>
                                    @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
<!-- ====================================== PROPOSTA_ENVIADA ====================================== -->
                <div class="tab-pane 
                    @if($manutencao->status == 'PROPOSTA_ENVIADA' || $manutencao->status == 'CLIENTE_ACEITOU' || $manutencao->status == 'CLIENTE_RECUSOU') active @endif" 
                    id="link9">
                    <div class="row">
                        <div class="col-md-6 pr-1">
                            <div class="form-group">
                            <label>Status:</label>
                            <p><b>
                                @if($manutencao->status == 'PROPOSTA_ENVIADA'  && !$manutencao->orderServico)
                                    Aguardando resposta do cliente
                                    <br>
                                    {{ $manutencao->orderServico }}
                                @elseif($manutencao->status == 'PROPOSTA_ENVIADA' && $manutencao->orderServico)
                                    Cliente aceitou, aguardando pagamento.
                                @elseif($manutencao->status == 'CLIENTE_ACEITOU')
                                    Cliente Aceitou
                                    @if($manutencao->order && $manutencao->order->status =='AGUARDANDO_PAGAMENTO') / Aguardando Pagamento 
                                    @elseif($manutencao->order && $manutencao->order->status =='PAGO') / Pago
                                    @endif
                                @elseif($manutencao->status == 'CLIENTE_RECUSOU')
                                    Cliente Recusou
                                @endif
                            </b></p>
                            </div>
                            <div class="form-group">
                            <a href="{{route('admin.manutencao.clientepagou',['id'=>$manutencao->id])}}" onclick="">Cliente Autorizouuuuu </a>
                             </div>
                             
                        </div>
                        <div class="col-md-6">
                            @if($manutencao->status == 'PROPOSTA_ENVIADA' || $manutencao->status == 'CLIENTE_ACEITOU')
                                @if($manutencao->order && $manutencao->order->status == 'PAGO')
                                <a href="{{route('admin.manutencao.clientepagou',['id'=>$manutencao->id])}}" class="btn btn-danger pull-right" style="color: white;">Pagamento realizado</a>
                                @endif
                            @elseif($manutencao->status == 'CLIENTE_RECUSOU')
                                @if($manutencao->retorno_rastreio)
                                    <a class="btn btn-success pull-right" style="color: white;">Rastreio enviado ({{ $manutencao->retorno_rastreio}})</a>
                                @else
                                    <a class="btn btn-danger pull-right" style="color: white;" data-toggle="modal" data-target="#reenviar">Reenviar Aparelho</a>
                                @endif
                            @endif
                        </div>
                        @if($manutencao->status == 'CLIENTE_RECUSOU')
                        <div class="col-12">
                            <strong>Dados do cliente</strong>
                        </div>
                        <div class="col-12">Nome: {{ $manutencao->cliente->nome }}</div>
                        <div class="col-12">Telefone: {{ $manutencao->cliente->telefone }}</div>
                        <div class="col-12">Endereços:</div>

                            @foreach($manutencao->cliente->enderecos as $endereco)
                                <div class="col-3" style="padding: 20px; @if($endereco->isPrincipal()) font-weight: bold; @endif">
                                    {{ $endereco->rua . ', ' . $endereco->numero . '.' }}<br>
                                    @if($endereco->complemento) {{ $endereco->complemento }}<br> @endif
                                    {{ $endereco->bairro }}<br>
                                    {{ $endereco->cidade . ', ' . $endereco->estado }}<br>
                                    {{ $endereco->cep }}<br>
                                    @if($endereco->isPrincipal()) (principal) @endif
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
<!-- ====================================== EM_MANUTENCAO ====================================== -->
                <div class="tab-pane @if($manutencao->status == 'EM_MANUTENCAO' || $manutencao->status == 'MANUTENCAO_FINALIZADA') active @endif" id="link12">
                    <div class="row">
                        @if($manutencao->status == 'EM_MANUTENCAO')
                        <div class="col-md-12">
                        <a href="{{route('admin.manutencao.finalizarmanutencao',['id'=>$manutencao->id])}}" class="btn btn-danger pull-right" id="finalizar_manutencao" style="color: white;">Manutenção finalizada</a>
                        </div>
                        @endif
                        
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-4">
                                    @if($manutencao->status == 'MANUTENCAO_FINALIZADA' && $manutencao->envio)
                                        <div class="form-group">
                                            <label>Chancela:</label>
                                            <p><a href="{{route('admin.manutencao.downloadChancela',['id'=>$manutencao->id])}}" target="_blank" style="color: blue;">
                                                Download
                                            </a></p>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Lista Postagem:</label>
                                            <p>
                                                <a href="{{route('admin.manutencao.downloadPlp',['id'=>$manutencao->id])}}" target="_blank" style="color: blue;">
                                                    Download
                                                </a>
                                            </p>
                                        </div>
                                    @elseif($manutencao->status == 'MANUTENCAO_FINALIZADA' && $manutencao->retirar_loja == 1)
                                        <div class="form-group">
                                            <h3>Cliente irá retirar produto na loja</h3>
                                        </div>
                                    @elseif($manutencao->status == 'MANUTENCAO_FINALIZADA' && !$manutencao->envio)
                                        <div class="form-group">
                                            <a href="{{ route('admin.manutencao.gerachancela',['servico_id'=>$manutencao->id]) }}" class="btn btn-primary" onclick="event.preventDefault();document.getElementById('gerar').submit();">
                                                Clique para gerar chancela / etiqueta de envio
                                            </a>
                                            <a href="#" class="btn btn-info" data-toggle="modal" data-target="#retirarLoja">
                                                Cliente vai retirar na loja
                                            </a>
                                            <form id="gerar" action="{{ route('admin.manutencao.gerachancela',['servico_id'=>$manutencao->id]) }}" method="POST" style="display: none;">
                                                @csrf
                                            </form>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-8">
                                    @if($manutencao->status == 'EM_MANUTENCAO')
                                        <a class="btn btn-info pull-right" style="color: white;" data-toggle="modal" 
                                        data-target="#fotofinal">Adicionar nova foto</a>
                                        <a class="btn btn-info pull-right" style="color: white;" data-toggle="modal" data-target="#videofinal">Adicionar novo vídeo</a>
                                    @endif
                                    <table id="datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>Foto / Video</th>
                                                @if($manutencao->status == 'EM_MANUTENCAO')
                                                <th class="disabled-sorting text-right">Excluir</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($manutencao->midia as $f)
                                            @if($f->status=='EM_MANUTENCAO')
                                            <tr>
                                                @if($f->foto!=null)
                                                <td>
                                                    <img src="{{$f->foto}}" style="max-width:150px">
                                                </td>
                                                @endif
                                                @if($f->video!=null)
                                                <td>
                                                    <a href="{{$f->video}}">{{$f->video}}</a>
                                                </td>
                                                @endif
                                                @if($manutencao->status == 'EM_MANUTENCAO')
                                                <td class="text-right">
                                                <a href="{{route('admin.servico.removermidia',['id'=>$f->id])}}" class="btn btn-round btn-danger btn-icon btn-sm remove"><i title="Desativar" class="fas fa-times"></i></a>
                                                </td>
                                                @endif
                                            </tr>
                                            @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
        </div>
    </div>
    </div>
</div>


<div class="modal fade" id="foto" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Nova Foto</h5>
            </div>
            <form action="{{route('admin.servico.uploadfoto',['id'=>$manutencao->id])}}" method="post" enctype="multipart/form-data">
                {{csrf_field()}}
                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-12 pr-1">
                            <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                                <div class="fileinput-new thumbnail">
                                    <img src="{{ asset('admin/assets/img/image_placeholder.jpg') }}" alt="...">
                                </div>
                                <div class="fileinput-preview fileinput-exists thumbnail"></div>
                                <div>
                                    <span class="btn btn-rose btn-round btn-file">
                                    <span class="fileinput-new">Selecione a imagem</span>
                                    <span class="fileinput-exists">Alterar</span>
                                    <input type="file" name="foto" required />
                                    <input type="hidden" name="status" value="MERCADORIA_CHEGOU" />
                                    </span>
                                    <a href="#pablo" class="btn btn-danger btn-round fileinput-exists" data-dismiss="fileinput"><i class="fa fa-times"></i> Cancelar</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Adicionar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="retirarLoja" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Uma vez setado que o cliente irá retirar na loja, não poderá mais gerar a etiqueta / chancela pelo correios</h5>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><b>Cancelar</b></button>
                <a href="{{ route('admin.manutencao.retirar.loja',['servico_id' => $manutencao->id]) }}" class="btn btn-success"><b>Estou ciente</b></a>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="video" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Novo Vídeo</h5>
            </div>
            <form action="{{route('admin.servico.uploadvideo',['id'=>$manutencao->id])}}" method="post" enctype="multipart/form-data">
                {{csrf_field()}}
                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-12 pr-1">
                            <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                                <div class="fileinput-new thumbnail">
                                    <img src="{{ asset('admin/assets/img/image_placeholder.jpg') }}" alt="...">
                                </div>
                                <div class="fileinput-preview fileinput-exists thumbnail"></div>
                                <div>
                                    <span class="btn btn-rose btn-round btn-file">
                                    <span class="fileinput-new">Selecione o video</span>
                                    <span class="fileinput-exists">Alterar</span>
                                    <input type="file" name="video" required />
                                    <input type="hidden" name="status" value="MERCADORIA_CHEGOU" />
                                    </span>
                                    <a href="#pablo" class="btn btn-danger btn-round fileinput-exists" data-dismiss="fileinput"><i class="fa fa-times"></i> Cancelar</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Adicionar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="observacao" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg" role="document">
<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Nova observação</h5>
    </div>
    <form action="{{route('admin.servico.adicionarobservacao',['id'=>$manutencao->id])}}" method="POST">
        {{csrf_field()}}
        <div class="modal-body">
            <div class="row">

                <div class="col-md-12 pr-1">
                    <div class="form-group">
                        <label>Descreva</label>
                        <textarea class="form-control" name="descricao" required>{{old('descricao')}}</textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-success">Atualizar</button>
        </div>
    </form>
</div>
</div>
</div>

<div class="modal fade" id="proposta" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg" role="document">
<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Orçamento</h5>
    </div>
    <form action="{{route('admin.manutencao.enviarproposta',['id'=>$manutencao->id])}}" method="POST">
        @csrf
        <div class="modal-body">
            <div class="row">

            <div class="col-md-12 pr-1">
                <div class="form-group">
                    <label>Valor do orçamento</label>
                    <input type="text" class="form-control money" name="valor_orcamento" required />
                </div>
            </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-success">Enviar</button>
        </div>
    </form>
</div>
</div>
</div>

<div class="modal fade" id="pagamento" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Pagamento realizado</h5>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row">
                        <div class="col-md-12 pr-1">
                        <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                            <div>
                                <span class="btn btn-rose btn-round btn-file">
                                <span class="fileinput-new">Comprovante (Opcional)</span>
                                <span class="fileinput-exists">Alterar</span>
                                <input type="file" name="foto" multiple />
                                </span>
                                <a href="#pablo" class="btn btn-danger btn-round fileinput-exists" data-dismiss="fileinput"><i class="fa fa-times"></i> Cancelar</a>
                            </div>
                            <div class="fileinput-new thumbnail">
                                <img src="{{ asset('admin/assets/img/image_placeholder.jpg') }}" alt="...">
                            </div>
                            <div class="fileinput-preview fileinput-exists thumbnail"></div>
                        </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success">Enviar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="reenviar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <form action="{{ route('admin.servico.reenviar', $manutencao->id) }}" method="POST">
            {{csrf_field()}}
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Reenviar o aparelho</h5>
                </div>
                <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 pr-1">
                                <div class="form-group">
                                    <label>Código de rastreio</label>
                                    <input type="text" name="retorno_rastreio" class="form-control" required>
                                </div>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Enviar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="fotofinal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Nova Foto</h5>
        </div>
        <form action="{{route('admin.servico.uploadfoto',['id'=>$manutencao->id])}}" method="post" enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="modal-body">
                <div class="row">
    
                    <div class="col-md-12 pr-1">
                        <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                            <div class="fileinput-new thumbnail">
                                <img src="{{ asset('admin/assets/img/image_placeholder.jpg') }}" alt="...">
                            </div>
                            <div class="fileinput-preview fileinput-exists thumbnail"></div>
                            <div>
                                <span class="btn btn-rose btn-round btn-file">
                                <span class="fileinput-new">Selecione a imagem</span>
                                <span class="fileinput-exists">Alterar</span>
                                <input type="file" name="foto" required />
                                <input type="hidden" name="status" value="EM_MANUTENCAO" />
                                </span>
                                <a href="#pablo" class="btn btn-danger btn-round fileinput-exists" data-dismiss="fileinput"><i class="fa fa-times"></i> Cancelar</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success">Adicionar</button>
            </div>
        </form>
    </div>
    </div>
    </div>
    
    
    <div class="modal fade" id="videofinal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Novo Vídeo</h5>
        </div>
        <form action="{{route('admin.servico.uploadvideo',['id'=>$manutencao->id])}}" method="post" enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="modal-body">
                <div class="row">
    
                    <div class="col-md-12 pr-1">
                        <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                            <div class="fileinput-new thumbnail">
                                <img src="{{ asset('admin/assets/img/image_placeholder.jpg') }}" alt="...">
                            </div>
                            <div class="fileinput-preview fileinput-exists thumbnail"></div>
                            <div>
                                <span class="btn btn-rose btn-round btn-file">
                                <span class="fileinput-new">Selecione o video</span>
                                <span class="fileinput-exists">Alterar</span>
                                <input type="file" name="video" required />
                                <input type="hidden" name="status" value="EM_MANUTENCAO" />
                                </span>
                                <a href="#pablo" class="btn btn-danger btn-round fileinput-exists" data-dismiss="fileinput"><i class="fa fa-times"></i> Cancelar</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success">Adicionar</button>
            </div>
        </form>
    </div>
    </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript">
        $(function(){
            $('.chancela').hide();
            $('#finalizar_manutencao').bind('click',function(){
                $('.chancela').show('slow');
            });
        });
    </script>
<script>
    $(".selecionarResp").change(function(){
        var resp_id = $(this).val();
        $.ajax({
            method:'POST',
            url:"{{route('admin.servico.setarresponsavel')}}",
            data:{servico_id:'{{$manutencao->id}}',responsavel_id: resp_id},
            headers: {
                'X-CSRF-TOKEN': '{{csrf_token()}}'
            },
        }).done(function(){
            swal("Feito!", "Técnico Selecionado..", "success"),
            function(){
                window.location.href = "#"
            };
        }).fail(function(){
            swal("Oops!", "Ocorreu um erro.", "warning");
        })
    })
</script>
<script>
$(function() {
    $('.acessorio-check').on('init.bootstrapSwitch', function(event, state) {
        console.log('init');
    })
    $('.acessorio-check').on('switchChange.bootstrapSwitch', function(event, state) {
        var id = $(this).data('id');
        $.ajax({
            url:"{{route('admin.servico.alterarstatusacessorio')}}",
            method: 'POST',
            data:{status: state,id: id},
            headers: {
                'X-CSRF-TOKEN': '{{csrf_token()}}'
            },
        }).done(function(){
            /*swal({ 
                title: "Feito",
                text: "",
                type: "success" 
            },*/
            //function(){
                //window.location.href = "{{route('admin.manutencao',['id'=>$manutencao->id])}}"
            //});
        }).fail(function(e){
            swal('Problema','Ocorreu um problema:'+e.erro,'warning');
        })
    });
})
</script>

<script>
$(function() {
    $('.problema-check').on('init.bootstrapSwitch', function(event, state) {
        console.log('init');
    })
    $('.problema-check').on('switchChange.bootstrapSwitch', function(event, state) {
        var id = $(this).data('id');
        $.ajax({
            url:"{{route('admin.servico.alterarstatusproblema')}}",
            method: 'POST',
            data:{status: state,id: id},
            headers: {
                'X-CSRF-TOKEN': '{{csrf_token()}}'
            },
        }).done(function(){
            /*swal({ 
                title: "Feito",
                text: "",
                type: "success" 
            },*/
            //function(){
                //window.location.href = "{{route('admin.manutencao',['id'=>$manutencao->id])}}"
            //});
        }).fail(function(e){
            swal('Problema','Ocorreu um problema:'+e.erro,'warning');
        })
    });
})
</script>

@endsection