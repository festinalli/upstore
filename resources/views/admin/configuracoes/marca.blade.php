@extends('admin.layout')

@section('css')
<style type="text/css">
	.inn.row {
	    margin-bottom: 5px;
	}
</style>
@endsection

@section('main')
<div class="main-panel">
	<!-- Navbar -->
      @include('admin.navbar', ['title' => $marca->nome])
      <!-- End Navbar -->
	<div class="panel-header panel-header-sm"></div>
	<div class="content">
        <div class="row">
			<div class="col-md-4">
				<div class="card">
					<div class="card-header">
                        <h5 class="title">Dados cadastrados</h5>
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
					</div>
					<div class="card-body">
						<form enctype="multipart/form-data" method="post" action="{{ route('admin.configuracoes.marca.update') }}">
						@csrf
						<div class="row">
							<div class="col-md-12 pr-1">
							<div class="form-group">
								<label>Nome</label>
								<input type="text" name="nome" class="form-control" required value="{{ $marca->nome }}">
								<input type="hidden" required name="marca_id" class="form-control" required value="{{ $marca->id }}">
							</div>
							</div>
							<div class="col-md-12 pr-1">
								<div class="fileinput fileinput-new text-center" data-provides="fileinput">
								<div class="fileinput-new thumbnail">
								@if($marca->foto == 'Sem foto')
									<img src="{{ asset('admin/assets/img/image_placeholder.jpg') }}" alt="...">
								@else 
									<img src="{{ $marca->foto }}" alt="...">
								@endif
								</div>
								<div class="fileinput-preview fileinput-exists thumbnail"></div>
								<div>
									<span class="btn btn-rose btn-round btn-file">
									<span class="fileinput-new">Selecione imagem</span>
									<span class="fileinput-exists">Alterar</span>
									<input type="file" name="foto" />
									</span>
									<a href="#pablo" class="btn btn-danger btn-round fileinput-exists" data-dismiss="fileinput"><i class="fa fa-times"></i> Cancelar</a>
								</div>
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-md-12">
							<div class="form-group">
								<button type="submit" style="width: 100%;" class="btn btn-success pull-right"><b>Atualizar</b></button>
							</div>
							</div>
						</div>
						</form>
					</div>
				</div>
          	</div>
          	<div class="col-md-8">
				<div class="card">
					<div class="card-header">
						<h5 class="title">Modelos <a class="btn btn-info pull-right" style="color: white;" data-toggle="modal" data-target="#novomodelo">Novo modelo</a></h5>
					</div>
					<div class="card-body">
						<div class="table-responsive">
							<table class="table">
								<thead class=" text-primary">
									<th>Nome</th>
									<th>Status</th>
									<th>Capacidades (Gb)</th>
									<th class="text-right">Opções</th>
								</thead>
								<tbody>
								@foreach($marca->modelos as $modelo)
								<tr>
									<td>{{ $modelo->nome }}</td>
									<td>
										@if($modelo->status == 'ATIVO')
										<button class="btn btn-success btn-xs">Ativo</button>
										@else 
										<button class="btn btn-danger btn-xs">Inativo</button>
										@endif
									</td>
									<td>
										@foreach($modelo->capacidades as $capacidade)
										{{ $capacidade->memoria }} Gb (R$ {{ number_format($capacidade->valor/100,2,',','.') }}) <br>
										@endforeach
									</td>
									<td class="text-right">
										@if($modelo->status == 'INATIVO')
											<a href="{{route('admin.configuracoes.modelos.ativar',['id'=>$modelo->id])}}" class="btn btn-round btn-success btn-icon btn-sm">
												<i title="Ativo" class="fas fa-check"></i>
											</a>
										@else 
											<a href="{{route('admin.configuracoes.modelos.desativar',['id'=>$modelo->id])}}" class="btn btn-round btn-danger btn-icon btn-sm">
												<i title="Desativar" class="fas fa-times"></i>
											</a>
										@endif
										<a data-toggle="modal" data-target="#editarmodelo{{$modelo->id}}" title="Editar" class="btn btn-round btn-info btn-icon btn-sm edit"><i class="fas fa-edit"></i></a>

