<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('enviar/email-contato','API\MailController@MailSend');
Route::post('email-recuperar/senha','API\MailController@RecuperarSenha');
Route::post('recuperar/senha','API\UsuarioController@recuperarSenha');

Route::get('juridico','API\ApiController@juridico');
Route::get('auth','API\ApiController@checkAuth');
Route::post('create/user','API\ApiController@createUser');
Route::post('login','API\JwtAuthenticateController@authenticate');
Route::post('esqueci/minha/senha','API\ApiController@createPasswordToken');
Route::post('update/password/forget','API\ApiController@updatePasswordForget');
Route::post('contato','API\ApiController@createContato');

/* Usuarios */
Route::get('verifica/email/login','API\UsuarioController@verificaEmailLogin');
Route::get('verifica/email','API\UsuarioController@verificaEmail');
Route::get('verifica/cpf','API\UsuarioController@verificaCpf');

/* Produtos */
Route::post('buscar/todos','API\ProdutoController@buscarTodosProdutos');
Route::get('buscar/por/categoria/{categoria_id}','API\ProdutoController@buscarPorCategoria');
Route::post('buscar/produto','API\ProdutoController@buscar');
Route::get('produtos/destaque','API\ProdutoController@produtosEmDestaque');
Route::get('produtos/relacionados/{produto_id}','API\ProdutoController@produtosRelacionados');
Route::get('produtos/relacionadoscarrinho','API\ProdutoController@produtosRelacionadosCarrinho');
Route::post('produtos/carrinho','API\ProdutoController@getProdutosCarrinho');/*Era para que mesmo?*/
Route::get('produto/{produto_id}','API\ProdutoController@getProduto');
Route::get('verifica/quantidade/{produto_id}/{quantidade}','API\ProdutoController@verificaQuantidadeProduto');
Route::post('retirar/categoria/filtro','API\ProdutoController@retirarCategoriaFiltro');
Route::get('categorias','API\ProdutoController@getCategoriasAtivas');

Route::post('calcular/frete','API\ApiController@calcularFrete');

/* Lojas */
Route::get('lojas', 'API\ApiController@indexLojas');
Route::get('lojas/nearby', 'API\ApiController@getLoja');


/* Aparelhos */
Route::get('marcas','API\AparelhoController@getMarcas');
Route::get('marca/{marca_id}','API\AparelhoController@getMarcaPorId');
Route::get('modelo/{modelo_id}','API\AparelhoController@getModeloPorId');
Route::get('modelos/{marca_id}','API\AparelhoController@getModelos');
Route::get('capacidades/{modelo_id}','API\AparelhoController@getCapacidades');
Route::get('capacidade/{capacidade_id}','API\AparelhoController@getCapacidadePorId');
Route::get('acessorios','API\AparelhoController@getAcessorios');
Route::post('acessorios/buscarporid','API\AparelhoController@getAcessoriosPorId');
Route::get('problemas/manutencao/{modelo_id}','API\AparelhoController@getProblemasManutencao');
Route::post('problemas/buscarporid','API\AparelhoController@getProblemasPorId');
Route::get('problemas/usado/{modelo_id}','API\AparelhoController@getProblemasUsado');
Route::post('aparelho/criar','API\AparelhoController@registerAparelho');
Route::get('aparelho/{id}','API\AparelhoController@listarInfoAparelho');
// Route::get('modelo/problemas/{id}','API\AparelhoController@listarProblemasModelo');

//Route::post('enviar/aparelho','API\ApiController@enviaAparelho');

/* Em teste ainda: Carrinho*/
Route::get('carrinho/iniciar','API\CarrinhoController@iniciaCarrinho');
Route::get('carrinho/atualizar','API\CarrinhoController@atualizarCarrinho');
//Route::get('carrinho/validartoken','API\CarrinhoController@validaToken');
Route::post('carrinho/adicionarproduto','API\CarrinhoController@adicionaProduto');
Route::post('carrinho/alterarquantidadeproduto','API\CarrinhoController@alteraQuantidadeProduto');
//Route::post('carrinho/removerproduto','API\CarrinhoController@removeProduto');
Route::get('carrinho/produtos/{token}','API\CarrinhoController@carrinhoProdutos');
Route::post('remove/quantidade/produto','API\CarrinhoController@removerProduto');
Route::post('remove/produto/carrinho','API\CarrinhoController@removerProdutoCarrinho');
Route::post('calcular/frete/carrinho','API\ApiController@calcularFreteCarrinho');

