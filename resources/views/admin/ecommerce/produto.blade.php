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
	@include('admin.navbar', ['title' => $produto->nome])
	<!-- End Navbar -->
	<div class="panel-header panel-header-sm">
	</div>
	<div class="content">

		<div class="row">
			<div class="col-md-12 ml-auto mr-auto">
				<div class="card card-plain card-subcategories">
					<div class="card-body">
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


						<ul class="nav nav-pills nav-pills-primary nav-pills-icons justify-content-center" id="tabProduto" role="tablist">
							<li class="nav-item">
								<a class="nav-link active" data-toggle="tab" href="#link7" role="tablist">
									<i class="now-ui-icons files_paper"></i> Dados
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" data-toggle="tab" href="#link10" role="tablist">
									<i class="now-ui-icons shopping_shop"></i> Lojas
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" data-toggle="tab" href="#link8" role="tablist">
									<i class="now-ui-icons design_image"></i> Fotos
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" data-toggle="tab" href="#link9" role="tablist">
									<i class="now-ui-icons business_money-coins"></i> Promoção
								</a>
							</li>
						</ul>
						<div class="tab-content tab-space tab-subcategories">

							<div class="tab-pane active" id="link7">
								<form action="{{route('admin.produtos.atualizar',['id'=>$produto->id])}}" method="post">
									{{csrf_field()}}
									<div class="row">
										<div class="col-md-6 pr-1">
											<div class="form-group">
												<label>Nome</label>
												<input type="text" class="form-control" name="nome" value="{{$produto->nome}}">
											</div>
										</div>
										<div class="col-md-6 pr-1">
											<div class="form-group">
												<label>Descrição</label>
												<input type="text" class="form-control" name="descricao" value="{{$produto->descricao}}">
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-4 pr-1">
											<div class="from-group">
												<div class="form-check" style="margin-left: 0; padding-left: 0; margin-bottom: 20px;">
													<label class="form-check-label">
														<input class="form-check-input" name="destaque" value="destaque" @if($produto->destaque) checked @endif type="checkbox" />
														<span class="form-check-sign"></span>
														Produto em Destaque
													</label>
												</div>
											</div>

											<div class="form-group">
												<label>Selecione a(s) categoria(s) do produto</label>
												@foreach($categorias as $categoria)
												<div class="form-check">
													<label class="form-check-label">
														<input class="form-check-input" name="categorias[]" value="{{ $categoria->id }}" @if($produto->hasCategoria($categoria->id)==true) checked @endif type="checkbox" />
														<span class="form-check-sign"></span>
														{{ $categoria->nome }}
													</label>
												</div>
												@endforeach
											</div>
										</div>
										<div class="col-md-8 pr-1 row">
											<div class="col-md-12 pr-1">
												<div class="form-group">
													<label>Preço</label>
													<input type="text" class="form-control money" name="valor" value="{{number_format($produto->valor/100,2,',','.')}}">
												</div>
											</div>
											<div class="col-md-12 pr-1">
												<label>É Seminovo?</label>
												<select class="form-control" name="semi_novo" required>
													<option value="0" @if($produto->semi_novo == 0) selected @endif>Não</option>
													<option value="1" @if($produto->semi_novo == 1) selected @endif>Sim</option>
												</select>
											</div>
											<div class="col-md-12 pr-1">
												<label>Selecione a marca</label>
												<select class="form-control" name="marca_id" required>
													<option value="0">Sem marca</option>
													@foreach($marcas as $marca)
													<option value="{{ $marca->id }}" @if($produto->marca && $produto->marca->id == $marca->id) selected @endif>
														{{ $marca->nome }}
													</option>
													@endforeach
												</select>
											</div>
											<div class="col-md-12 pr-1">
												<div class="form-group">
													<label>Possui capacidade?</label>
													<input class="form-control" placeholder="Caso sim, entre com a capaciade em GB" type="number" name="capacidade" value="{{$produto->capacidade_id}}" />
												</div>
											</div>

											<div class="col-md-3 pr-1">
												<div class="form-group">
													<label>Peso (em gramas)</label>
													<input class="form-control"  type="text" name="peso" value="{{$produto->peso }}" />
												</div>
											</div>

											<div class="col-md-3 pr-1">
												<div class="form-group">
													<label>Altura (em centímetros)</label>
													<input class="form-control"  type="text" name="altura" value="{{$produto->altura }}" />
												</div>
											</div>

											<div class="col-md-3 pr-1">
												<div class="form-group">
													<label>Largura (em centímetros)</label>
													<input class="form-control"  type="text" name="largura" value="{{$produto->largura }}" />
												</div>
											</div>

											<div class="col-md-3 pr-1">
												<div class="form-group">
													<label>Comprimento (em centímetros)</label>
													<input class="form-control"  type="text" name="comprimento" value="{{$produto->comprimento }}" />
												</div>
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-md-12">
											<div class="form-group">
												<button class="btn btn-success pull-right">Atualizar</button>
											</div>
										</div>
									</div>
								</form>
							</div>

							<div class="tab-pane" id="link10">
								<div class="row">
									<div class="col-md-12">
										<h5><a class="btn btn-success pull-right" style="color:white;" data-toggle="modal" data-target="#novaloja">Adicionar nova loja</a></h5>
										<table id="datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
											<thead>
												<tr>
													<th>Loja</th>
													<th>Estoque</th>
													<th>Voltagem</th>
													<th class="disabled-sorting text-right">Opções</th>
												</tr>
											</thead>
											<tbody>
												@foreach($produto->estoques as $e)
												<tr>
													<td>{{$e->loja->titulo}}</td>
													<td>
														<a href="#" class="btn btn-round btn-default btn-icon btn-sm remove"><i title="Retirar" class="fas fa-minus"></i></a>
														{{$e->quantidade}} 
														<a href="#" class="btn btn-round btn-default btn-icon btn-sm remove"><i title="Retirar" class="fas fa-plus"></i></a>

													</td>
													<td>{{ $e->getVoltagemName() }}</td>
													<td class="text-right">

														<a href="{{route('admin.produtos.removerestoque',['id'=>$e->id])}}" class="btn btn-round btn-danger btn-icon btn-sm remove"><i title="Retirar" class="fas fa-times"></i></a>
														<a href="#" data-toggle="modal" data-target="#estoque{{$e->id}}" class="btn btn-round btn-info btn-icon btn-sm edit"><i class="fas fa-archive"></i></a>
													</td>
												</tr>
												<div class="modal fade" id="estoque{{$e->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
													<div class="modal-dialog modal-lg" role="document">
														<div class="modal-content">
															<div class="modal-header">
																<h5 class="modal-title" id="exampleModalLabel">Atualizar estoque</h5>

															</div>
															<form action="{{route('admin.produtos.atualizarestoque',['id'=>$e->id])}}" method="POST">
																{{csrf_field()}}
																<div class="modal-body">
																	<div class="row">

																		<div class="col-md-12 pr-1">
																			<div class="form-group">
																				<label>Quantidade em estoque</label>
																				<input type="number" class="form-control" name="quantidade" required value="{{$e->quantidade}}" min="0" />
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
												@endforeach
											</tbody>
										</table>
									</div>
								</div>
							</div>

							<div class="tab-pane" id="link8">
								<div class="row">
									<div class="col-md-12" style="margin-bottom: 20px; color:white;">
										<a class="btn btn-success pull-right" data-toggle="modal" data-target="#novafoto">Adicionar nova foto</a>

										<div class="modal fade" id="novafoto" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
											<div class="modal-dialog modal-lg" role="document">
												<div class="modal-content">
													<form action="{{ route('admin.produtos.fotos.create') }}" method="post" enctype="multipart/form-data">
														<div class="modal-body">
															@csrf
															<input type="hidden" name="produto_id" required value="{{ $produto->id }}"/>
															<center>
																<div class="row">
																	<div class="col-md-12 pr-1">
																		<div class="fileinput fileinput-new text-center" data-provides="fileinput">
																			<div>
																				<span class="btn btn-rose btn-round btn-file">
																					<span class="fileinput-new">Selecione a foto</span>
																					<span class="fileinput-exists">Alterar</span>
																					<input type="file" name="foto" required />
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
															</center>
														</div>
														<div class="modal-footer">
															<button type="button" class="btn btn-danger" data-dismiss="modal"><b>Cancelar</b></button>
															<button type="submit" class="btn btn-success"><b>Adicionar</b></button>
														</div>
													</form>
												</div>
											</div>
										</div>

									</div>
									@foreach($produto->fotos as $f)
									<div class="col-md-3">
										<div class="card card-user">
											<div class="image">
												<img src="{{ $f->diretorio }}">
											</div>
											<hr>
											<div class="button-container">
												<button href="#" data-toggle="modal" data-target="#editfoto{{ $f->id }}" class="btn btn-info">
													Editar
												</button>
												<a href="{{route('admin.produtos.removerfoto',['id'=>$f->id])}}" class="btn btn-danger">
													Remover
												</a>
											</div>
										</div>
									</div>

									<div class="modal fade" id="editfoto{{ $f->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
										<div class="modal-dialog modal-lg" role="document">
											<div class="modal-content">
												<form action="{{ route('admin.produtos.fotos.update') }}" method="post" enctype="multipart/form-data">
													<div class="modal-body">
														@csrf
														<input type="hidden" name="foto_id" required value="{{ $f->id }}"/>
														<center>
															<div class="row">
																<div class="col-md-12 pr-1">
																	<div class="fileinput fileinput-new text-center" data-provides="fileinput">
																		<div>
																			<span class="btn btn-rose btn-round btn-file">
																				<span class="fileinput-new">Selecione a foto</span>
																				<span class="fileinput-exists">Alterar</span>
																				<input type="file" name="foto" required />
																			</span>
																			<a href="#pablo" class="btn btn-danger btn-round fileinput-exists" data-dismiss="fileinput"><i class="fa fa-times"></i> Cancelar</a>
																		</div>
																		<div class="fileinput-new thumbnail">
																			<img src="{{ $f->diretorio }}" alt="...">
																		</div>
																		<div class="fileinput-preview fileinput-exists thumbnail"></div>
																	</div>
																</div>
															</div>
														</center>
													</div>
													<div class="modal-footer">
														<button type="button" class="btn btn-danger" data-dismiss="modal"><b>Cancelar</b></button>
														<button type="submit" class="btn btn-success"><b>Atualizar</b></button>
													</div>
												</form>
											</div>
										</div>
									</div>

									@endforeach
								</div>
							</div>

							<div class="tab-pane" id="link9">
								<form method="post" action=" {{ route('admin.produtos.alterarstatuspromo') }} ">
									@csrf
									<div class="row">
										<div class="col-md-4 pr-1">
											<div class="form-group">
												<label>Porcentagem de desconto</label>
												<input type="number" class="form-control" name="desconto" max="100" min="0" value="{{ $produto->promocao ? $produto->promocao->desconto : 0 }}">
												<input type="hidden" name="produto_id" class="form-control" value="{{ $produto->id }}">
											</div>
										</div>
										<div class="col-md-4 pr-1">
											<div class="form-group">
												<label>Status da promoção</label>
												<select name="status" class="form-control">
													@if(!$produto->promocao)
														<option value="ATIVO">ATIVO</option>
														<option value="INATIVO">INATIVO</option>
													@else
														<option value="{{ $produto->promocao->status }}">{{ $produto->promocao->status }}</option>
														@if($produto->promocao->status != 'ATIVO')<option value="ATIVO">ATIVO</option>@endif
														@if($produto->promocao->status != 'INATIVO')<option value="INATIVO">INATIVO</option>@endif
													@endif
												</select>

											</div>
										</div>
										<div class="col-md-4 pr-1">
											<button type="submit" class="btn btn-success" style="color: white; margin-top: 23px;" ><b>Atualizar</b></button>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="novaloja" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Associe uma loja que venda esse produto</h5>
			</div>
			<form action="{{route('admin.produtos.adicionarestoque',['id'=>$produto->id])}}" method="post">
				{{csrf_field()}}
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12 pr-1">
							<div class="form-group">
								<label>Selecione a loja</label>
								<select class="selectpicker" name="loja_id" data-style="btn btn-default btn-round" data-size="7">
									@foreach(\App\Loja::all() as $l)
									<option value="{{$l->id}}">{{$l->titulo}} </option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-12 pr-1">
							<div class="form-group">
								<label>Quantidade em estoque</label>
								<input type="number" class="form-control" name="quantidade" required />
							</div>
						</div>
						<div class="col-md-12 pr-1">
							<div class="form-group">
								<label>Eletrônico</label>
								<select class="selectpicker" name="tipo" data-style="btn btn-default btn-round" data-size="7">
									<option value="q">Não </option>
									<option value="1">110 V </option>
									<option value="2">220 V </option>
								</select>
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


