@extends('admin.layout')

@section('css')

@endsection

@section('main')
<div class="main-panel">
    <!-- Navbar -->
    
    @include('admin.navbar', ['title' => 'Envios'])
    
    <!-- End Navbar -->
    <div class="panel-header"></div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Filtros </h4>
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
                        <br>
                        <div class="row">
                          <div class="col-md-2">
                            <div class="row">
                              <div class="col-lg-12 col-md-12 col-sm-12">
                                <select id="selecionacliente" class="selectpicker" data-style="btn btn-default btn-round" multiple title="Selecione o cliente" data-size="7">
                                  @foreach($clientes as $cliente)
                                      <option value="{{ $cliente }}">{{ $cliente }}</option>
                                  @endforeach
                                </select>
                              </div>
                              
                            </div>
                            <div class="row">
                              <div class="col-md-12">
                                <input id="tagselecionacliente" type="text" value="" class="tagsinput" data-role="tagsinput" data-color="danger" />
                              </div>
                            </div>
                          </div>

                            <div class="col-md-3">
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <select id="selecionaTipos" class="selectpicker" data-style="btn btn-default btn-round" multiple title="Selecione o tipo" data-size="7">
                                            <option value="Manutenção">Manutenção</option>
                                            <option value="Aparelho como entrada">Aparelho como entrada</option>
                                            <option value="Venda seu usado">Venda seu usado</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                    <input id="tagselecionaTipos" type="text" class="tagsinput" data-role="tagsinput" data-color="danger" />
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="row" style="margin-top: 50px;"> 
                            <div class="col-md-2">
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <a href="{{ route('admin.envios.exportar') }}" class="btn btn-info btn-xs">Exportar</a>
                                    </div> 
                                </div>
                            </div>
                        </div>
                    
                    </div>
                    <div class="card-body">
                        <table id="datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Cliente</th>
                                    <th>Local atual</th>
                                    <th>Status</th>
                                    <th>Tipo</th>
                                    <th>Rastreamento</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($envios as $e)
                                <tr>
                                    @if($e->servico)
                                        <td>{{$e->id}}</td>
                                        <td>{{$e->servico->cliente->nome}}</td>
                                        <td>Local enviado pelo correio</td>
                                        <td>{{$e->servico->status}}</td>
                                        <td>
                                            @if($e->servico->tipo == 'M') Manutenção
                                            @elseif($e->servico->tipo == 'V') Venda seu usado
                                            @elseif($e->servico->tipo == 'T') Aparelho como Entrada
                                            @endif
                                        </td>
                                        <td>
                                            <a target="_blank" href="https://correios.postmon.com.br/rastreamento/?objeto={{$e->servico->etiqueta_id}}">
                                                {{$e->servico->etiqueta_id}}
                                            </a>
                                        </td>
                                    @elseif($e->order)
                                        <td>{{$e->id}}</td>
                                        <td>@if($e->order->usuario){{$e->order->usuario->nome}}@endif</td>
                                        <td>Local enviado pelo correio</td>
                                        <td>{{$e->order->status}}</td>
                                        <td>Compra</td>
                                        <td>
                                            @foreach($e->order->vendas as $v)
                                            <a target="_blank" href="https://correios.postmon.com.br/rastreamento/?objeto={{$v->etiqueta_id}}">
                                                {{$v->etiqueta_id}}
                                            </a>
                                            @endforeach
                                        </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- end content-->
                </div>
                <!--  end card  -->
            </div>
            <!-- end col-md-12 -->
        </div>
        <!-- end row -->
    </div>
</div>
@endsection

@section('js')
	<!--  DataTables.net Plugin, full documentation here: https://datatables.net/    -->
  <script src="{{ asset('admin/assets/js/plugins/jquery.dataTables.min.js') }}"></script>
  <script>
    $(document).ready(function() {
      $('#datatable').DataTable({
        "pagingType": "full_numbers",
        "lengthMenu": [
          [10, 25, 50, -1],
          [10, 25, 50, "All"]
        ],
        responsive: true,
        "language": {
	        "url": "{{asset('js/pt-br.json')}}"
	    },

      });

      var table = $('#datatable').DataTable();

      
    });
  </script>
  <script src="{{ asset('admin/assets/js/tabela.js') }}" defer></script>
@endsection