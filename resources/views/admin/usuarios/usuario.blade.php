@extends('admin.layout')

@section('css')

@endsection

@section('main')
<div class="main-panel">
    <!-- Navbar -->
    
    @include('admin.navbar', ['title' => $usuario->nome ])
    
    <!-- End Navbar -->
    <div class="panel-header panel-header-sm">
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="title">Dados de perfil</h5>
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
                   <form action="{{ route('admin.perfil.update') }}" method="post" enctype="multipart/form-data">
                  @csrf
                        <div class="row">
                            <input type="hidden" value="{{$usuario->id}}" name="id">
                        <div class="col-md-6 pr-1">
                            <div class="form-group">
                            <label>Nome</label>
                            <input type="text" class="form-control" name="nome"  value="{{$usuario->nome}}">
                            </div>
                        </div>
                        <div class="col-md-6 px-1">
                            <div class="form-group">
                            <label>Email</label>
                            <input type="text" class="form-control" disabled="" value="{{$usuario->email}}" name="email">
                            </div>
                        </div>
                        </div>
                        <div class="row">
                        <div class="col-md-4 pr-1">
                            <div class="form-group">
                            <label>CPF</label>
                            <input type="text" disabled="" class="form-control" value="{{$usuario->documento}}">
                            </div>
                        </div>
                        <div class="col-md-4 pl-1">
                            <div class="form-group">
                            <label>Telefone</label>
                            <input type="text" disabled="" class="form-control" value="{{$usuario->telefone}}">
                            </div>
                        </div>
                        <div class="col-md-4 pl-1">
                            <div class="form-group">
                            <label>Data de nascimento</label>
                            <input type="text" disabled="" class="form-control" value="{{$usuario->data_nascimento}}">
                            </div>
                        </div>
                        </div>
                        <div class="row">
                        <div class="col-md-4 pr-1">
                            <div class="form-group">
                            <label>Cep</label>
                            <input type="text" class="form-control" disabled="" value="{{$usuario->cep}}">
                            </div>
                        </div>
                        <div class="col-md-4 px-1">
                            <div class="form-group">
                            <label>Cidade</label>
                            <input type="text" class="form-control" disabled="" value="{{$usuario->cidade}}">
                            </div>
                        </div>
                        <div class="col-md-4 pl-1">
                            <div class="form-group">
                            <label>Bairro</label>
                            <input type="text" class="form-control" disabled="" value="{{$usuario->bairro}}">
                            </div>
                        </div>
                        </div>
                        <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                            <label>Rua</label>
                            <input type="text" class="form-control" disabled="" placeholder="Home Address" value="{{$usuario->rua}}">
                            </div>
                        </div>
                        </div>

                          @csrf
                        <div class="col-md-6 px-1">
                            <div class="form-group">
                            <label>Nova senha</label>
                        <input type="password" class="form-control" name="passnovo"  >
                            </div>
                        </div>
                        <div class="col-md-6 px-1">
                            <div class="form-group">
                            <label>Confirme Senha</label>
                        <input type="password" class="form-control" name="passconf"  >
                            </div>
                        </div>
                    <div class="row">
                    <div class="col-md-12">
                      <div class="form-group">
                        <button class="btn btn-success pull-right" type="submit">Atualizar</button>
                      </div>
                    </div>
                  </div>
                    </form>
                </div>
            </div>
            </div>
            <div class="col-md-6">
                <div class="card card-user">
                    <div class="card-header">
                        <h5 class="title">Enderecos Cadastrados</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead class=" text-primary">
                                    <th>Rua</th>
                                    <th>Número</th>
                                    <th>Bairro</th>
                                    <th>Cidade</th>
                                    <th>Estado</th>
                                </thead>
                                <tbody>
                                    @forelse(\Auth::user()->enderecos as $e)
                                    <tr>
                                        <td>{{$e->rua}}</td>
                                        <td>{{$e->numero}}</td>
                                        <td>{{$e->bairro}}</td>
                                        <td>{{$e->cidade}}</td>
                                        <td>{{$e->estado}}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" style="text-align:center">Nenhum Endereço Cadastrado</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
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