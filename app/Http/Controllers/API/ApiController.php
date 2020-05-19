<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\API\UploadController;
use App\Http\Controllers\API\CorreioController;
use App\Http\Controllers\CorreioServicoController;
use App\Http\Controllers\MailController;

//MODELS
use App\User;
use App\Token;
use App\Telemetria;
use App\Contato;
use App\Codigo;
use App\Produto;
use App\Loja;
use App\EstoqueLoja;
use App\Endereco;
use App\Cartao;
use App\Telefone;
use App\Order;
use App\Servico;

use Auth;

class ApiController extends Controller
{
    private $token;
    private $user;

    public function __construct(Request $request)
    {
        if($request->header('token')){
            $this->token = $request->header('token');
            $this->user = Auth::id();
        }
    }
    
    //register
    public function createUser(Request $request)
    {
        
        $this->validate($request,[
            'nome' => 'required',
            'sobrenome' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:6',
            'documento' => 'required',
            'data_nascimento' => 'required',
            'telefone' => 'required',
            'sexo' => 'required'
        ]);
        
        try{
            $user = User::where('email',$request->email)->first();
            
            if($user){
                return response()->json([
                    'error' => 'Email já cadastrado.'
                ], 405);
            }

            $user = User::where('documento',$request->documento)->first();
            
            if($user){
                return response()->json([
                    'error' => 'Documento já cadastrado.'
                ], 405);
            }
            
            $user = new User;
            $user->nome = $request->nome;
            $user->sobrenome = $request->sobrenome;
            $user->email = $request->email;
            $user->documento = $request->documento;
            $user->data_nascimento = $request->data_nascimento;
            $user->telefone = $request->telefone;
            $user->sexo = $request->sexo;
            $user->password = app('hash')->make($request->password);
            $user->tipo = 'CLIENTE';
            $user->status = 'ATIVO';
            $user->save();

            try {
                $mailController = new MailController();
                if($mailController->confirmation($user)) {
                    \Log::alert("EMAIL JOB CRIADO COM SUCESSO");
                } else {
                    \Log::alert("[ERRO] EMAIL JOB ");
                }
            } catch(\Exception $e){
                \Log::alert("[EXCEPTION] EMAIL JOB");
                \Log::alert($e);
            }

            return response()->json([
                'user' => $user,
            ], 200);

        }catch(\Exception $e){

            $telemetry = new Telemetria;
            $telemetry->user_id = 0;
            $telemetry->metodo = 'createUser';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email'     => 'required|email',
            'password'  => 'required'
        ]);

        try{
            // Find the user by email
            $user = User::where('email', $request->email)->where('tipo','CLIENTE')->first();

            if (!$user) {
                return response()->json([
                    'error' => 'Email não encontrado'
                ], 405);
            }

            if($user->status == 'INATIVO'){
                return response()->json([
                    'error' => 'Conta inativa'
                ], 405);
            }

            if($user->status == 'EMAIL'){
                return response()->json([
                    'error' => 'Account email verification'
                ], 201);
            }

            // Verify the password and generate the token
            if (Hash::check($request->password, $user->password)) {
                return response()->json([
                    'user' => $user,
                ], 200);
            }

            // Bad Request response
            return response()->json([
                'error' => 'Senha inválida.'
            ], 405);

        }catch(\Exception $e){

            $telemetry = new Telemetria;
            $telemetry->user_id = 0;
            $telemetry->metodo = 'login';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
        
    }

