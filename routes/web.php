<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*Route::get('/', function () {
    return view('welcome');
});*/
//Route::get('/home', 'HomeController@index')->name('home');

Auth::routes();

Route::get('/','Auth\LoginController@showLoginForm')->name('admin.login');
Route::get('/adm/login','Auth\LoginController@showLoginForm')->name('admin.login');

Route::post('adm/login','AuthController@login')->name('admin.post.login');

Route::group(['middleware' => 'jwt.auth'], function () use ($router) {
    Route::get('confirmar/email', 'API\UsuarioController@confirmarEmail')->name('confirmar.email');
});

Route::get('email', 'MailController@confirmation');

Route::get('teste', 'CronController@enviaBoletoEmail');






Route::get('moip/verifica/order/servicos', 'CronController@verificaPagamentoServicoManutencao');



Route::group(['middleware' => 'auth'], function () {
    
    Route::get('/adm/', function () {
        return redirect('/adm/perfil');
    });    
    
    Route::get('/adm/perfil','ViewAdminController@perfil')->name('admin.perfil');
    Route::post('adm/perfil/update','UsuarioController@updatePerfil')->name('admin.perfil.update');
    Route::post('adm/perfil/update/senha','UsuarioController@updateSenha')->name('admin.perfil.update.senha');

    //VENDA SEU USADO
    Route::get('/adm/vendaseuusado','VendaUsadoController@vendaSeuUsado')->name('admin.venda.usado');
    Route::get('/adm/usado/{servico_id}','VendaUsadoController@seuUsado')->name('admin.usado');
    Route::post('/adm/usado/mercadoriachegou/{id}','VendaUsadoController@mercadoriaChegou')->name('admin.usado.mercadoriachegou');
    Route::get('/adm/usado/orcamento/{id}','VendaUsadoController@analiseOrcamentoStatus')->name('admin.usado.orcamento');
    Route::post('/adm/usado/enviarproposta/{id}','VendaUsadoController@enviarProposta')->name('admin.usado.enviarproposta');
    Route::get('/adm/usado/pagamentorealizado/{id}','VendaUsadoController@pagamentoRealizado')->name('admin.usado.pagamentorealizado');

    //MANUTENÇÕES
    Route::get('/adm/manutencoes','ManutencaoController@manutencoes')->name('admin.manutencoes');
    Route::get('/adm/manutencao/{id}','ManutencaoController@manutencao')->name('admin.manutencao');
    Route::post('/adm/manutencao/mercadoriachegou/{id}','ManutencaoController@mercadoriaChegou')->name('admin.manutencao.mercadoriachegou');
    Route::get('/adm/manutencao/emanalise/{id}','ManutencaoController@colocarEmAnalise')->name('admin.manutencao.emanalise');
    Route::post('/adm/manutencao/enviarproposta/{id}','ManutencaoController@enviarProposta')->name('admin.manutencao.enviarproposta');
    Route::get('/adm/manutencao/clientepagou/{id}','ManutencaoController@clientepagou')->name('admin.manutencao.clientepagou');
    Route::get('/adm/manutencao/finalizarmanutencao/{id}','ManutencaoController@finalizarManutencao')->name('admin.manutencao.finalizarmanutencao');
    Route::post('/adm/manutencao/gerachancela/{servico_id}','ManutencaoController@geraChancela')->name('admin.manutencao.gerachancela');
    Route::get('/adm/manutencao/retirar/loja/{servico_id}','ManutencaoController@retirarLoja')->name('admin.manutencao.retirar.loja');
    Route::get('/adm/manutencao/downloadchancela/{id}','ManutencaoController@downloadChancela')->name('admin.manutencao.downloadChancela');
    Route::get('/adm/manutencao/downloadplp/{id}','ManutencaoController@downloadPlp')->name('admin.manutencao.downloadPlp');
    Route::get('/adm/manutencao/downloadOs/{id}','ManutencaoController@downloadOs')->name('admin.manutencao.downloadOs');
    Route::get('/adm/problema/update/valido/{id}','ProblemaController@updateValido')->name('admin.problema.update.valido');
    Route::get('/adm/acessorio/update/valido/{id}','AcessorioController@updateValido')->name('admin.acessorio.update.valido');
    Route::post('adm/manutencao/order/{order_id}/pagar', 'ManutencaoController@pagarOrder')->name('admin.manutencao.order.pagar');

    //SERIVCOS
    Route::post('/adm/servico/uploadfoto/{id}','ServicoController@midiaCreateFoto')->name('admin.servico.uploadfoto');
    Route::post('/adm/servico/uploadvideo/{id}','ServicoController@midiaCreateVideo')->name('admin.servico.uploadvideo');
    Route::get('/adm/servico/removermidia/{id}','ServicoController@removermidia')->name('admin.servico.removermidia');
    Route::post('/adm/servico/setarresponsavel','ServicoController@setarResponsavel')->name('admin.servico.setarresponsavel');
    Route::post('/adm/servico/alterarstatusacessorio','ServicoController@alterarStatusAcessorio')->name('admin.servico.alterarstatusacessorio');
    Route::post('/adm/servico/alterarstatusproblema','ServicoController@alterarStatusProblema')->name('admin.servico.alterarstatusproblema');
    Route::post('/adm/servico/adicionarobservacao/{id}','ServicoController@adicionarObservacao')->name('admin.servico.adicionarobservacao');
    Route::get('/adm/servico/removerobservacao/{id}','ServicoController@removerObservacao')->name('admin.servico.removerobservacao');
    Route::post('/adm/servico/reenviar/{id}','ServicoController@reenviar')->name('admin.servico.reenviar');

    //CELULAR COMO ENTRADA
    Route::get('/adm/entradas','AparelhoEntradaController@entradas')->name('admin.entradas');
    Route::get('/adm/entrada/{id}','AparelhoEntradaController@entrada')->name('admin.entrada');
    Route::post('/adm/entrada/mercadoriachegou/{id}','AparelhoEntradaController@mercadoriaChegou')->name('admin.entrada.mercadoriachegou');
    Route::get('/adm/entrada/orcamento/{id}','AparelhoEntradaController@analiseOrcamentoStatus')->name('admin.entrada.orcamento');
    Route::post('/adm/entrada/enviarproposta/{id}','AparelhoEntradaController@enviarProposta')->name('admin.entrada.enviarproposta');
    Route::post('/adm/entrada/geracodigo/{id}','AparelhoEntradaController@gerarCodigo')->name('admin.entrada.gerarcodigo');

    Route::post('/adm/notificacao/','NotificacaoController@visualizarNotificacao')->name('admin.notificacao');


    Route::group(['middleware' => 'admin'], function () {
        //USUARIOS
        Route::get('/adm/usuarios','UsuarioController@usuarios')->name('admin.usuarios');
        Route::get('/adm/usuario/ativar/{user_id}','UsuarioController@ativar')->name('admin.usuario.ativar');
        Route::get('/adm/usuario/desativar/{user_id}','UsuarioController@desativar')->name('admin.usuario.desativar');
        Route::get('adm/usuarios/{status}','UsuarioController@status')->name('admin.usuarios.status');
        Route::get('/adm/usuarios/perfil/{id}','UsuarioController@usuario')->name('admin.usuario');
        Route::get('/adm/exportar/usuario','UsuarioController@exportar')->name('admin.usuarios.exportar');
        Route::get('/adm/exportar/envios','EnvioController@exportar')->name('admin.envios.exportar');

        //ENVIOS
        Route::get('/adm/envios','EnvioController@envios')->name('admin.envios');

        //ECCOMERCE - PRODUTO + CATEGORIAS
        Route::get('/adm/produtos','ProdutoController@produtos')->name('admin.produtos');
        Route::post('adm/produtos/create','ProdutoController@create')->name('admin.produtos.create');
        Route::get('/adm/produto/{id}','ProdutoController@produto')->name('admin.produto');
        Route::post('adm/produtos/editar/{id}','ProdutoController@atualizar')->name('admin.produtos.atualizar');
        Route::get('adm/produtos/ativar/{id}','ProdutoController@ativar')->name('admin.produtos.ativar');
        Route::get('adm/produtos/desativar/{id}','ProdutoController@desativar')->name('admin.produtos.desativar');
        Route::post('adm/produtos/adicionarestoque/{id}','ProdutoController@adicionarEstoque')->name('admin.produtos.adicionarestoque');
        Route::post('adm/produtos/atualizarestoque/{id}','ProdutoController@atualizarEstoque')->name('admin.produtos.atualizarestoque');
        Route::get('adm/produtos/removerestoque/{id}','ProdutoController@removerEstoque')->name('admin.produtos.removerestoque');
        Route::get('adm/produtos/removerfoto/{id}','ProdutoController@removerFoto')->name('admin.produtos.removerfoto');
        Route::post('adm/produtos/alterarstatuspromo','ProdutoController@alterarStatusPromo')->name('admin.produtos.alterarstatuspromo');
        Route::post('adm/produtos/fotos/create','ProdutoController@createFoto')->name('admin.produtos.fotos.create');
        Route::post('adm/produtos/fotos/update','ProdutoController@updateFoto')->name('admin.produtos.fotos.update');


        Route::get('/adm/categorias','CategoriaController@categorias')->name('admin.categorias');
        Route::post('adm/categorias/create','CategoriaController@create')->name('admin.categorias.create');
        Route::get('adm/categorias/ativar/{marca_id}','CategoriaController@ativar')->name('admin.categorias.ativar');
        Route::get('adm/categorias/desativar/{marca_id}','CategoriaController@desativar')->name('admin.categorias.desativar');
        Route::post('adm/categorias/update','CategoriaController@update')->name('admin.categorias.update');

        //RELATORIOS
        Route::get('/adm/relatorios/vendas','RelatorioController@vendas')->name('admin.vendas');
        Route::get('/adm/relatorios/venda/{id}','RelatorioController@venda')->name('admin.venda');
        Route::post('/adm/relatorios/gerachancela/{order_id}','RelatorioController@geraChancela')->name('admin.gerachancela');
        Route::get('/adm/relatorios/downloadchancela/{id}','RelatorioController@downloadChancela')->name('admin.downloadChancela');
        Route::get('/adm/relatorios/downloadplp/{id}','RelatorioController@downloadPlp')->name('admin.downloadPlp');
        Route::get('/adm/relatorios/manutencoes','RelatorioController@relatorioManutencoes')->name('admin.relatorios.manutencoes');
        Route::get('/adm/relatorios/problemas','RelatorioController@relatoriosProblemas')->name('admin.relatorios.problemas');

        //CONFIGURACOES - MARCAS
        Route::get('/adm/configuracoes/marcas','ConfiguracaoController@marcas')->name('admin.configuracoes.marcas');
        Route::get('/adm/configuracoes/marca/perfil/{marca_id}','ConfiguracaoController@marca')->name('admin.configuracoes.marca');
        Route::get('adm/configuracoes/marca/ativar/{marca_id}','MarcaController@ativar')->name('admin.configuracoes.marca.ativar');
        Route::get('adm/configuracoes/marca/desativar/{marca_id}','MarcaController@desativar')->name('admin.configuracoes.marca.desativar');
        Route::post('adm/configuracoes/marca/create','MarcaController@create')->name('admin.configuracoes.marca.create');
        Route::post('adm/configuracoes/marca/update','MarcaController@update')->name('admin.configuracoes.marca.update');

        //CONFIGURAÇÕES - MODELOS
        Route::post('adm/configuracoes/modelos/create','ModeloController@create')->name('admin.configuracoes.modelos.create');
        Route::post('adm/configuracoes/modelos/removercapapacidade','ModeloController@removerCapacidade')->name('admin.configuracoes.modelos.removercapacidade');
        Route::post('adm/configuracoes/modelos/removerproblema','ModeloController@removerProblema')->name('admin.configuracoes.modelos.removerproblema');
        Route::post('adm/configuracoes/modelos/update/{id}','ModeloController@update')->name('admin.configuracoes.modelos.update');
        Route::get('adm/configuracoes/modelos/ativar/{id}','ModeloController@ativarModelo')->name('admin.configuracoes.modelos.ativar');
        Route::get('adm/configuracoes/modelos/desativar/{id}','ModeloController@desativarModelo')->name('admin.configuracoes.modelos.desativar');
        Route::post('adm/configuracoes/problemas/get/modelos','ModeloController@getModelosPorMarca')->name('adm.configuracoes.problemas.get.modelos');

        
        //CONFIGURACOES - PROBLEMAS
        Route::get('/adm/configuracoes/problemas','ProblemaController@problemas')->name('admin.configuracoes.problemas');
        Route::get('adm/configuracoes/problemas/ativar/{problema_id}','ProblemaController@ativar')->name('admin.configuracoes.problemas.ativar');
        Route::get('adm/configuracoes/problemas/desativar/{problema_id}','ProblemaController@desativar')->name('admin.configuracoes.problemas.desativar');    
        Route::post('adm/configuracoes/problemas/create','ProblemaController@create')->name('admin.configuracoes.problemas.create');
        Route::post('adm/configuracoes/problemas/update','ProblemaController@update')->name('admin.configuracoes.problemas.update');

        //Route::get('/adm/configuracoes/servicos','ConfiguracaoController@servicos')->name('admin.configuracoes.servicos');
        
        //CONFIGURACOES - LOJAS
        Route::get('/adm/configuracoes/lojas','LojaController@lojas')->name('admin.configuracoes.lojas');
        Route::post('adm/configuracoes/lojas/create','LojaController@create')->name('admin.configuracoes.lojas.create');

        //CONFIGURACOES - ACESSORIOS
        Route::get('/adm/configuraoes/acessorios','AcessorioController@acessorios')->name('admin.configuracoes.acessorios');
        Route::post('adm/configuracoes/acessorios/create','AcessorioController@create')->name('admin.configuracoes.acessorios.create');
        Route::post('adm/configuracoes/acessorios/update','AcessorioController@update')->name('admin.configuracoes.acessorios.update');
        Route::get('adm/configuracoes/acessorios/ativar/{acessorio_id}','AcessorioController@ativar')->name('admin.configuracoes.acessorios.ativar');
        Route::get('adm/configuracoes/acessorios/desativar/{acessorio_id}','AcessorioController@desativar')->name('admin.configuracoes.acessorios.desativar');

        //CONFIGURACOES - TECNICOS
        Route::get('/adm/configuracoes/tecnicos','TecnicoController@tecnicos')->name('admin.configuracoes.tecnicos');
        Route::post('adm/configuracoes/tecnicos/create','TecnicoController@create')->name('admin.configuracoes.tecnicos.create');
        Route::get('adm/configuracoes/tecnicos/ativar/{user_id}','TecnicoController@ativar')->name('admin.configuracoes.tecnicos.ativar');
        Route::get('adm/configuracoes/tecnicos/desativar/{user_id}','TecnicoController@desativar')->name('admin.configuracoes.tecnicos.desativar');
        Route::post('adm/configuracoes/tecnicos/update','TecnicoController@update')->name('admin.configuracoes.tecnicos.update');


        //CONFIGURACOES - TERMOS
        Route::get('/adm/configuracoes/juridico','ConfiguracaoController@juridico')->name('admin.configuracoes.juridico');
        Route::post('/adm/configuracoes/juridico/termos','ConfiguracaoController@uploadTermos')->name('admin.configuracoes.juridico.upload.termos');
        Route::post('/adm/configuracoes/juridico/politica','ConfiguracaoController@uploadPolitica')->name('admin.configuracoes.juridico.upload.politica');

    });


    
});
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
