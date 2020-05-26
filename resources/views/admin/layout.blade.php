<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="utf-8" />
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <title>
    Upstore
  </title>
  <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
  <link href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" rel="stylesheet">
  <!-- CSS Files -->
  <link href="{{ asset('admin/assets/css/bootstrap.min.css') }}" rel="stylesheet" />
  <link href="{{ asset('admin/assets/css/now-ui-dashboard.css?v=1.1.0') }}" rel="stylesheet" />
  <!-- CSS Just for demo purpose, don't include it in your project -->
  <link href="{{ asset('admin/assets/js/plugins/yadcf/jquery.dataTables.yadcf.css') }}" rel="stylesheet" />
  <link href="{{ asset('admin/assets/demo/demo.css') }}" rel="stylesheet" />
  @yield('css')
  <!-- Google Tag Manager -->
  <script>
    (function(w, d, s, l, i) {
      w[l] = w[l] || [];
      w[l].push({
        'gtm.start': new Date().getTime(),
        event: 'gtm.js'
      });
      var f = d.getElementsByTagName(s)[0],
        j = d.createElement(s),
        dl = l != 'dataLayer' ? '&l=' + l : '';
      j.async = true;
      j.src =
        'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
      f.parentNode.insertBefore(j, f);
    })(window, document, 'script', 'dataLayer', 'GTM-NKDMSK6');
  </script>
  <!-- End Google Tag Manager -->
  <style type="text/css">
    .panel-header{
      height: 0px !important;
    }
  </style>
</head>

<body class=" sidebar-mini ">
  <!-- Google Tag Manager (noscript) -->
  <noscript>
    <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NKDMSK6" height="0" width="0" style="display:none;visibility:hidden"></iframe>
  </noscript>
  <!-- End Google Tag Manager (noscript) -->
  <div class="wrapper ">
    <div class="sidebar" data-color="black">
      <!--
        Tip 1: You can change the color of the sidebar using: data-color="blue | green | orange | red | yellow"
    -->
      <div class="logo">
        <a href="#" class="simple-text logo-mini">
          UP
        </a>
        <a href="#" class="simple-text logo-normal">
          UPSTORE
        </a>
