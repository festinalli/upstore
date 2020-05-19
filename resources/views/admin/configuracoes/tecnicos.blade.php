@extends('admin.layout')

@section('css')

@endsection

@section('main')
	<div class="main-panel">
      <!-- Navbar -->
      @include('admin.navbar', ['title' => 'Técnicos'])
      <!-- End Navbar -->
      <div class="panel-header">
      </div>
      <div class="content">
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title">Técnicos cadastrados </h4>
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
                  <a class="btn btn-info" style="color: white;" data-toggle="modal" data-target="#novacategoria"><b>Cadastrar técnico</b></a>
                </div>
                <table id="datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>
                      <th>Nome</th>
                      <th>Email</th>
                      <th>Status</th>
                      <th class="disabled-sorting text-right">Opções</th>
                    </tr>
                  </thead>
                  
                  <tbody>
                    @foreach($tecnicos as $user)
                      <tr>
                        <td>{{ $user->nome }} {{ $user->sobrenome }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                          @if($user->status == 'ATIVO')
                            <button class="btn btn-success btn-xs"><b>Ativo</b></button>
                          @else 
                            <button class="btn btn-danger btn-xs"><b>Inativo</b></button>
                          @endif
                        </td>
                        <td class="text-right">
                          @if($user->status == 'ATIVO')
                            <a href="{{ route('admin.configuracoes.tecnicos.desativar',['user_id' => $user->id]) }}" class="btn btn-round btn-danger btn-icon btn-sm remove"><i title="Desativar" class="fas fa-times"></i></a>
                          @else 
                            <a href="{{ route('admin.configuracoes.tecnicos.ativar',['user_id' => $user->id]) }}" class="btn btn-round btn-success btn-icon btn-sm like"><i class="fas fa-check"></i></a>
                          @endif
                          <a href="#" data-toggle="modal" data-target="#editcategoria{{ $user->id }}" class="btn btn-round btn-info btn-icon btn-sm edit"><i class="fas fa-edit"></i></a>
                        </td>
                      </tr>

                      <div class="modal fade" id="editcategoria{{ $user->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title" id="exampleModalLabel">Editar técnico</h5>
                            </div>
                            <form action="{{ route('admin.configuracoes.tecnicos.update') }}" method="post" >
                              @csrf
                              <div class="modal-body">
                                
                                  <div class="row">
                                    <div class="col-md-12 pr-1">
                                      <div class="form-group">
                                        <label>Nome</label>
                                        <input type="text" class="form-control" name="nome" required value="{{ $user->nome }}">
                                        <input type="hidden" name="user_id" class="form-control" required value="{{ $user->id }}">
                                      </div>
                                      <div class="form-group">
                                        <label>Sobrenome</label>
                                        <input type="text" class="form-control" name="sobrenome" required value="{{ $user->sobrenome }}">
                                      </div>
                                    </div>
                                    <div class="col-md-12 pr-1">
                                      <div class="form-group">
                                        <label>Email</label>
                                        <input type="text" class="form-control" required name="email" value="{{ $user->email }}">
                                      </div>
                                    </div>
                                    
                                  </div>
                                
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal"><b>Cancelar<b></button>
                                <button type="submit" class="btn btn-success"><b>Atualizar</b></button>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
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

      <div class="modal fade modal-ng" id="novacategoria" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Novo técnico</h5>
            </div>
            <form method="post" action="{{ route('admin.configuracoes.tecnicos.create') }}">
              @csrf
              <div class="modal-body">
                
                  <div class="row">
                    <div class="col-md-12 pr-1">
                      <div class="form-group">
                        <label>Nome</label>
                        <input type="text" class="form-control" required name="nome">
                      </div>
                    </div>
                    <div class="col-md-12 pr-1">
                      <div class="form-group">
                        <label>Sobrenome</label>
                        <input type="text" class="form-control" required name="sobrenome">
                      </div>
                    </div>
                    <div class="col-md-12 pr-1">
                      <div class="form-group">
                        <label>Email</label>
                        <input type="text" class="form-control" required name="email" >
                      </div>
                    </div>
                    <div class="col-md-12 pr-1">
                      <div class="form-group">
                        <label>Senha</label>
                        <input type="password" class="form-control" required name="password">
                      </div>
                    </div>
                    <div class="col-md-12 pr-1">
                      <div class="form-group">
                        <label>Confirmar senha</label>
                        <input type="password" class="form-control" required name="conf_password">
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