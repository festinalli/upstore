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
    	
    @include('admin.navbar', ['title' => '#'.$servico->id])
      
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
                  	<li class="nav-item">
						<a class="nav-link @if($servico->status == 'CRIADO') active @endif" data-toggle="tab" href="#link7" role="tablist">
							<i class="now-ui-icons tech_mobile"></i> Aparelho e Cliente
						</a>
					</li>

					@if(
						$servico->status == 'MERCADORIA_CHEGOU' OR
						$servico->status == 'ANALISE' OR
						$servico->status == 'PROPOSTA_ENVIADA' OR
						$servico->status == 'CLIENTE_RECUSOU' OR
						$servico->status == 'CLIENTE_ACEITOU' OR
						$servico->status == 'PAGAMENTO_REALIZADO'
					)
					<li class="nav-item">
						<a class="nav-link @if($servico->status == 'MERCADORIA_CHEGOU') active @endif" data-toggle="tab" href="#link10" role="tablist">
							<i class="now-ui-icons shopping_box"></i> Chegada da mercadoria
						</a>
					</li>
					@endif

					@if(
						$servico->status == 'ANALISE' OR
						$servico->status == 'PROPOSTA_ENVIADA' OR
						$servico->status == 'CLIENTE_RECUSOU' OR
						$servico->status == 'CLIENTE_ACEITOU' OR
						$servico->status == 'PAGAMENTO_REALIZADO'
					)
					<li class="nav-item">
						<a class="nav-link @if($servico->status == 'ANALISE') active @endif" data-toggle="tab" href="#link8" role="tablist">
							<i class="now-ui-icons shopping_delivery-fast"></i> Análise e orçamento
						</a>
					</li>
					@endif

					@if(
						$servico->status == 'PROPOSTA_ENVIADA' OR
						$servico->status == 'CLIENTE_RECUSOU' OR
						$servico->status == 'CLIENTE_ACEITOU' OR
						$servico->status == 'PAGAMENTO_REALIZADO' 
					)
					<li class="nav-item">
						<a class="nav-link @if($servico->status == 'PROPOSTA_ENVIADA' OR $servico->status == 'CLIENTE_RECUSOU' OR
								$servico->status == 'CLIENTE_ACEITOU') active @endif" data-toggle="tab" href="#link9" role="tablist">
							<i class="now-ui-icons ui-1_check"></i> Autorização do cliente
						</a>
					</li>
					@endif

					@if(
						$servico->status == 'PAGAMENTO_REALIZADO'
					)
					<li class="nav-item">
						<a class="nav-link  
						@if(
							$servico->status == 'PAGAMENTO_REALIZADO'
						) active 
						@endif" data-toggle="tab" href="#link11" role="tablist">
							<i class="now-ui-icons business_money-coins"></i> Pagamento realizado
						</a>
					</li>
					@endif
                </ul>
                <div class="tab-content tab-space tab-subcategories">

