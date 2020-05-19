<nav class="navbar navbar-expand-lg navbar-transparent  navbar-absolute bg-primary fixed-top">
  <div class="container-fluid">
      <div class="navbar-wrapper">
        <div class="navbar-toggle">
          <button type="button" class="navbar-toggler">
            <span class="navbar-toggler-bar bar1"></span>
            <span class="navbar-toggler-bar bar2"></span>
            <span class="navbar-toggler-bar bar3"></span>
          </button>
        </div>
        <a class="navbar-brand" href="#">{{ ucfirst($title ?? '') }}</a>
      </div>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigation" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-bar navbar-kebab"></span>
        <span class="navbar-toggler-bar navbar-kebab"></span>
        <span class="navbar-toggler-bar navbar-kebab"></span>
      </button>
      @if( \Auth::user()->tipo == 'TECNICO')
      <div class="collapse navbar-collapse justify-content-end" id="navigation">
        <ul class="navbar-nav">
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="http://example.com" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="now-ui-icons business_bulb-63"></i>
            <span class="badge badge-danger" style="font-size: 7pt">{{\App\Notificacao::where('user_id',\Auth::Id())->where('lido',0)->get()->count()}}</span>
              <p>
                <span class="d-lg-none d-md-block">Notificações</span>
              </p>
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                @forelse(\App\Notificacao::where('user_id',\Auth::Id())->where('lido',0)->get() as $n)
                    <a class="dropdown-item" href="{{route('admin.notificacao')}}" onclick="event.preventDefault();document.getElementById('logout-form{{$n->id}}').submit();" style="padding-bottom: 0px;padding-top: 0px">
                        <div class="alert alert-info alert-with-icon" data-notify="container">
                            <span data-notify="icon" class="now-ui-icons 
                            @if($n->tipo == 'M') ui-2_settings-90 
                            @elseif($n->tipo == 'V') tech_mobile 
                            @elseif($n->tipo == 'T') arrows-1_share-66 
                            @endif
                            "></span>
                            <span data-notify="message">{{$n->descricao}}</span>
                        </div>
                    </a>
                    <form id="logout-form{{$n->id}}" action="{{route('admin.notificacao')}}" method="POST" style="display: none;">
                        @csrf
                        <input type='hidden' name="notificacao_id" value="{{$n->id}}">
                    </form>
                @empty
                    <p class="dropdown-item">Sem Notificações novas</p>
                @endforelse
            </div>
          </li>
        </ul>
      </div>
      @endif
  </div>
</nav>