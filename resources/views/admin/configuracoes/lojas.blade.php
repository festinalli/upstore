@extends('admin.layout')

@section('css')

@endsection

@section('main')
	<div class="main-panel">
      <!-- Navbar -->
        @include('admin.navbar', ['title' => 'Lojas'])
      <!-- End Navbar -->
      <div class="panel-header">
      </div>
      <div class="content">
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title">Lojas cadastradas </h4>
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
                  <a class="btn btn-info" style="color: white;" data-toggle="modal" data-target="#novaloja"> Nova loja</a>
                </div>
                <table id="datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>
                      <th>Nome</th>
                      <th>Endereço</th>
                      <th>Cep</th>
                      <th>CNPJ</th>
                      <th>Status</th>
                      <th class="disabled-sorting text-right">Opções</th>
                    </tr>
                  </thead>
                  
                  <tbody>
                    @foreach($lojas as $loja)
                      <tr>
                        <td>{{ $loja->titulo }}</td>
                        <td>
                          {{ $loja->endereco }}, {{ $loja->bairro }}, {{ $loja->cidade }} - {{ $loja->estado }}
                        </td>
                        <td>{{ $loja->cep }}</td>
                        <td>{{ $loja->cnpj }}</td>
                        <td>
                          @if($loja->status == 'ATIVO')
                            <button class="btn btn-success btn-xs"><b>Ativo</b></button>
                          @else 
                            <button class="btn btn-danger btn-xs"><b>Inativo</b></button>
                          @endif
                        </td>
                        <td class="text-right">
                          @if($loja->status == 'ATIVO')
                            <a href="#" class="btn btn-round btn-danger btn-icon btn-sm remove"><i title="Desativar" class="fas fa-times"></i></a>
                          @else 
                            <a href="#" class="btn btn-round btn-success btn-icon btn-sm like"><i class="fas fa-check"></i></a>
                          @endif
                          <a href="#" data-toggle="modal" data-target="#editservico" class="btn btn-round btn-info btn-icon btn-sm edit"><i class="fas fa-edit"></i></a>
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

      <div class="modal fade" id="novaloja" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Nova loja</h5>
            </div>
            <form action="{{ route('admin.configuracoes.lojas.create') }}" method="post">
              @csrf
              <div class="modal-body">
                <div class="row">
                  <div class="col-md-12 pr-1">
                    <div class="form-group">
                      <label>Nome</label>
                      <input type="text" class="form-control" name="titulo" required>
                    </div>
                  </div>
                  <div class="col-md-12 pr-1">
                    <div class="form-group">
                      <label>Cnpj</label>
                      <input type="text" class="form-control" name="cnpj" required>
                    </div>
                  </div>
                  
                  <div class="col-md-12 pr-1">
                    <div class="form-group">
                      <label>Cep</label>
                      <input type="text" id="cep" class="form-control" name="cep" required>
                    </div>
                  </div>
                  <div class="col-md-12 pr-1">
                    <div class="form-group">
                      <label>Numero</label>
                      <input type="text" class="form-control" name="numero" required>
                    </div>
                  </div>
                  <div class="col-md-12 pr-1">
                    <div class="form-group">
                      <label>Rua</label>
                      <input type="text" id="rua" class="form-control" name="rua" required>
                    </div>
                  </div>
                  <div class="col-md-12 pr-1">
                    <div class="form-group">
                      <label>Bairro</label>
                      <input type="text" id="bairro" class="form-control" name="bairro" required>
                    </div>
                  </div>
                  <div class="col-md-12 pr-1">
                    <div class="form-group">
                      <label>Cidade</label>
                      <input type="text" id="cidade" class="form-control" name="cidade" required>
                    </div>
                  </div>
                  <div class="col-md-12 pr-1">
                    <div class="form-group">
                      <label>Estado</label>
                      <input type="text" id="uf" class="form-control" name="estado" required>
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

      <div class="modal fade" id="editservico" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Alterar loja</h5>
              
            </div>
            <div class="modal-body">
              <form>
                <div class="row">
                  <div class="col-md-12 pr-1">
                    <div class="form-group">
                      <label>Nome</label>
                      <input type="text" class="form-control">
                    </div>
                  </div>
                  <div class="col-md-12 pr-1">
                    <div class="form-group">
                      <label>Telefone</label>
                      <input type="text" class="form-control">
                    </div>
                  </div>
                  <div class="col-md-12 pr-1">
                    <div class="form-group">
                      <label>Cep</label>
                      <input type="text" class="form-control">
                    </div>
                  </div>
                  <div class="col-md-12 pr-1">
                    <div class="form-group">
                      <label>Rua</label>
                      <input type="text" class="form-control">
                    </div>
                  </div>
                  <div class="col-md-12 pr-1">
                    <div class="form-group">
                      <label>Bairro</label>
                      <input type="text" class="form-control">
                    </div>
                  </div>
                  <div class="col-md-12 pr-1">
                    <div class="form-group">
                      <label>Cidade</label>
                      <input type="text" class="form-control">
                    </div>
                  </div>
                  <div class="col-md-12 pr-1">
                    <div class="form-group">
                      <label>Estado</label>
                      <input type="text" class="form-control">
                    </div>
                  </div>
                  <div class="col-md-12 pr-1">
                    <div class="form-group">
                      <label>Horário de funcionamento (Descreva)</label>
                      <input type="text" class="form-control">
                    </div>
                  </div>
                  <div class="col-md-12 pr-1">
                    <div class="form-group">
                      <label>Serviços associados</label>
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

  <script type="text/javascript" >
    $(document).ready(function() {

        function limpa_formulário_cep() {
            // Limpa valores do formulário de cep.
            $("#rua").val("");
            $("#bairro").val("");
            $("#cidade").val("");
            $("#uf").val("");
            $("#ibge").val("");
        }
        
        //Quando o campo cep perde o foco.
        $("#cep").blur(function() {

            //Nova variável "cep" somente com dígitos.
            var cep = $(this).val().replace(/\D/g, '');

            //Verifica se campo cep possui valor informado.
            if (cep != "") {

                //Expressão regular para validar o CEP.
                var validacep = /^[0-9]{8}$/;

                //Valida o formato do CEP.
                if(validacep.test(cep)) {

                    //Preenche os campos com "..." enquanto consulta webservice.
                    $("#rua").val("...");
                    $("#bairro").val("...");
                    $("#cidade").val("...");
                    $("#uf").val("...");
                    $("#ibge").val("...");

                    //Consulta o webservice viacep.com.br/
                    $.getJSON("https://viacep.com.br/ws/"+ cep +"/json/?callback=?", function(dados) {

                        if (!("erro" in dados)) {
                            //Atualiza os campos com os valores da consulta.
                            $("#rua").val(dados.logradouro);
                            $("#bairro").val(dados.bairro);
                            $("#cidade").val(dados.localidade);
                            $("#uf").val(dados.uf);
                            $("#ibge").val(dados.ibge);
                        } //end if.
                        else {
                            //CEP pesquisado não foi encontrado.
                            limpa_formulário_cep();
                            alert("CEP não encontrado.");
                        }
                    });
                } //end if.
                else {
                    //cep é inválido.
                    limpa_formulário_cep();
                    alert("Formato de CEP inválido.");
                }
            } //end if.
            else {
                //cep sem valor, limpa formulário.
                limpa_formulário_cep();
            }
        });
    });

</script>
@endsection