<!-- ====================================== CRIADO ====================================== -->
				<div class="tab-pane @if($servico->status == 'CRIADO') active @endif" id="link7">
                  	<div class="row">
                        <div class="col-md-12">
                            <form action="{{ route('admin.usado.mercadoriachegou',['servico_id' => $servico->id]) }}" method="post" style="width:100%">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Técnico</label>
                                            <select class="selectpicker form-control" data-style="btn btn-default btn-round" title="Selecione o responsável" data-size="7" @if($servico->status != 'CRIADO') disabled @endif name="responsavel_id">
                                                @foreach(\App\User::where('tipo','TECNICO')->get() as $u)
                                                    <option value="{{$u->id}}" @if($servico->tecnico_id == $u->id) selected @endif>
                                                        {{$u->nome}} {{$u->sobrenome}}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Data Entrega</label>
                                            <input class="form-control" type="date" id="data_entrega" name="data_entrega" style="margin:10px 0; padding: 5px 10px;font-size:1.29em" @if($servico->status != 'CRIADO') readonly @endif @if($servico->data_entrega) value="{{date('Y-m-d',strtotime($servico->data_entrega))}}" @endif>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            @if($servico->status == 'CRIADO')
                                            <button type="submit" class="btn btn-danger pull-right" style="color: white;margin:33px 20px 0px 33px">Mercadoria Chegou</button>
                                            @endif
                                        </div>
                                    </div>

                                </div>
                            </form>
                        </div>

						<div class="col-md-12">
							<div class="col-md-12 pr-1">
								<div class="form-group">
									<label>Cliente</label>
									<p><a href="#">{{ $servico->cliente->nome }}</a></p>
								</div>
							</div>
							<div class="col-md-12 pr-1">
								<div class="form-group">
									<label>Marca</label>
									<input type="text" class="form-control" disabled="" value="{{ $servico->aparelho->capacidade->modelo->marca->nome }}">
								</div>
							</div>
							<div class="col-md-12 pr-1">
								<div class="form-group">
									<label>Modelo</label>
									<input type="text" class="form-control" disabled="" value="{{ $servico->aparelho->capacidade->modelo->nome }}">
								</div>
							</div>
							<div class="col-md-12 pr-1">
								<div class="form-group">
									<label>Capacidade</label>
									<input type="text" class="form-control" disabled="" value="{{ $servico->aparelho->capacidade->memoria }} GB">
								</div>
							</div>
							<div class="col-md-12 pr-1">
								<div class="form-group">
									<label>Pré-orçamento:</label>
									<input type="text" class="form-control" disabled="" value="R$ {{ number_format($servico->valor/100,2,',','.') }}">
								</div>
							</div>
							<div class="col-md-12 pr-1">
								<div class="form-group">
									<label>Acessos:</label>
									<p>Senha: {{ $servico->aparelho->senha }}</p>
								</div>
							</div>
							<div class="col-md-12 pr-1">
								<div class="form-group">
									<label>Especificações de outros problemas:</label>
									<p>{{$servico->descricao}}</p>
								</div>
							</div>
							<div class="col-md-12">
								<div class="form-group">
									<label>Método de coleta selecionado pelo cliente</label>
									<input type="text" class="form-control" value="{{ $servico->metodo }}" disabled="">
								</div>
							</div>

							@if($servico->loja_id)
							<div class="col-md-12">
								<div class="form-group">
									<label>Loja selecionada</label>
									<input type="text" class="form-control" disabled="" value="{{$servico->loja->titulo}}">
									
								</div>
							</div>
							@endif

						</div>
						<div class="col-md-6">
							<table id="datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
								<thead>
									<tr>
										<th>Acessórios</th>
									</tr>
								</thead>
								<tbody>
								
									@if(count($servico->aparelho->acessorios) <= 0)
										<tr>
											<td>Nenhum acessório selecionado</td>
											<td></td>
										</tr>
									@else
										@foreach($servico->aparelho->acessorios as $a)
											<tr>
												<td>{{ $a->acessorio->nome }}</td>
											</tr>
										@endforeach
									@endif
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
									@foreach($servico->aparelho->problemas as $p)
									<tr>
										<td>{{ $p->problema->nome }}</td>
									</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
                </div>
<!-- ====================================== MERCADORIA_CHEGOU ====================================== -->
                <div class="tab-pane @if($servico->status == 'MERCADORIA_CHEGOU') active @endif" id="link10">
                    <div class="row">
                    	
                    	<div class="col-md-12">
							@if($servico->status == 'MERCADORIA_CHEGOU')
							<a href="{{ route('admin.usado.orcamento',['servico_id' => $servico->id]) }}" class="btn btn-danger pull-right" style="color: white;">Próximo >></a>
							@endif
                    	</div>
                    	<div class="col-md-12">
							<div class="row">
								<div class="col-md-4">
									<div class="form-group">
										<label>Data de chegada:</label>
										<p>{{ date('d/m/Y',strtotime($servico->updated_at)) }}</p>
									</div>
								</div>
								<div class="col-md-8">
									@if($servico->status == 'MERCADORIA_CHEGOU')
										<a class="btn btn-info pull-right" style="color: white;" data-toggle="modal" data-target="#foto">Adicionar foto</a>
										<a class="btn btn-info pull-right" style="color: white;" data-toggle="modal" data-target="#video">Adicionar video</a>
									@endif

									<table id="datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
										<thead>
											<tr>
												<th>Foto / Video</th>
												<th class="disabled-sorting text-right">Excluir</th>
											</tr>
										</thead>
										<tbody>
											@foreach($midiasMercadoria as $midia)
											<tr>
												@if(!$midia->video)
												<td><img src="{{ $midia->foto }}" style="max-width:150px"></td>
												@else 
												<td>
													<video style="max-width:150px" controls>
														<source src="{{ $midia->video }}" type="video/mp4">
													</video>
												</td>
												@endif
												<td class="text-right">
													<a href="#" class="btn btn-round btn-danger btn-icon btn-sm remove" data-toggle="modal" data-target="#midia{{ $midia->id }}"><i title="Desativar" class="fas fa-times"></i></a>
												</td>
