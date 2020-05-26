@extends('admin.layout')

@section('css')

@endsection

@section('main')
	<div class="main-panel">
      <!-- Navbar -->
      @include('admin.navbar', ['title' => 'Produtos'])
      <!-- End Navbar -->
      <div class="panel-header">
      </div>
      <div class="content">
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title">Produtos cadastrados </h4>

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
                

                <div class="row">
                  <div class="col-lg-3 col-md-3 col-sm-3">
                    <select id="selecionaProdutos" class="selectpicker" data-style="btn btn-default btn-round" multiple title="Filtrar por categoria" data-size="7">
                      @foreach($categorias as $categoria)
                          <option value="{{ $categoria->nome }}">{{ $categoria->nome }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="row">
                    <div class="col-md-12">
                      <input id="tagselecionaProdutos" type="text" value="" class="tagsinput" data-role="tagsinput" data-color="danger" />
                    </div>
                  </div>
                </div>

              </div>
              <div class="card-body">
                <div class="toolbar">
                    <a class="btn btn-info" style="color: white;" data-toggle="modal" data-target="#novoproduto"><b>Novo produto</b></a>
                    <!--<a class="btn btn-success" style="color: white;"><b>Aplicar filtros</b></a>-->
                </div>
                <table id="datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>
                      <th>Nome</th>
                      <th>Preço</th>
                      <th>Semi Novo</th>
                      <th>Promoção</th>
                      <th>Categorias</th>
                      <th>Status</th>
                      <th class="disabled-sorting text-right">Opções</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($produtos as $produto)
                      <tr>
                        <td @if(!$produto->qtd_estoque) class="text-danger" @endif>{{ $produto->nome }}</td>
                        <td  @if(!$produto->qtd_estoque) class="text-danger" @endif>R$ {{ number_format($produto->valor/100,2,',','.') }}</td>
                        <td  @if(!$produto->qtd_estoque) class="text-danger" @endif>
                          @if($produto->semi_novo == 1)
                            Sim
                          @else  
                            Não
                          @endif
                        </td>
                        <td  @if(!$produto->qtd_estoque) class="text-danger" @endif>
							@if($produto->descontoAtivo()->first())
								Sim
							@else
								Não
							@endif
                        </td>
                        <td  @if(!$produto->qtd_estoque) class="text-danger" @endif>
							@foreach($produto->categorias as $c)
								{{$c->categoria->nome}}<br>
							@endforeach
                        </td>
                        <td  @if(!$produto->qtd_estoque) class="text-danger" @endif>
                          @if($produto->status == 'ATIVO')
                            <button class="btn btn-success">Ativo</button>
                          @else   
                            <button class="btn btn-danger">Inativo</button>
                          @endif
                        </td>
                        <td class="text-right">
                          <a href="{{ route('admin.produtos.ativar',['id'=>$produto->id]) }}" class="btn btn-round btn-success btn-icon btn-sm like"><i class="fas fa-check"></i></a>
                          <a href="{{ route('admin.produtos.desativar',['id'=>$produto->id]) }}" class="btn btn-round btn-danger btn-icon btn-sm remove"><i title="Desativar" class="fas fa-times"></i></a>
                          <a href="{{ route('admin.produto',['id'=>$produto->id]) }}" class="btn btn-round btn-info btn-icon btn-sm edit"><i class="fas fa-eye"></i></a>
                        </td>
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

      <div class="modal fade" id="novoproduto" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Novo produto</h5>
            </div>
            <form method="post" enctype="multipart/form-data" action="{{ route('admin.produtos.create') }}">
              @csrf
              <div class="modal-body">
                <div class="row">
                  <div class="col-md-12 pr-1">
                    <div class="form-group">
                      <label>Nome do produto</label>
                      <input type="text" class="form-control" name="nome" required />
                    </div>
                  </div>
                  <div class="col-md-12 pr-1">
                    <div class="form-group">
                      <label>Preço</label>
                      <input type="text" class="form-control money" name="valor" required >
                    </div>
                  </div>
                  <div class="col-md-12 pr-1">
                    <div class="form-group">
                      <label>É Seminovo?</label>
                      <select class="form-control" name="semi_novo" required>
                        <option value="0">Não</option>
                        <option value="1">Sim</option>
                      </select>
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Selecione a marca</label>
                      <select class="form-control" name="marca_id" required>
                        <option value="0">Sem marca</option>
                        @foreach($marcas as $marca)
                          <option value="{{ $marca->id }}">{{ $marca->nome }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                  <div class="col-md-12 pr-1">
                    <div class="form-group">
                      <label>Possui capacidade?</label>
                      <input class="form-control" placeholder="Caso sim, entre com a capaciade em GB" type="number" name="capacidade" />
                    </div>
                  </div>

                      <div class="col-md-6 pr-1">
                        <div class="form-group">
                          <label>Peso (em gramas)</label>
                          <input class="form-control"  type="text" name="peso" required>
                        </div>
                      </div>

                      <div class="col-md-6 pr-1">
                        <div class="form-group">
                          <label>Altura (em centímetros)</label>
                          <input class="form-control"  type="text" name="altura" required>
                        </div>
                      </div>

                      <div class="col-md-6 pr-1">
                        <div class="form-group">
                          <label>Largura (em centímetros)</label>
                          <input class="form-control"  type="text" name="largura" value="" required>
                        </div>
                      </div>

                      <div class="col-md-6 pr-1">
                        <div class="form-group">
                          <label>Comprimento (em centímetros)</label>
                          <input class="form-control"  type="text" name="comprimento" required>
                        </div>
                      </div>

                  <div class="col-md-12 pr-1">
                    <div class="form-group">
                      <label>Descrição do produto</label>
                      <textarea rows="3" class="form-control" name="descricao" required></textarea>
                    </div>
                  </div>
                  <div class="col-md-12 pr-1">
                    <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                      <div>
                        <span class="btn btn-rose btn-round btn-file">
                          <span class="fileinput-new">Selecione a foto principal do produto</span>
                          <span class="fileinput-exists">Alterar</span>
                          <input type="file" name="foto" multiple required />
                        </span>
                        <a href="#pablo" class="btn btn-danger btn-round fileinput-exists" data-dismiss="fileinput"><i class="fa fa-times"></i> Cancelar</a>
                      </div>
                      <div class="fileinput-new thumbnail">
                        <img src="{{ asset('admin/assets/img/image_placeholder.jpg') }}" alt="...">
                      </div>
                      <div class="fileinput-preview fileinput-exists thumbnail"></div>
                    </div>
                  </div>
                  <div class="col-md-12 pr-1">
                    <div class="form-group">
                      <label>Selecione a(s) categoria(s) do produto</label>

                      @foreach($categorias as $categoria)
                        <div class="form-check">
                          <label class="form-check-label">
                            <input class="form-check-input" name="categorias[]" value="{{ $categoria->id }}" type="checkbox" />
                            <span class="form-check-sign"></span>
                            {{ $categoria->nome }}
                          </label>
                        </div>
                      @endforeach

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

    localStorage.removeItem('tabProduto')
  </script>
<script src="{{ asset('admin/assets/js/tabela.js') }}" defer></script>

@endsection