<script>
	$('#tabProduto a').click(function(e) {
	  e.preventDefault();
	  $(this).tab('show');
	});

	// store the currently selected tab in the hash value
	$("ul.nav-pills > li > a").on("shown.bs.tab", function(e) {
	  var id = $(e.target).attr("href").substr(1);
	  localStorage.setItem('tabProduto', id);
	});

	// on load of the page: switch to the currently selected tab
	var hash = window.location.hash;
	$('#tabProduto a[href="' + hash + '"]').tab('show');

	$(document).ready(function(){
		$("input[name='checkbox']").bootstrapSwitch();

		let tab = localStorage.getItem('tabProduto');

		if(tab) {
			$('#tabProduto a[href="#' + tab + '"]').tab('show');
		}
	})
	
	$(function() {
		$('input[name="checkbox"]').on('init.bootstrapSwitch', function(event, state) {
			console.log('init');
		})
		$('input[name="checkbox"]').on('switchChange.bootstrapSwitch', function(event, state) {
		console.log(state); // true | false
		var valor = $("#valor_desconto").val();
		$.ajax({
			url:"{{route('admin.produtos.alterarstatuspromo')}}",
			method: 'POST',
			data:{status:state,valor:valor,id:'{{$produto->id}}'},
			headers: {
				'X-CSRF-TOKEN': '{{csrf_token()}}'
			},
		}).done(function(){
			swal({ 
				title: "Feito",
				text: "Promoção ativa!",
				type: "success" 
			},
			function(){
				window.location.href = "{{route('admin.produto',['id'=>$produto->id])}}"
			});
		}).fail(function(e){
			swal('Problema','Ocorreu um problema:'+e.erro,'warning');
		})
	});
	})
</script>
@endsection