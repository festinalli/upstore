@extends('admin.layout')

@section('css')

@endsection

@section('main')
	<div class="main-panel">
      <!-- Navbar -->
      
      @include('admin.navbar', ['title' => Auth::user()->nome])
      
      <!-- End Navbar -->
      <div class="panel-header panel-header-sm">
      </div>
      <div class="content">
        @if(session('success'))
          <div class="alert alert-success">{{session('success')}}</div>
        @elseif(session('danger'))
          <div class="alert alert-danger">{{session('danger')}}</div>
        @endif

        <div class="row">
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h5 class="title">Dados cadastrados</h5>
              </div>
              <div class="card-body">
                <form action="{{ route('admin.perfil.update') }}" method="post" enctype="multipart/form-data">
                  @csrf
                  <div class="row">
                    <div class="col-md-12 pr-1">
                      <div class="form-group">
                        <label>Nome</label>
                        <input type="text" class="form-control" name="nome" placeholder="Company" value="{{ Auth::user()->nome }}" required>
                      </div>
                      <div class="form-group">
                        <label>Email</label>
                        <input type="text" class="form-control" name="email" placeholder="Company" value="{{ Auth::user()->email }}" required>
                      </div>
                    </div>
                    <!--
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
	                          <input type="file" name="foto" />
	                        </span>
	                        <a href="#pablo" class="btn btn-danger btn-round fileinput-exists" data-dismiss="fileinput"><i class="fa fa-times"></i> Cancelar</a>
	                      </div>
	                    </div>
                    </div>
                    -->
                  </div>
                  
                  <div class="row">
                    <div class="col-md-12">
                      <div class="form-group">
                        <button class="btn btn-success pull-right" type="submit" >Atualizar</button>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card">
        	  <div class="card-header">
                <h5 class="title">Senha</h5>
              </div>
              <div class="card-body">
                <form action="{{ route('admin.perfil.update.senha') }}" method="post" enctype="multipart/form-data">
                  @csrf
                  <div class="row">
                    <div class="col-md-12 pr-1">
                      <div class="form-group">
                        <label>Senha atual</label>
                        <input type="password" class="form-control" name="passatual" required >
                      </div>
                      <div class="form-group">
                        <label>Nova senha</label>
                        <input type="password" class="form-control" name="passnovo" required >
                      </div>
                      <div class="form-group">
                        <label>Confirmar nova senha</label>
                        <input type="password" class="form-control" name="passconf" required >
                      </div>
                    </div>
                    
                  </div>
                  
                  <div class="row">
                    <div class="col-md-12">
                      <div class="form-group">
                        <button class="btn btn-success pull-right">Atualizar</button>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
@endsection

@section('js')
	<script type="text/javascript">
		$(function(){
			$('#addCapacidade').bind('click',function(){
				$('#capacidades').append('<div class="col-md-10"><input type="number" required class="form-control" placeholder="Em Gb"></div><div class="col-md-2"><a style="top:-7px;" href="#" class="btn btn-round btn-danger btn-icon btn-sm remove"><i title="Desativar" class="fas fa-times"></i></a></div>');
			});
		});
	</script>
@endsection