Route::group(['middleware' => 'jwt.auth'], function () use ($router) {
    Route::get('manutencao/downloadchancela/{id}','API\ApiController@geraChancela')->name('admin.manutencao.gerachancela');
    Route::post('calcular/frete/carrinho/logado','API\ApiController@calcularFreteCarrinhoLogado');
    /* Usuario */
    Route::post('alterar/senha','API\UsuarioController@alterarSenha');
    Route::post('alterar/email','API\UsuarioController@alterarEmail');
    Route::post('novo/telefone','API\UsuarioController@createTelefone');
    Route::post('alterar/telefone','API\UsuarioController@updateTelefone');
    Route::post('lertudo','API\UsuarioController@lerTudo');
    Route::get('lernotificacao/{id}','API\UsuarioController@lerNotificacao');
    Route::get('deletar/telefone/{telefone_id}','API\UsuarioController@deleteTelefone');
    Route::get('telefones','API\UsuarioController@telefones');
    //Route::get('notificacoes/lida/{notificacao_id}','API\ApiController@lerNotificacao');

    /* Endereco */
    Route::post('novo/endereco','API\EnderecoController@novoEndereco');
    Route::post('editar/endereco','API\EnderecoController@editarEndereco');
    Route::get('setar/endereco/{endereco_id}','API\EnderecoController@setarEndereco');
    Route::get('buscar/enderecos','API\EnderecoController@buscarEnderecos');
    Route::get('one/endereco/{id}','API\EnderecoController@getOneEndereco');
    Route::get('endereco/atual','API\EnderecoController@enderecoAtual');

    /* Cartao */
    Route::get('cartao/atual','API\CartaoController@cartaoAtual');
    Route::get('cartao/listar','API\CartaoController@getCartoes');
    Route::get('deletar/cartao/{cartao_id}','API\CartaoController@deletarCartao');

    Route::get('meus/cupons','API\ApiController@meusCupons');
    
    Route::post('codigo/promocional','API\ApiController@codigoPromocional');

    //Route::post('finalizar/compra','API\ApiController@finalizarCompra');
    /* Carrinho */
    Route::post('carrinho/finalizar','API\CarrinhoController@finalizarPedido');
    Route::get('minhascompras','API\CarrinhoController@minhasCompras');
    Route::get('compra/{id}','API\CarrinhoController@detalhesCompra');
    Route::get('compra/envio/{id}','API\CarrinhoController@detalhesEnvio');

    /* Aparelho */
    Route::post('preorcamento','API\AparelhoController@preOrcamento');
    Route::post('criaservico','API\AparelhoController@criaServicos');
    
    Route::get('minhasmanutencoes','API\AparelhoController@minhasManutencoes');
    Route::get('acompanharmanutencao/{id}','API\AparelhoController@acompanharManutencao');

    Route::get('minhasvendas','API\AparelhoController@minhasVendas');
    Route::get('acompanharvenda/{id}','API\AparelhoController@acompanharVenda');

    Route::post('aceitar/{servico_id}','API\AparelhoController@aceitarServico');
    Route::post('aceitarvenda/{servico_id}','API\AparelhoController@aceitarServicoVenda');
    Route::post('recusar/{servico_id}','API\AparelhoController@recusarServico');

    Route::get('link/pagamento/{servico_id}','API\AparelhoController@linkPagamento');

    Route::get('minhastrocas','API\AparelhoController@minhasTrocas');


    Route::get('notificacoes','API\UsuarioController@listarNotificacoes');
    Route::post('notificacao/visualizar','API\UsuarioController@visualizarNotificacao');
});
