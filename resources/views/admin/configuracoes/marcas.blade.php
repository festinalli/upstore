@extends('admin.layout')

@section('css')

@endsection

@section('main')
	<div class="main-panel">
      <!-- Navbar -->
      @include('admin.navbar', ['title' => 'Marcas'])
      <!-- End Navbar -->
      <div class="panel-header">
      </div>
      <div class="content">
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title">Marcas cadastradas </h4>
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
                <div class="toolbar">
                    <a class="btn btn-info" style="color: white;" data-toggle="modal" data-target="#novamarca"><b>Nova marca</b></a>
                </div>
                <table id="datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Nome</th>
                      <th>Status</th>
                      <th class="disabled-sorting text-right">Opções</th>
                    </tr>
                  </thead>
                  
                  <tbody>
                    @foreach($marcas as $marca)
                      <tr>
                        <td>
                          @if($marca->foto != 'Sem foto')
                            <img src="{{ $marca->foto }}" width="100" height="100"/>
                          @else 
                            Sem foto
                          @endif
                        </td>
                        <td>{{ $marca->nome }}</td>
                        <td>
                          @if($marca->status == 'ATIVO')
                            <button class="btn btn-success btn-xs"><b>Ativo</b></button>
                          @else 
                            <button class="btn btn-danger btn-xs"><b>Inativo</b></button>
                          @endif
                        </td>
                        <td class="text-right">
                          @if($marca->status == 'ATIVO')
                            <a href="{{ route('admin.configuracoes.marca.desativar',['marca_id' => $marca->id]) }}" class="btn btn-round btn-danger btn-icon btn-sm remove"><i title="Desativar" class="fas fa-times"></i></a>
                          @else
                            <a href="{{ route('admin.configuracoes.marca.ativar',['marca_id' => $marca->id]) }}" class="btn btn-round btn-success btn-icon btn-sm like"><i class="fas fa-check"></i></a>
                          @endif<a href="{{ route('admin.configuracoes.marca',['marca_id' => $marca->id]) }}" class="btn btn-round btn-info btn-icon btn-sm edit"><i class="fas fa-eye"></i></a>
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

      <div class="modal fade" id="novamarca" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Nova marca</h5>
            </div>
            <form action="{{ route('admin.configuracoes.marca.create') }}" method="post" enctype="multipart/form-data">
              <div class="modal-body">
                  @csrf
                  <div class="row">
                    <div class="col-md-12 pr-1">
                      <div class="form-group">
                        <label>Nome</label>
                        <input type="text" name="nome" class="form-control" required >
                      </div>
                    </div>
                    <div class="col-md-12 pr-1">
                      <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                        <div class="fileinput-new thumbnail">
                          <img src="{{ asset('admin/assets/img/image_placeholder.jpg') }}" alt="...">
                        </div>
                        <div class="fileinput-preview fileinput-exists thumbnail"></div>
                        <div>
                          <span class="btn btn-rose btn-round btn-file">
                            <span class="fileinput-new">Selecione imagem</span>
                            <span class="fileinput-exists">Alterar</span>
                            <input type="file" name="foto" required />
                          </span>
                          <a href="#pablo" class="btn btn-danger btn-round fileinput-exists" data-dismiss="fileinput"><i class="fa fa-times"></i> Cancelar</a>
                        </div>
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
  </script>
@endsection