    public function createPasswordToken(Request $request)
    {
        $this->validate($request,[
            'email' => 'required'
        ]);

        try{
            // Find the user by email
            $user = User::where('email', $request->email)->where('tipo','CLIENTE')->first();

            if (!$user) {
                return response()->json([
                    'error' => 'Email não encontrado'
                ], 405);
            }

            if($user->status == 'INACTIVE'){
                return response()->json([
                    'error' => 'Conta inativa'
                ], 405);
            }

            if($user->token){
                $token = $user->token;
                $token->tipo = 2;
                $token->enviado = false;
                $token->status = 'ATIVO';
                $token->token = str_random(9);
                $token->update();
            }else{
                $token = new Token;
                $token->user_id = $user->id;
                $token->tipo = 2;
                $token->enviado = false;
                $token->status = 'ATIVO';
                $token->token = str_random(9);
                $token->save();
            }
            
            return response()->json([
                'message' => 'Recuperação de senha gerada'
            ], 200);

        }catch(\Exception $e){

            $telemetry = new Telemetria;
            $telemetry->user_id = 0;
            $telemetry->metodo = 'esqueciSenha';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function updatePasswordForget(Request $request)
    {
        $this->validate($request,[
            'password' => 'required',
            'token' => 'required',
            'email' => 'required'
        ]);

        try{
            $user = User::where('email',$request->email)->where('tipo','CLIENTE')->first();

            if(!$user){
                return response()->json([
                    'error' => 'Usuário inválido'
                ], 405);
            }

            if($user->status == 'INATIVO'){
                return response()->json([
                    'error' => 'Usuário inativo'
                ], 405);
            }

            $token = Token::where('token',$request->token)->where('status','ATIVO')->where('user_id',$user->id)->first();

            if(!$token){
                return response()->json([
                    'error' => 'Token de recuperação inválido'
                ], 405);
            }

            $user->password = app('hash')->make($request->password);
            $user->update();

            return response()->json([
                'mensagem' => 'Senha alterada com sucesso'
            ], 200);

        }catch(\Exception $e){

            $telemetry = new Telemetria;
            $telemetry->user_id = 0;
            $telemetry->metodo = 'updatePasswordForget';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function createContato(Request $request)
    {
        $this->validate($request,[
            'nome' => 'required',
            'email' => 'required',
            'mensagem' => 'required'
        ]);

        try{

            $contato = Contato::create($request->all());
            $contato->save();

            return response()->json([
                'contato' => $contato
            ], 200);

        }catch(\Exception $e){

            $telemetry = new Telemetria;
            $telemetry->user_id = 0;
            $telemetry->metodo = '/contatos';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function calcularFrete(Request $request)
    {
        $this->validate($request,[
            'produtos' => 'required|array',
            'cep_origem' => 'required|string'
        ]);

        try{
            $correio =  new CorreioController;

            foreach($request->produtos as $produto_id){
                $produto = Produto::findOrFail($produto_id);

                $loja = Loja::first();

                $frete = $correio->calcPrecoPrazo($loja->cep, $request->cep_origem, $produto->comprimento, $produto->largura, $produto->altura, $produto->peso/1000);
            }


            return response()->json($frete);

        }catch(\Exception $e){

            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'calculaFrete';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            \Log::alert($e);

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function calcularFreteCarrinho(Request $request)
    {
        $this->validate($request,[
            'carrinho_token' => 'required',
            // 'endereco_id' => 'required',
        ]);
        
        try{
            $carrinho = Order::where('token',$request->carrinho_token)->first();

            if($request->input('endereco_id')) {
                $endereco = Endereco::where('user_id',Auth::id())->where('id',$request->endereco_id)->first();

                if(!$endereco){
                    return response()->json([
                        'mensagem' => 'Endereço inválido'
                    ], 400);
                }

                $cep_destino = str_replace('-','',$endereco->cep);
            }

            if($request->input('cep_origem')) {
                $cep_destino = str_replace('-','',$request->cep_origem);
            }

            $cep_destino = str_replace('.','',$cep_destino);
            $cep_destino = str_replace(',','',$cep_destino);

            $correio = new CorreioController;

            $frete = array();
            $soma_frete = null;

            foreach($carrinho->vendas as $venda){
                $cep_origem = $venda->loja->cep;
                $produto = $venda->produto;

                $peso = $produto->peso * $venda->quantidade;
                $altura = $produto->altura;
                $largura = $produto->largura * $venda->quantidade;
                $comprimento = $produto->comprimento * $venda->quantidade;

                $loja = $venda->loja;

                $frete = $correio->calcPrecoPrazo($loja->cep, $cep_destino, $comprimento, $largura, $altura, $peso/1000);
                
                if($soma_frete != null) {
                    $soma_frete = $correio->somaFrete($frete, $soma_frete);
                } else {
                    $soma_frete = $frete;
                }
            }
            
            return response()->json($soma_frete, 200);

        }catch(\Exception $e){

            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'calculaFreteCarrinho';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function calcularFreteCarrinhoLogado(Request $request)
    {
        $this->validate($request,[
            'carrinho_token' => 'required',
            // 'endereco_id' => 'required',
        ]);
        
        try{
            $carrinho = Order::where('token',$request->carrinho_token)->first();

            if($request->input('endereco_id')) {
                $endereco = Endereco::where('user_id',Auth::id())->where('id',$request->endereco_id)->first();

                if(!$endereco){
                    return response()->json([
                        'mensagem' => 'Endereço inválido'
                    ], 400);
                }

                $cep_destino = str_replace('-','',$endereco->cep);
            }

            if($request->input('cep_origem')) {
                $cep_destino = str_replace('-','',$request->cep_origem);
            }

            $cep_destino = str_replace('.','',$cep_destino);
            $cep_destino = str_replace(',','',$cep_destino);

            $correio = new CorreioController;

            $frete = array();
            $soma_frete = null;

            foreach($carrinho->vendas as $venda){
                $cep_origem = $venda->loja->cep;
                $produto = $venda->produto;

                $peso = $produto->peso * $venda->quantidade;
                $altura = $produto->altura;
                $largura = $produto->largura * $venda->quantidade;
                $comprimento = $produto->comprimento * $venda->quantidade;

                $loja = $venda->loja;

                $frete = $correio->calcPrecoPrazo($loja->cep, $cep_destino, $comprimento, $largura, $altura, $peso/1000);
                
                if($soma_frete != null) {
                    $soma_frete = $correio->somaFrete($frete, $soma_frete);
                } else {
                    $soma_frete = $frete;
                }
            }
            
            return response()->json($soma_frete, 200);

        }catch(\Exception $e){

            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'calculaFreteCarrinhoLogado';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function codigoPromocional(Request $request)
    {
        $this->validate($request,[
            'produto_id' => 'required',
            'codigo' => 'required'
        ]);

        try{

            $codigo = Codigo::where('user_id',$this->user)->where('codigo',$request->codigo)->first();

            if(!$codigo){
                return response()->json([
                    'error' => 'Código inválido'
                ], 405);
            }

            return response()->json([
                'codigo' => $codigo
            ], 200);

        }catch(\Exception $e){

            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = '/codigo/promocional';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function diminuiEstoque($loja_id,$produto_id,$quantidade)
    {
        try{
            $estoque = EstoqueLoja::where('loja_id',$loja_id)->where('produto_id',$produto_id)->first();
            $estoque->quantidade -= $quantidade;
            $estoque->update();

        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'diminuiEstoque';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function meusCupons()
    {
        try{
            $user = Auth::user();
            $cupons = $user->cuponsValidos;
            return response()->json([
                'cupons' => $cupons
            ], 200);

        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'alterarEmail';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return response()->json([
                'error' => $e->getMessage()
            ], 405);
        }
    }

    public function indexLojas() {
        $lojas = Loja::all();

        return response()->json($lojas);
    }

    public function getLoja(Request $request) {
        /* Por ainda não ter a geolocalizacao*/
        $loja = Loja::inRandomOrder()->first();

        return response()->json($loja);
    }

    public function geraChancela($servico_id,Request $request){
        try{
            // $servico = Servico::findOrFail($servico_id);

            // $correioController = new CorreioServicoController;
            
            // //Padrao para sedex 40096
            // $plp = $correioController->fecharPlpVariosServicos($servico->id,40096);

            // if($plp == -1){
            //     return redirect()->back()->with('danger','Parece que esse produto já faz parte de uma pré lista de postagem. Entre em contato com o administrador da plataforma para mais detalhes.');
            // }
            // if($plp == -2){
            //     return redirect()->back()->with('danger','Segundo erro.');                
            // }

        }catch(\Exception $e){
            // return redirect()->back()->with('danger','Erro telemetria');
        }

        try {
            $servico = Servico::findOrFail($servico_id);
            $correioController = new CorreioServicoController;
            return $plp = $correioController->downloadChancelaCliente($servico_id);
        } catch(\Exception $e) {
            dd($e);
        }
    }


    public function downloadChancela($id){
        try{

            // $servico = Servico::findOrFail($id);
            // $correioController = new CorreioServicoController;
            // $plp = $correioController->downloadChancela($id,40096);

            return redirect()->route('admin.venda',['id'=>$id]);
        }catch(\Exception $e){
            $telemetria = new Telemetria;
            $telemetria->user_id = \Auth::user()->id;
            $telemetria->metodo = 'RelatorioController@downloadChancela';
            $telemetria->descricao = $e->getMessage();
            $telemetria->save();

            throw($e);

            // return redirect()->back()->with('danger','Erro telemetria');
        }
    }

}