<div class="modal fade" id="editarmodelo{{$modelo->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg" role="document">
<div class="modal-content">
	<div class="modal-header">
		<h5 class="modal-title" id="exampleModalLabel">Editar modelo</h5>
	</div>
	<form action="{{route('admin.configuracoes.modelos.update',['id'=>$modelo->id])}}" method="post">
		@csrf
		<div class="modal-body">
		<div class="row">
			<div class="col-md-12 pr-1">
				<div class="form-group">
					<label class="form-label pull-left">Nome</label>
					<input type="text" name="nome" value="{{$modelo->nome}}" required class="form-control" />
					<input type="hidden" name="marca_id" required class="form-control" value="{{ $marca->id }}">
				</div>
			</div>


			<div class="col-md-12 capacidade">
				<div class="form-group col-md-12">


					

					<div class="col-md-12 capacidade">
						<div class="row">
							<label>Capacidades desse modelo 
								<button class="btn btn-round btn-success btn-icon btn-sm addCapacidade">
									<i title="Adicionar" class="fas fa-plus"></i>
								</button>
							</label>
						</div>
						<div class="form-group col-md-12">
							<div class="capacidades">
								@foreach($modelo->capacidades as $capacidade)
									<div class="inn row">
										<input type="hidden" name="ids[]" value="{{$capacidade->id}}">
										<div class="col-md-5">
											<input value="{{$capacidade->memoria}}" type="number" name="capacidades_memoria[]" required class="form-control" placeholder="Em Gb">
										</div>
										<div class="col-md-5">
											<input value="{{number_format($capacidade->valor/100,2,',','.') }}" type="text" name="capacidades_valor[]" required class="form-control money">
										</div>
										<div class="col-md-2">
											<button class="btn btn-round btn-danger btn-icon btn-sm removeUpdate" data-id="{{$capacidade->id}}">
												<i title="Desativar" class="fas fa-times"></i>
											</button>
										</div>	
									</div>			
								@endforeach
							</div>
						</div>
					</div>

					<div class="col-md-12 problema_modelo">
						<div class="row">
							<label>Problemas desse modelo 
								<button class="btn btn-round btn-success btn-icon btn-sm addProblemaModel">
									<i title="Adicionar" class="fas fa-plus"></i>
								</button>
							</label>
						</div>
						<div class="form-group col-md-12">
							
							
							<div class="problemas_modelo">
								@if($modelo->problemas)
								@foreach($modelo->problemas as $problema)
									@if($problema->status != 'INATIVO')
									<div class="inn row">
										<input type="hidden" name="ids_problemas[]" value="{{$problema->id}}">
										<div class="col-md-4">
											<input value="{{$problema->nome}}" type="text" name="problemas_modelo_md[]" required class="form-control" placeholder="Nome do problema">
										</div>
										<div class="col-md-3">
											<input type="text" value="{{number_format($problema->valor/100,2,',','.') }}" name="problemas_modelo_md_valor[]" required class="form-control money" placeholder="Preço">
										</div>
										<div class="col-md-4">
											<select name="problemas_modelo_md_tipo[]" required class="form-control">
												<option @if($problema->tipo =='MANUTENCAO') selected @endif value="MANUTENCAO" >Manutenção</option>
				                        		<option @if($problema->tipo=='VENDA') selected @endif value="VENDA">Venda seu usado</option>
											</select>
										</div>

										<div class="col-md-1">
											<button class="btn btn-round btn-danger btn-icon btn-sm removeProblemaUpdate" data-id="{{$problema->id}}">
												<i title="Desativar" class="fas fa-times"></i>
											</button>
										</div>
									</div>
									@endif
								@endforeach
								@endif
							</div>
						</div>
					</div>


				</div>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
		<button type="submit" class="btn btn-success">Salvar</button>
	</div>
	</form>
