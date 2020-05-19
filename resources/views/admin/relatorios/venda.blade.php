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

    @include('admin.navbar', ['title' => '#'.$venda->id])
      
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
					<li class="nav-item active">
						<a class="nav-link active" data-toggle="tab" href="#link5" role="tablist">
							<i class="now-ui-icons shopping_cart-simple"></i> Compra
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-toggle="tab" href="#link10" role="tablist">
							<i class="now-ui-icons shopping_box"></i> Postagem
						</a>
					</li>
				</ul>
				<div class="tab-content tab-space tab-subcategories">
					<div class="tab-pane active" id="link5">
					<div class="row">
						
						<div class="col-md-12">
							<b>Valor total da compra: R$ {{number_format($venda->valor_total/100,2,',','.')}}</b>
							
						</div>
						<div class="col-md-8">
							<table id="datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
								<thead>
								<tr>
									<th>Produto</th>
									<th>Valor</th>
									<th>Quantidade</th>
									<th>Subtotal</th>
								</tr>
								</thead>
								<tbody>
									@foreach($venda->vendas as $v)
									<tr>
										<td>{{$v->produto->nome}}</td>
										<td>R$ {{number_format($v->valor_unitario/100,2,',','.')}}</td>
										<td>{{$v->quantidade}}</td>
										<td>R$ {{number_format(($v->valor_unitario*$v->quantidade)/100,2,',','.')}}</td>
									</tr>
									@endforeach
								</tbody>
							</table>
						</div>
						<div class="col-md-4">
							<p>
								<b>Endereço:</b>
								<br>
								{{$venda->rua}}, {{$venda->numero}} {{$venda->bairro}} {{$venda->cidade}}/{{$venda->estado}} 
							</p>
							<p>
								<b>Método de coleta:</b>
								<br>
								{{$venda->frete_tipo}} R$ {{number_format($venda->frete_valor/100,2,',','.')}}, entrega em {{$venda->frete_prazo}} dias
							</p>
							<p>
								<b>Forma de pagamento:</b>
								<br>
								{{$venda->forma_pagamento}}
                            </p>
                            
                            <p>
                                <b>Status do Pagamento:</b>
                                <br>
                                @if($venda->status == 'PAGO')
                                <div class="alert alert-success text-center">
                                @else
                                <div class="alert alert-warning text-center">
                                @endif
                                {{$venda->status}}
                                </div>
                            </p>

							@if($venda->codigo_id != 0)
							<p style="color:red;">
								<b>Aparelho como entrada:</b>
								<br>
								Valor abatido de R$ {{number_format($venda->codigo->valor/100,2,',','.')}}
								<br>
								<a href="#" style="color: blue;"> Ver Detalhes</a> <!--rota que estava antes: admin.entrada-->
							</p>
							@endif
						</div>
						
					</div>
					</div>

					<div class="tab-pane" id="link10">
					<div class="row">
						<div class="col-md-12">
                            @if($venda->envio)
							<div class="row">
								<div class="col-md-4">
									<div class="form-group">
										<label>Chancela:</label>
                                        <p>
                                            <a href="{{route('admin.downloadChancela',['id'=>$venda->id])}}" target="_blank" style="color: blue;">
                                                Download
                                            </a>
                                        </p>
                                    </div>
                                    <div class="form-group">
                                        <label>Lista Postagem:</label>
                                        <p>
                                            <a href="{{route('admin.downloadPlp',['id'=>$venda->id])}}" target="_blank" style="color: blue;">
                                                Download
                                            </a>
                                        </p>
                                    </div>
									<div class="form-group">
										<label>Código de rastreio:</label>
										<p>
											{{$venda->envio->codigo_rastreio}}
										</p>
									</div>
									<div class="form-group">
										<label>Data estimada de chegada</label>
										<p>{{date('d/m/Y',strtotime($venda->created_at.' + '.$venda->frete_prazo.' days'))}}</p>
									</div>
									<div class="form-group">
										<label>Método de envio</label>
										<p>PAC | SEDEX</p>
									</div>
                                </div>
								<div class="col-md-8">
                                    <table id="datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>Linha to tempo</th>
                                                <th class="disabled-sorting text-right">Data</th>
                                            </tr>
										</thead>
										<tbody>
                                            @if($venda->envio)
	                                            @foreach($venda->envio->logsEnvio as $l)
	                                            <tr>
	                                                <td>{{$l->descricao}}</td>
	                                                <td class="text-right">
	                                                    {{date('d/m/Y H:i:s',strtotime($l->data))}}
	                                                </td>
	                                            </tr>
	                                            @endforeach
											@endif
										</tbody>
									</table>
								</div>
                            </div>
                            @else
                            <div class="row">
                                <div class="col-sm-12">
                                    <p class="text-center">
                                        <a href="{{ route('admin.gerachancela',['id'=>$venda->id]) }}" class="btn btn-primary btn-lg" onclick="event.preventDefault();document.getElementById('gerar').submit();">
                                            Clique para gerar chancela / etiqueta de envio
                                        </a>
                                        <form id="gerar" action="{{ route('admin.gerachancela',['id'=>$venda->id]) }}" method="POST" style="display: none;">
                                            @csrf
                                        </form>
                                    </p>
                                </div>
                            </div>
                            @endif
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
@endsection

@section('js')

@endsection