<div class="modal fade" id="midia{{ $midia->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg" role="document">
<div class="modal-content">
	<div class="modal-header">
		<h5 class="modal-title" id="exampleModalLabel">Excluir midia</h5>
	</div>
	<div class="modal-body">
		<div class="row">
			<div class="col-md-12 pr-1">
				<p>Tem certeza que deseja excluir essa midia?</p>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<a class="btn btn-danger" data-dismiss="modal" style="color:white;"><b>Não</b></a>
		<a href="{{ route('admin.servico.removermidia',['midia_id' => $midia->id]) }}" class="btn btn-success"><b>Sim</b></a>
	</div>
</div>
</div>
</div>
											</tr>
											@endforeach
										</tbody>
									</table>
								</div>
							</div>
                    	</div>
                    </div>
				</div>

<!-- ====================================== ORCAMENTO ====================================== -->				  
				<div class="tab-pane @if($servico->status == 'ANALISE') active @endif" id="link8">
                    <div class="row">
                    	<div class="col-md-6">
							<b>Pré-orçamento: R$ {{ number_format($servico->preOrcamento()/100,2,',','.') }}</b>
							@if($servico->status == 'ANALISE')
							<button class="btn btn-danger pull-right" style="color: white;" data-toggle="modal" data-target="#proposta">Enviar proposta</button>
							@endif
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Data Entrega</label>
                                <input class="form-control" type="date" id="data_entrega" name="data_entrega" style="margin:10px 0; padding: 5px 10px;font-size:1.29em" readonly @if($servico->data_entrega) value="{{date('Y-m-d',strtotime($servico->data_entrega))}}" @endif>
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
								@foreach($servico->aparelho->acessorios as $a)
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
									@foreach($servico->aparelho->problemas as $p)
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
                    		<a class="btn btn-info pull-right" style="color: white;" data-toggle="modal" data-target="#observacao">Adicionar observação</a>
                    		<table id="datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
								<thead>
									<tr>
										<th>Observações</th>
										<th class="disabled-sorting text-right">Excluir</th>
									</tr>
								</thead>
			                  	<tbody>
								@foreach($servico->observacoes as $observacao)
									<tr>
										<td>{{ $observacao->descricao }}</td>
										<td class="text-right">
											<a data-toggle="modal" data-target="#deleteObser{{ $observacao->id }}" href="#" class="btn btn-round btn-danger btn-icon btn-sm remove"><i title="Desativar" class="fas fa-times"></i></a>
										</td>
<div class="modal fade" id="deleteObser{{ $observacao->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Excluir observação</h5>
				
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12 pr-1">
						<p>Tem certeza que deseja deletar essa observação?</p>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<a style="color:white"; class="btn btn-danger" data-dismiss="modal"><b>Não</b></a>
				<a href="{{ route('admin.servico.removerobservacao',['observacao_id' => $observacao->id]) }}" class="btn btn-success"><b>Sim</b></a>
			</div>
		</div>
	</div>
</div>
									</tr>
								@endforeach
			                  	</tbody>
			                </table>
                    	</div>
                    </div>
				</div>
				  
<!-- ====================================== PROPOSTA ====================================== -->
                <div class="tab-pane @if($servico->status == 'PROPOSTA_ENVIADA' || $servico->status == 'CLIENTE_ACEITOU' || $servico->status == 'CLIENTE_RECUSOU') active @endif" id="link9">
					<div class="row">
						<div class="col-md-6 pr-1">
							<div class="form-group">
							<label>Status:</label>
							@if($servico->status == 'PROPOSTA_ENVIADA')
								<p><b>Aguardando resposta do cliente</b></p>
							@elseif($servico->status == 'CLIENTE_ACEITOU')
								<p><b>Cliente Aceitou</b></p>
							@elseif($servico->status == 'CLIENTE_RECUSOU')
								<p><b>Cliente Recusou</b></p>
							@endif
							</div>
						</div>
						<div class="col-md-6">
							@if($servico->status == 'PROPOSTA_ENVIADA' && $servico->status != 'CLIENTE_RECUSOU')
							<a href="{{ route('admin.usado.pagamentorealizado',['servico_id' => $servico->id]) }}" class="btn btn-danger pull-right" style="color: white;">Pagamento realizado</a>
							@endif
						</div>
						@if($servico->banco && $servico->status != 'CLIENTE_RECUSOU')
						<div class="col-md-6 pr-1">
							<div class="form-group">
							<label>Banco</label>
							<p>{{$servico->banco->banco}}</p>
							</div>
						</div>
						<div class="col-md-6 pr-1">
							<div class="form-group">
							<label>Tipo de conta</label>
							<p>{{$servico->banco->tipo_conta}}</p>
							</div>
						</div>
						<div class="col-md-6 pr-1">
							<div class="form-group">
							<label>Agência</label>
							<p>{{$servico->banco->agencia}}</p>
							</div>
						</div>
						<div class="col-md-6 pr-1">
							<div class="form-group">
							<label>Conta</label>
							<p>{{$servico->banco->conta}}</p>
							</div>
						</div>
						<div class="col-md-6 pr-1">
							<div class="form-group">
							<label>Nome do titular da conta</label>
							<p>{{$servico->banco->titular}}</p>
							</div>
						</div>
						<div class="col-md-6 pr-1">
							<div class="form-group">
							<label>Cpf fo titular da conta</label>
							<p>{{$servico->banco->documento_titular}}</p>
							</div>
						</div>
						@endif
					</div>
				</div>

