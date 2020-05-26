@extends('admin.layout')

@section('css')

@endsection

@section('main')
	<div class="main-panel">
      <!-- Navbar -->
      
      @include('admin.navbar', ['title' => 'Usuários'])
      
      <!-- End Navbar -->
      <div class="panel-header">
      </div>
      <div class="content">
        <div class="row">
          <div class="col-md-12">
            
            <div class="card">
              <div class="card-header">
                <h4 class="card-title">Usuários cadastrados </h4>
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
                    <a href="{{ route('admin.usuarios') }}" class="btn btn-info" style="color: white;"> <b>Todos</b></a>
                    <a href="{{ route('admin.usuarios.status',['status' => 'ATIVO']) }}" class="btn btn-success" style="color: white;"> <b>Ativos</b></a>
                    <a href="{{ route('admin.usuarios.status',['status' => 'INATIVO']) }}" class="btn btn-danger" style="color: white;"> <b>Inativos</b></a>
                    <a href="{{ route('admin.usuarios.exportar') }}" class="btn btn-default" style="color: white;"> <b>Exportar usuários</b></a>
                </div>
                <table id="datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>
                      <th>Nome</th>
                      <th>Email</th>
                      <th>Status</th>
                      <th>Data de cadastro</th>
                      <th class="disabled-sorting text-right">Opções</th>
                    </tr>
                  </thead>
                  
                  <tbody>
                    @foreach($usuarios as $u)
                    <tr>
                      <td>{{$u->nome}} {{$u->sobrenome}}</td>
                      <td>{{$u->email}}</td>
                      <td>
                          @if($u->status == 'ATIVO')
                            <button class="btn btn-success btn-xs"><b>Ativo</b></button>
                          @else
                            <button class="btn btn-danger btn-xs"><b>Inativo</b></button>
                          @endif
                      </td>
                      <td>{{ date('d/m/Y',strtotime($u->created_at)) }}</td>
                      <td class="text-right">
                        @if($u->status == 'ATIVO')
                          <a href="{{ route('admin.usuario.desativar',['user_id'=>$u->id]) }}" class="btn btn-round btn-danger btn-icon btn-sm remove"><i title="Desativar" class="fas fa-times"></i></a>
                        @else
                          <a href="{{ route('admin.usuario.ativar',['user_id'=>$u->id]) }}" title="ativar" class="btn btn-round btn-success btn-icon btn-sm like"><i class="fas fa-check"></i></a>
                        @endif
                        
                        <a href="{{ route('admin.usuario',['id'=>$u->id]) }}" class="btn btn-round btn-info btn-icon btn-sm edit"><i class="fas fa-edit"></i></a>
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