</div>
</div>
</div>
										</td>
									</tr>
                      			@endforeach
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
      
<div class="modal fade" id="novomodelo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg" role="document">
<div class="modal-content">
	<div class="modal-header">
		<h5 class="modal-title" id="exampleModalLabel">Novo modelo</h5>
	</div>
	<form action="{{ route('admin.configuracoes.modelos.create') }}" method="post">
	@csrf
	<div class="modal-body">
	<div class="row">
		<div class="col-md-12 pr-1">
			<div class="form-group">
				<label>Nome</label>
				<input type="text" name="nome" required class="form-control" />
				<input type="hidden" name="marca_id" required class="form-control" value="{{ $marca->id }}">
			</div>
		</div>
		<div class="col-md-12 capacidade">
			<div class="form-group col-md-12">
				<label>Capacidades desse modelo 
					<button class="btn btn-round btn-success btn-icon btn-sm addCapacidade">
						<i title="Adicionar" class="fas fa-plus"></i>
					</button>
				</label>
				<div class="capacidades">
					<div class="inn row">
						<input type="hidden" name="ids[]" value="0">
						<div class="col-md-5">
							<input type="number" name="capacidades_memoria[]" required class="form-control" placeholder="Em Gb">
						</div>
						<div class="col-md-5">
							<input type="text" name="capacidades_valor[]" required class="form-control money" placeholder="Preço">
						</div>
						<div class="col-md-2">
							<button class="btn btn-round btn-danger btn-icon btn-sm remove" data-id="0">
								<i title="Desativar" class="fas fa-times"></i>
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>


		<div class="col-md-12 problema_modelo">
			<div class="form-group col-md-12">
				<label>Problemas desse modelo 
					<button class="btn btn-round btn-success btn-icon btn-sm addProblemaModel">
						<i title="Adicionar" class="fas fa-plus"></i>
					</button>
				</label>
				<div class="problemas_modelo">
					<div class="inn row">
						<input type="hidden" name="ids_problemas[]" value="0">
						<div class="col-md-4">
							<input type="text" name="problemas_modelo_md[]" required class="form-control" placeholder="Nome do problema">
						</div>
						<div class="col-md-3">
							<input type="text" name="problemas_modelo_md_valor[]" required class="form-control money" placeholder="Preço">
						</div>
						<div class="col-md-4">
							<select name="problemas_modelo_md_tipo[]" required class="form-control">
								<option value="MANUTENCAO" >Manutenção</option>
                        		<option value="VENDA">Venda seu usado</option>
							</select>
						</div>

						<div class="col-md-1">
							<button class="btn btn-round btn-danger btn-icon btn-sm remove_problema_modelo" data-id="0">
								<i title="Desativar" class="fas fa-times"></i>
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-danger" data-dismiss="modal"><b>Cancelar</b></button>
		<button type="submit" class="btn btn-success"><b>Criar</b></button>
	</div>
	</form>
</div>
</div>
</div>
</div>
@endsection