<!--         <div class="navbar-minimize">
          <button id="minimizeSidebar" class="btn btn-simple btn-icon btn-neutral btn-round">
            <i class="now-ui-icons text_align-center visible-on-sidebar-regular"></i>
            <i class="now-ui-icons design_bullet-list-67 visible-on-sidebar-mini"></i>
          </button>
        </div> -->
      </div>
      <div class="sidebar-wrapper">
        <div class="user">
          <div class="photo">
            @if(Auth::user()->foto)
              <img src="{{Auth::user()->foto}}" />
            @else
              <img src="{{ asset('admin/assets/img/user.png') }}" />
            @endif
          </div>
          <div class="info">
            <a data-toggle="collapse" href="#collapseExample" class="collapsed">
              <span>
                {{ Auth::user()->nome }}
                <b class="caret"></b>
              </span>
            </a>
            <div class="clearfix"></div>
            <div class="collapse" id="collapseExample">
              <ul class="nav">
                <li>
                  <a href="{{ route('admin.perfil') }}">
                    <span class="sidebar-mini-icon">MP</span>
                    <span class="sidebar-normal">Meu perfil</span>
                  </a>
                </li>
                </li>
                <li>
                  <a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                    <span class="sidebar-mini-icon">S</span>
                    <span class="sidebar-normal">Sair</span>
                  </a>
                </li>
              </ul>
            </div>
          </div>
        </div>
        <ul class="nav">
          @if(\Auth::user()->tipo == 'ADMIN')
          <li>
            <a href="{{ route('admin.usuarios') }}">
              <i class="now-ui-icons users_single-02"></i>
              <p>Usuários</p>
            </a>
          </li>
          @endif
          <li>
            <a href="{{ route('admin.venda.usado') }}">
              <i class="now-ui-icons tech_mobile"></i>
              <p>Venda seu usado</p>
            </a>
          </li>

          <li>
            <a href="{{ route('admin.manutencoes') }}">
              <i class="now-ui-icons ui-2_settings-90"></i>
              <p>Manutenção</p>
            </a>
          </li>

          <li>
            <a href="{{ route('admin.entradas') }}">
              <i class="now-ui-icons arrows-1_share-66"></i>
              <p>Aparelho como entrada</p>
            </a>
          </li>
          @if(\Auth::user()->tipo == 'ADMIN')
          <li>
            <a href="{{ route('admin.envios') }}">
              <i class="now-ui-icons shopping_delivery-fast"></i>
              <p>Envios</p>
            </a>
          </li>

          <li>
            <a data-toggle="collapse" href="#formsExamples">
              <i class="now-ui-icons shopping_cart-simple"></i>
              <p>
                Ecommerce
                <b class="caret"></b>
              </p>
            </a>
            <div class="collapse " id="formsExamples">
              <ul class="nav">
                <li>
                  <a href="{{ route('admin.produtos') }}">
                    <span class="sidebar-mini-icon">P</span>
                    <span class="sidebar-normal"> Produtos </span>
                  </a>
                </li>
                <li>
                  <a href="{{ route('admin.categorias') }}">
                    <span class="sidebar-mini-icon">C</span>
                    <span class="sidebar-normal"> Categorias </span>
                  </a>
                </li>
                
              </ul>
            </div>
          </li>
          <li>
            <a data-toggle="collapse" href="#relatorios">
              <i class="now-ui-icons business_chart-pie-36"></i>
              <p>
                Relatórios
                <b class="caret"></b>
              </p>
            </a>
            <div class="collapse " id="relatorios">
              <ul class="nav">
                <li>
                  <a href="{{ route('admin.vendas') }}">
                    <span class="sidebar-mini-icon">V</span>
                    <span class="sidebar-normal">Vendas </span>
                  </a>
                </li>
                <li>
                  <a href="{{ route('admin.relatorios.manutencoes') }}">
                    <span class="sidebar-mini-icon">M</span>
                    <span class="sidebar-normal">Manutenções </span>
                  </a>
                </li>
                <li>
                  <a href="{{ route('admin.relatorios.problemas') }}">
                    <span class="sidebar-mini-icon">P</span>
                    <span class="sidebar-normal">Problemas </span>
                  </a>
                </li>
                
              </ul>
            </div>
          </li>
          <li>
            <a data-toggle="collapse" href="#componentsExamples">
              <i class="now-ui-icons ui-1_settings-gear-63"></i>
              <p>
                Configurações
                <b class="caret"></b>
              </p>
            </a>
            <div class="collapse " id="componentsExamples">
              <ul class="nav">
                <li>
                  <a href="{{ route('admin.configuracoes.acessorios') }}">
                    <span class="sidebar-mini-icon">A</span>
                    <span class="sidebar-normal">Acessórios </span>
                  </a>
                </li>
                <li>
                  <a href="{{ route('admin.configuracoes.marcas') }}">
                    <span class="sidebar-mini-icon">M</span>
                    <span class="sidebar-normal">Marcas </span>
                  </a>
                </li>
                <li>
                  <a href="{{ route('admin.configuracoes.problemas') }}">
                    <span class="sidebar-mini-icon">P</span>
                    <span class="sidebar-normal">Problemas </span>
                  </a>
                </li>
                {{--  <li>
                  <a href="{{ route('admin.configuracoes.servicos') }}">
                    <span class="sidebar-mini-icon">S</span>
                    <span class="sidebar-normal">Serviços </span>
                  </a>
                </li>  --}}
                <li>
                  <a href="{{ route('admin.configuracoes.lojas') }}">
                    <span class="sidebar-mini-icon">L</span>
                    <span class="sidebar-normal">Lojas </span>
                  </a>
                </li>
                <li>
                  <a href="{{ route('admin.configuracoes.tecnicos') }}">
                    <span class="sidebar-mini-icon">T</span>
                    <span class="sidebar-normal">Técnicos </span>
                  </a>
                </li>
                
              </ul>
            </div>
          </li>
          
          @endif

          <li>
            <a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
              <i class="now-ui-icons ui-1_simple-remove"></i>
              <p>Sair</p>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
              @csrf
          </form>
          </li>
        </ul>
      </div>
    </div>
    @yield('main')
  </div>
  <!--   Core JS Files   -->
  <script src="{{ asset('admin/assets/js/core/jquery.min.js') }}"></script>
  <script src="{{ asset('admin/assets/js/core/popper.min.js') }}"></script>
  <script src="{{ asset('admin/assets/js/core/bootstrap.min.js') }}"></script>
  <script src="{{ asset('admin/assets/js/plugins/perfect-scrollbar.jquery.min.js') }}"></script>
  <script src="{{ asset('admin/assets/js/plugins/moment.min.js') }}"></script>
  
  <!--  Plugin for Switches, full documentation here: http://www.jque.re/plugins/version3/bootstrap.switch/ -->
  <script src="{{ asset('admin/assets/js/plugins/bootstrap-switch.js') }}"></script>
  <!--  Plugin for Sweet Alert -->
  <script src="{{ asset('admin/assets/js/plugins/sweetalert2.min.js') }}"></script>
  <!-- Forms Validations Plugin -->
  <script src="{{ asset('admin/assets/js/plugins/jquery.validate.min.js') }}"></script>
  <!--  Plugin for the Wizard, full documentation here: https://github.com/VinceG/twitter-bootstrap-wizard -->
  <script src="{{ asset('admin/assets/js/plugins/jquery.bootstrap-wizard.js') }}"></script>
  <!--	Plugin for Select, full documentation here: http://silviomoreto.github.io/bootstrap-select -->
  <script src="{{ asset('admin/assets/js/plugins/bootstrap-selectpicker.js') }}"></script>
  <!--  Plugin for the DateTimePicker, full documentation here: https://eonasdan.github.io/bootstrap-datetimepicker/ -->
  <script src="{{ asset('admin/assets/js/plugins/bootstrap-datetimepicker.js') }}"></script>
  <!--  DataTables.net Plugin, full documentation here: https://datatables.net/    -->
  <script src="{{ asset('admin/assets/js/plugins/jquery.dataTables.min.js') }}"></script>
  <!--	Plugin for Tags, full documentation here: https://github.com/bootstrap-tagsinput/bootstrap-tagsinputs  -->
  <script src="{{ asset('admin/assets/js/plugins/bootstrap-tagsinput.js') }}"></script>
  <!-- Plugin for Fileupload, full documentation here: http://www.jasny.net/bootstrap/javascript/#fileinput -->
  <script src="{{ asset('admin/assets/js/plugins/jasny-bootstrap.min.js') }}"></script>
  <!--  Full Calendar Plugin, full documentation here: https://github.com/fullcalendar/fullcalendar    -->
  {{--<script src="{{ asset('admin/assets/js/plugins/fullcalendar.min.js') }}"></script>--}}
  <!-- Vector Map plugin, full documentation here: http://jvectormap.com/documentation/ -->
  <script src="{{ asset('admin/assets/js/plugins/jquery-jvectormap.js') }}"></script>
  <!--  Plugin for the Sliders, full documentation here: http://refreshless.com/nouislider/ -->
  <script src="{{ asset('admin/assets/js/plugins/nouislider.min.js') }}"></script>
  <!--  Google Maps Plugin    -->
  {{--<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_KEY_HERE"></script>--}}
  <!-- Chart JS -->
  <script src="{{ asset('admin/assets/js/plugins/chartjs.min.js') }}"></script>
  <!--  Notifications Plugin    -->
  <script src="{{ asset('admin/assets/js/plugins/bootstrap-notify.js') }}"></script>

  <script src="{{ asset('admin/assets/js/plugins/yadcf/jquery.dataTables.yadcf.js') }}"></script>
  <!-- Control Center for Now Ui Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="{{ asset('admin/assets/js/now-ui-dashboard.min.js?v=1.1.0" type="text/javascript') }}"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
  <script src="//cdn.datatables.net/plug-ins/1.10.10/sorting/datetime-moment.js"></script>
  <script src="{{ asset('admin/assets/js/now-ui-dashboard.min.js?v=1.1.0" type="text/javascript') }}"></script>
  <!-- Now Ui Dashboard DEMO methods, don't include it in your project! -->
  {{--<script src="{{ asset('admin/assets/demo/demo.js') }}"></script>--}}
 {{--  <script>
    $(document).ready(function() {
      // Javascript method's body can be found in assets/js/demos.js
      demo.initDashboardPageCharts();

      demo.initVectorMap();

    });
  </script> --}}
  <script type="text/javascript" src="{{asset('maskMoney/dist/jquery.maskMoney.min.js')}}"></script>
  <script>
      $(function() {
          $('.money').maskMoney({prefix:'R$ ', allowNegative: true, thousands:'.', decimal:',', affixesStay: false});
      })
  </script>

  @yield('js')
</body>

</html>