<!-- ====================================== PAGAMENTO_REALIZADO ====================================== -->
				<div class="tab-pane @if($servico->status == 'PAGAMENTO_REALIZADO') active @endif" id="link11">
					<div class="row">
						<div class="col-md-6 pr-1">
							<div class="form-group">
							<p><b>Pagamento enviado para o cliente</b></p>
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

<!-- ========================== MODAIS ========================== -->

<div class="modal fade" id="foto" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg" role="document">
<div class="modal-content">
	<div class="modal-header">
		<h5 class="modal-title" id="exampleModalLabel">Nova foto</h5>
	</div>
	<form action="{{ route('admin.servico.uploadfoto',['id'=>$servico->id]) }}" method="post" enctype="multipart/form-data">
		<div class="modal-body">
			@csrf
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
								<input type="hidden" name="status" value="MERCADORIA" required />
								<input type="hidden" name="servico_id" value="{{ $servico->id }}" required />
							</span>
							<a href="#pablo" class="btn btn-danger btn-round fileinput-exists" data-dismiss="fileinput"><i class="fa fa-times"></i> Cancelar</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
			<button type="submit" class="btn btn-success"><b>Adicionar</b></button>
		</div>
	</form>
</div>
</div>
</div>
				
<div class="modal fade" id="video" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg" role="document">
<div class="modal-content">
	<div class="modal-header">
		<h5 class="modal-title" id="exampleModalLabel">Novo video</h5>
	</div>
	<form action="{{ route('admin.servico.uploadfoto',['id'=>$servico->id]) }}" method="post" enctype="multipart/form-data">
		<div class="modal-body">
			@csrf
			<div class="row">
				<div class="col-md-12 pr-1">
					<div class="fileinput fileinput-new text-center" data-provides="fileinput">
						<div class="fileinput-new thumbnail">
							<img src="{{ asset('admin/assets/img/image_placeholder.jpg') }}" alt="...">
						</div>
						<div class="fileinput-preview fileinput-exists thumbnail"></div>
						<div>
							<span class="btn btn-rose btn-round btn-file">
								<span class="fileinput-new">Selecione vídeo</span>
								<span class="fileinput-exists">Alterar</span>
								<input type="file" name="video" />
							</span>
							<a href="#pablo" class="btn btn-danger btn-round fileinput-exists" data-dismiss="fileinput"><i class="fa fa-times"></i> Cancelar</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
			<button type="button" class="btn btn-success">Adicionar</button>
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
	<form method="post" action="{{ route('admin.servico.adicionarobservacao',['id'=>$servico->id]) }}">
		@csrf
		<div class="modal-body">
			<div class="row">
				<div class="col-md-12 pr-1">
					<div class="form-group">
						<label>Descreva</label>
						<input type="text" class="form-control" name="descricao" required />
						<input type="hidden" class="form-control" name="servico_id" value="{{ $servico->id }}" required />
					</div>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-danger" data-dismiss="modal"><b>Cancelar</b></button>
			<button type="submit" class="btn btn-success"><b>Adicionar</b></button>
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
	<form action="{{route('admin.usado.enviarproposta',['id'=>$servico->id])}}" method="POST">
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
@endsection

@section('js')
<script>
	$(".selecionarResp").change(function(){
		var resp_id = $(this).val();
		$.ajax({
			method:'POST',
			url:"{{route('admin.servico.setarresponsavel')}}",
			data:{servico_id:'{{$servico->id}}',responsavel_id: resp_id},
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
					//window.location.href = "{{route('admin.manutencao',['id'=>$servico->id])}}"
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
					//window.location.href = "{{route('admin.manutencao',['id'=>$servico->id])}}"
				//});
			}).fail(function(e){
				swal('Problema','Ocorreu um problema:'+e.erro,'warning');
			})
		});
	})
</script>
@endsection