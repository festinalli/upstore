@extends('admin.layout')

@section('css')

@endsection

@section('main')
	<div class="main-panel">
      <!-- Navbar -->
      @include('admin.navbar', ['title' => 'Serviços'])
      <!-- End Navbar -->
      <div class="panel-header">
      </div>
      <div class="content">
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title">Serviços cadastrados </h4>
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
                <a class="btn btn-info" style="color: white;" data-toggle="modal" data-target="#novoservico"> Novo serviço</a>
                <br>
              </div>
              <div class="card-body">
                <div class="toolbar">
                  <!--        Here you can write extra buttons/actions for the toolbar              -->
                </div>
                <table id="datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>
                      <th>Nome</th>
                      <th>Status</th>
                      <th class="disabled-sorting text-right">Opções</th>
                    </tr>
                  </thead>
                  
                  <tbody>
                    <tr>
                      <td>Nome do serviço</td>
                      <td>Ativo / Inativo</td>
                      <td class="text-right">
                        <a href="#" class="btn btn-round btn-success btn-icon btn-sm like"><i class="fas fa-check"></i></a>
                        <a href="#" class="btn btn-round btn-danger btn-icon btn-sm remove"><i title="Desativar" class="fas fa-times"></i></a>
                        <a href="#" data-toggle="modal" data-target="#editservico" class="btn btn-round btn-info btn-icon btn-sm edit"><i class="fas fa-edit"></i></a>
                      </td>
                    </tr>
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

      <div class="modal fade" id="novoservico" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Novo serviço</h5>
              
            </div>
            <div class="modal-body">
              <form>
                <div class="row">
                  <div class="col-md-12 pr-1">
                    <div class="form-group">
                      <label>Nome</label>
                      <input type="text" class="form-control" placeholder="Company">
                    </div>
                  </div>
                  <div class="col-md-12 pr-1">
                    <div class="form-group">
                      <label>Problemas associados</label>
                      <div class="form-check">
                        <label class="form-check-label">
                          <input class="form-check-input" type="checkbox">
                          <span class="form-check-sign"></span>
                          First Checkbox
                        </label>
                      </div>
                      <div class="form-check">
                        <label class="form-check-label">
                          <input class="form-check-input" type="checkbox">
                          <span class="form-check-sign"></span>
                          Second Checkbox
                        </label>
                      </div>
                    </div>
                  </div>
                </div>
              </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
              <button type="button" class="btn btn-success">Adicionar</button>
            </div>
          </div>
        </div>
      </div>

      <div class="modal fade" id="editservico" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Alterar servico</h5>
              
            </div>
            <div class="modal-body">
              <form>
                <div class="row">
                  <div class="col-md-12 pr-1">
                    <div class="form-group">
                      <label>Nome</label>
                      <input type="text" class="form-control" placeholder="Company" value="Nome do serviços">
                    </div>
                  </div>

                  <div class="col-md-12 pr-1">
                    <div class="form-group">
                      <label>Problemas associados</label>
                      <div class="form-check">
                        <label class="form-check-label">
                          <input class="form-check-input" type="checkbox">
                          <span class="form-check-sign"></span>
                          First Checkbox
                        </label>
                      </div>
                      <div class="form-check">
                        <label class="form-check-label">
                          <input checked="" class="form-check-input" type="checkbox">
                          <span class="form-check-sign"></span>
                          Second Checkbox
                        </label>
                      </div>
                    </div>
                  </div>
                </div>
              </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
              <button type="button" class="btn btn-success">Atualizar</button>
            </div>
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