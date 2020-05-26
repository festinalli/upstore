@extends('admin.layout')

@section('css')

@endsection

@section('main')
	<div class="main-panel">
      <!-- Navbar -->
      @include('admin.navbar', ['title' => 'Problemas'])
      <!-- End Navbar -->
      <div class="panel-header">
      </div>
      <div class="content">
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title">Problemas cadastrados </h4>
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
                  <div class="col-md-2">
                    <div class="row">
                      <div class="col-lg-12 col-md-12 col-sm-12">
                        <select id="selecionamarcas1" class="selectpicker" data-style="btn btn-default btn-round" multiple title="Selecione a Marca" data-size="7">
                          @foreach($marcas as $marca)
                                <option value="{{ $marca }}">{{ $marca }}</option>
                          @endforeach
                        </select>
                      </div>
                      
                    </div>
                    <div class="row">
                      <div class="col-md-12">
                        <input id="tagselecionamarcas" type="text" value="" class="tagsinput" data-role="tagsinput" data-color="danger" />
                      </div>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="row">
                      <div class="col-lg-12 col-md-12 col-sm-12">
                        <select id="selecionamodelo" class="selectpicker" data-style="btn btn-default btn-round" multiple title="Selecione o modelo" data-size="7">
                          @foreach($modelos as $modelo)
                                <option value="{{ $modelo }}">{{ $modelo }}</option>
                          @endforeach
                        </select>
                      </div>
                      
                    </div>
                    <div class="row">
                      <div class="col-md-12">
                        <input id="tagselecionaModelo" type="text" value="" class="tagsinput" data-role="tagsinput" data-color="danger" />
                      </div>
                    </div>
                   
                  </div>

                  <div class="col-md-2">
                      <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                          <select id="selecionaTipos" class="selectpicker" data-style="btn btn-default btn-round" multiple title="Selecione o tipo" data-size="7">
                            @foreach($tipos as $tipo)
                                  <option value="{{ $tipo }}">{{ $tipo }}</option>
                            @endforeach
                          </select>
                        </div>
                        
                      </div>
                      <div class="row">
                        <div class="col-md-12">
                          <input id="tagselecionaTipos" type="text" value="" class="tagsinput" data-role="tagsinput" data-color="danger" />
                        </div>
                      </div>
                     
                    </div>
              </div>
              <div class="card-body">
                <!--
                <div class="toolbar">
                  <a class="btn btn-info" style="color: white;" data-toggle="modal" data-target="#novoproblema"><b>Novo problema</b></a>
                </div>
                -->
                <table id="datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>
                      <th>Nome</th>
                      <th>Marca</th>
                      <th>Moldelo</th>
                      <th>Preço</th>
                      <th>Tipo</th>
                      <th>Status</th>
                      <th class="disabled-sorting text-right">Opções</th>
                    </tr>
                  </thead>
                  
                  <tbody>
                    @foreach($problemas as $problema)

                      <tr>
                        <td>{{ $problema->nome }}</td>
                        <td>{{ $problema->modelo->marca->nome ?? '' }}</td>
                        <td>{{ $problema->modelo->nome ?? ''}}</td>
                        <td>R$ {{ number_format($problema->valor/100,2,',','.') }}</td>
                        <td>
                          @if($problema->tipo == 'MANUTENCAO')
                            Manutenção
                          @else 
                            Venda seu usado
                          @endif
                        </td>
                        <td>
                          @if($problema->status == 'ATIVO')
                            <button class="btn btn-success btn-xs"><b>Ativo</b></button>
                          @else 
                            <button class="btn btn-danger btn-xs"><b>Inativo</b></button>
                          @endif
                        </td>
                        <td class="text-right">
                          @if($problema->status == 'ATIVO')
                            <a href="{{ route('admin.configuracoes.problemas.desativar',['problema_id' => $problema->id]) }}" class="btn btn-round btn-danger btn-icon btn-sm remove"><i title="Desativar" class="fas fa-times"></i></a>
                          @else 
                            <a href="{{ route('admin.configuracoes.problemas.ativar',['problema_id' => $problema->id]) }}" class="btn btn-round btn-success btn-icon btn-sm like"><i class="fas fa-check"></i></a>
                          @endif
                          
                          <a href="#" data-toggle="modal" data-target="#editproblema{{ $problema->id }}" class="btn btn-round btn-info btn-icon btn-sm edit"><i class="fas fa-edit"></i></a>
                        </td>
                      </tr>

                      <div class="modal fade" id="editproblema{{ $problema->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title" id="exampleModalLabel">Alterar problema</h5>
                            </div>
                            <form action="{{ route('admin.configuracoes.problemas.update') }}" method="post">
                              @csrf
                              <div class="modal-body">
                                <div class="row">
                                  <div class="col-md-12 pr-1">
                                    <div class="form-group">
                                      <label>Nome</label>
                                      <input type="text" class="form-control" name="nome" required value="{{ $problema->nome }}">
                                      <input type="hidden" class="form-control" name="problema_id" value="{{ $problema->id }}">
                                    </div>
                                  </div>
                                  <div class="col-md-12 pr-1">
                                    <div class="form-group">
                                      <label>Preço</label>
                                      <input type="text" class="form-control money" required name="valor" value="{{ number_format($problema->valor/100,2,',','.') }}">
                                    </div>
                                  </div>
                                  <div class="col-md-4 pr-1">
                                    <div class="form-group">
                                      <label>Tipo</label>
                                      <select class="form-control" name="tipo" required >
                                        <option value="{{ $problema->tipo }}">
                                          @if($problema->tipo == 'MANUTENCAO')  
                                            Manutenção
                                          @else 
                                            Venda seu usado
                                          @endif
                                        </option>
                                        @if($problema->tipo != 'MANUTENCAO') <option value="MANUTENCAO">Manutenção</option> @endif
                                        @if($problema->tipo != 'VENDA') <option value="VENDA" >Venda seu usado</option> @endif
                                      </select>
                                    </div>
                                  </div>

                                  <div class="col-md-4 pr-1">
                                    <div class="form-group">
                                      <label>Marcas</label>
                                      <select name='marca_id' class="form-control marcaUpdate" onchange="OpenModelos(this.value)" id="marcaUpdate" required title="Selecione a marca" >
                                            <option value='0' >Selecione...</option>
                                          @foreach($marcas_all as $marca)
                                            <option  @if ($problema->modelo) 
                                                          @if($problema->modelo->marca->nome == $marca->nome ) selected @endif   
                                                    @endif
                                                      value='{{$marca->id}}'>{{$marca->nome}}</option>
                                          @endforeach                                          
                                      </select>
                                    </div>
                                  </div>


                                  <div class="col-md-4 pr-1">
                                    <div class="form-group">
                                      <label>Modelos</label>
                                      <select name='modelo_id' class="form-control modelosUpdate" required >

                                        @if ($problema->modelo) 
                                            @foreach( \App\Modelo::where('marca_id',$problema->modelo->marca->id)->get()  as $modelo )
                                              <option 
                                                

                                              value="{{$modelo->id}}" 
                                              @if($problema->modelo->nome == $modelo->nome) selected @endif >

                                              {{$modelo->nome}}

                                              </option>
                                            @endforeach
                                        @endif
                                        
                                        </option>
                                      </select>
                                    </div>
                                  </div>

                                                                    


                                </div>
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
      <!--
      <div class="modal fade" id="novoproblema" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Novo problema</h5>
            </div>
            <form action="{{ route('admin.configuracoes.problemas.create') }}" method="post">
              @csrf
              <div class="modal-body">
                <div class="row">
                  <div class="col-md-12 pr-1">
                    <div class="form-group">
                      <label>Nome</label>
                      <input type="text" name="nome" required class="form-control" >
                    </div>
                  </div>
                  <div class="col-md-12 pr-1">
                    <div class="form-group">
                      <label>Preço</label>
                      <input type="text" name="valor" required class="form-control money">
                    </div>
                  </div>
                  <div class="col-md-12 pr-1">
                    <div class="form-group">
                      <label>Tipo</label>
                      <select class="form-control" required name="tipo" required>
                        <option value="MANUTENCAO" >Manutenção</option>
                        <option value="VENDA">Venda seu usado</option>
                      </select>
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
      -->
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

    function OpenModelos(val){

          let codigo = val;
          if(codigo != 0  ){
            let data = {
                codigo:codigo,
                '_token':'{{csrf_token()}}'
            };

            $.ajax({  
              type: "POST",
              url: "{{route('adm.configuracoes.problemas.get.modelos')}}",
              data:data
            })
            .done(function(data) {
                // modelosUpdate
                $(".modelosUpdate").append(new Option("option text", "value"));

                $('.modelosUpdate')
                    .find('option')
                    .remove()
                    .end();


                    //.append('<option value="whatever">text</option>')
                    //.val('whatever')
                $('.modelosUpdate')
                  .append('<option value="0">Selecione...</option>');

                jQuery.each(data, function(arr,i) {
                  $('.modelosUpdate')
                  .append('<option value="'+i.id+'">'+i.nome+'</option>');
                });

            });

          }  
          
      };
  </script>
  <script src="{{ asset('admin/assets/js/tabela.js') }}" defer></script>
@endsection