@section('js')
<script type="text/javascript">
$(function(){
	
	$(document).on('click', '.addCapacidade', function(e){
		e.preventDefault();
		$(this).parents('div.capacidade').find(".capacidades").append(
		//$('#capacidades').append(
			'<div class="inn row">'+
				'<input type="hidden" name="ids[]" value="0">'+
				'<div class="col-md-5">'+
					'<input type="number" required class="form-control" name="capacidades_memoria[]" placeholder="Em Gb">'+
				'</div>'+
				'<div class="col-md-5">'+
					'<input type="text" required name="capacidades_valor[]" class="form-control money" placeholder="Preço">'+
				'</div>'+
				'<div class="col-md-2">'+
					'<button class="btn btn-round btn-danger btn-icon btn-sm remove" data-id="0">'+
						'<i title="Desativar" class="fas fa-times"></i>'+
					'</button>'+
				'</div>'+
			'</div>'
		);

		$('.money').maskMoney({prefix:'R$ ', allowNegative: true, thousands:'.', decimal:',', affixesStay: false});
	});

	$(document).on('click', '.removeUpdate', function(e){
		e.preventDefault();
        var qtd = $(this).parents('div.capacidade').find(".capacidades").find('.inn').length;
        if(qtd > 1){
            var id = $(this).data('id');
            var $remove = $(this);
            if(id!=0){
                $.ajax({
                    method:'POST',
                    url:"{{route('admin.configuracoes.modelos.removercapacidade')}}",
                    data:{capacidade_id:id},
                    headers: {
                        'X-CSRF-TOKEN': '{{csrf_token()}}'
                    },
                }).done(function(){
                    $remove.parents('div.inn').remove();
                }).fail(function(){
                    swal("Oops!", "Ocorreu um erro.", "warning");
                })
            }
            else{
                $(this).parents('div.inn').remove();
            }
        }
        else{
            swal("Oops!", "Você não pode remover todos.", "warning");
        }
	});

	$(document).on('click', '.removeProblemaUpdate', function(e){
		e.preventDefault();
        var qtd = $(this).parents('div.problema_modelo').find(".problemas_modelo").find('.inn').length;
        if(qtd > 1){
            var id = $(this).data('id');
            var $remove = $(this);
            if(id!=0){
                $.ajax({
                    method:'POST',
                    url:"{{route('admin.configuracoes.modelos.removerproblema')}}",
                    data:{problema_id:id},
                    headers: {
                        'X-CSRF-TOKEN': '{{csrf_token()}}'
                    },
                }).done(function(){
                    $remove.parents('div.inn').remove();
                }).fail(function(){
                    swal("Oops!", "Ocorreu um erro.", "warning");
                })
            }
            else{
                $(this).parents('div.inn').remove();
            }
        }
        else{
            swal("Oops!", "Você não pode remover todos.", "warning");
        }
	});

	$(document).on('click', '.remove', function(e){
		e.preventDefault();
        var qtd = $(this).parents('div.capacidade').find(".capacidades").find('.inn').length;
        if(qtd > 1){
            var id = $(this).data('id');
            var $remove = $(this);
            $(this).parents('div.inn').remove();
        }
        else{
            swal("Oops!", "Você não pode remover todos.", "warning");
        }
	});

	$(document).on('click', '.addProblemaModel', function(e){
		e.preventDefault();
		$(this).parents('div.problema_modelo').find(".problemas_modelo").append(
			'<div class="inn row">'+
				'<input type="hidden" name="ids_problemas[]" value="0">'+
				'<div class="col-md-4">'+
					'<input type="text" name="problemas_modelo_md[]" required class="form-control" placeholder="Nome do problema">'+
				'</div>'+
				'<div class="col-md-3">'+
					'<input type="text" name="problemas_modelo_md_valor[]" required class="form-control money" placeholder="Preço">'+
				'</div>'+
				'<div class="col-md-4">'+
					'<select name="problemas_modelo_md_tipo[]" required class="form-control">'+
						'<option value="MANUTENCAO" >Manutenção</option>'+
                		'<option value="VENDA">Venda seu usado</option>'+
					'</select>'+
				'</div>'+

				'<div class="col-md-1">'+
					'<button class="btn btn-round btn-danger btn-icon btn-sm remove_problema_modelo" data-id="0">'+
						'<i title="Desativar" class="fas fa-times"></i>'+
					'</button>'+
				'</div>'+
			'</div>'
		);

		$('.money').maskMoney({prefix:'R$ ', allowNegative: true, thousands:'.', decimal:',', affixesStay: false});
	});

	$(document).on('click', '.remove_problema_modelo', function(e){
		e.preventDefault();
        let qtd = $(this).parents('div.problema_modelo').find(".problemas_modelo").find('.inn').length;
        if(qtd > 1){
            var id = $(this).data('ids_problemas');
            var $remove = $(this);
            $remove.parents('div.inn').remove();

        }
        else{
            swal("Oops!", "Você não pode remover todos.", "warning");
        }
	});
});
</script>
@endsection