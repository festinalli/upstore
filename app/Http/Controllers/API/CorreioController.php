<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use PhpSigep;
use App\Http\Controllers\API\ProdutoController;
use App\Telemetria;

class CorreioController extends Controller
{
   
    // private $ambiente = 'desenvolvimento';
    // private $usuario = 'sigep';
    // private $senha = 'n5f9t8';
    // private $codAdministrativo = '17000190';
    // private $contrato = '9992157880';
    // private $cartao = '0067599079';
    // private $cnpj = '34028316000103';
    private $user = 0;

    function __construct(){
         
        $accessDataParaAmbienteDeHomologacao = new \PhpSigep\Model\AccessDataHomologacao();
        $accessDataParaAmbienteDeHomologacao->setFromArray([
            'usuario'           => 'UP_STORE',
            'senha'             => 'upstore',
            'codAdministrativo' => '18296246',
            'numeroContrato'    => '9912448202',
            'cartaoPostagem'    => '0074383329',
            'cnpjEmpresa'       => '27613841000120', // Obtido no método 'buscaCliente'.
            'anoContrato'       => null, // Não consta no manual.
            'diretoria'         => new Diretoria(Diretoria::DIRETORIA_DR_BRASILIA), // Obtido no método 'buscaCliente'.
        ])


        $config = new \PhpSigep\Config();
        $config->setAccessData($accessDataParaAmbienteDeHomologacao);
        $config->setEnv(\PhpSigep\Config::ENV_PRODUCTION);
        $config->setCacheOptions(
            array(
                'storageOptions' => array(
                    // Qualquer valor setado neste atributo será mesclado ao atributos das classes
                    // "\PhpSigep\Cache\Storage\Adapter\AdapterOptions" e "\PhpSigep\Cache\Storage\Adapter\FileSystemOptions".
                    // Por tanto as chaves devem ser o nome de um dos atributos dessas classes.
                    'enabled' => false,
                    'ttl' => 10,// "time to live" de 10 segundos
                    'cacheDir' => sys_get_temp_dir(), // Opcional. Quando não inforado é usado o valor retornado de "sys_get_temp_dir()"
                ),
            )
        );
  
        \PhpSigep\Bootstrap::start($config);
      }
  
      public function calcPrecoPrazo(
        $cepOrigem,
        $cepDestino,
        $comprimento = 13.5,
        $largura = 18,
        $altura = 9,
        $peso = 1
        )
      {
        try{
            $dimensao = new \PhpSigep\Model\Dimensao();
            $dimensao->setTipo(\PhpSigep\Model\Dimensao::TIPO_PACOTE_CAIXA);
            $dimensao->setAltura($altura); // em centímetros
            $dimensao->setComprimento($comprimento); // em centímetros
            $dimensao->setLargura($largura); // em centímetros
    
            $params = new \PhpSigep\Model\CalcPrecoPrazo();
            $params->setAccessData(new \PhpSigep\Model\AccessDataHomologacao());
            $params->setCepOrigem($cepOrigem);
            $params->setCepDestino($cepDestino);
            $params->setServicosPostagem(array(new \PhpSigep\Model\ServicoDePostagem('04162'),new \PhpSigep\Model\ServicoDePostagem('04669')));
            $params->setAjustarDimensaoMinima(true);
            $params->setDimensao($dimensao);
            $params->setPeso($peso);// 150 gramas
            
            \Log::alert($peso);

            $phpSigep = new PhpSigep\Services\SoapClient\Real();
            $result = $phpSigep->calcPrecoPrazo($params);
    
            $data = [];

            foreach($result->getResult() as $frete) {
                $servicoObj = $frete->getServico();

                $servico = array(
                        'id' => $servicoObj->getCodigo(),
                        'nome' => $servicoObj->getNome(),
                );

                if(!$frete->getErroCodigo()) {


                    $data[] = array(
                        'produto_id' => 3,
                        'loja_id' => 2,
                        'prazo' => $frete->getPrazoEntrega(),
                        'servico' => $servico,
                        'valor' => $frete->getValor(),
                    );
                } else {
                    $data[] = array(
                        'erro' => true,
                        'erro_code' => $frete->getErroCodigo(),
                        'erro_name' => $frete->getErroMsg(),
                        'servico' => $servico,
                    );
                }
            }

            return $data;
        }catch(\Exception $e){

            $telemetry = new Telemetria;
            $telemetry->user_id = $this->user;
            $telemetry->metodo = 'CorreioController@calculaFrete';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return $e->getMessage();
        }
        
      }
  
      public function somaFrete($novo, $soma) {

        foreach($soma as $key => $frete) {
            foreach($novo as $freteNovo) {
                if($frete['servico']['id'] == $freteNovo['servico']['id']) {
                    
                    if(!isset($freteNovo['erro']) && !isset($frete['erro'])) {
                        $soma[$key]['valor'] = $frete['valor'] + $freteNovo['valor'];

                        if($freteNovo['prazo'] > $frete['prazo']) {
                            $soma[$key]['prazo'] = $freteNovo['prazo'];
                        }
                    } else {
                        if(isset($freteNovo['erro'])) {
                            $soma[$key] = $freteNovo;
                        }
                    }
                } else {

                }
            }
        }

        return $soma;
      }

      public function solicitarEtiquetas($qnt,$codServico){
        $accessDataDeHomologacao = new \PhpSigep\Model\AccessDataHomologacao();
        $usuario = trim((isset($_GET['usuario']) ? $_GET['usuario'] : $accessDataDeHomologacao->getUsuario()));
        $senha = trim((isset($_GET['senha']) ? $_GET['senha'] : $accessDataDeHomologacao->getSenha()));
        $cnpjEmpresa = $accessDataDeHomologacao->getCnpjEmpresa();
  
       $accessData = new \PhpSigep\Model\AccessData();
        $accessData->setUsuario($usuario);
        $accessData->setSenha($senha);
        $accessData->setCnpjEmpresa($cnpjEmpresa);
  
       $params = new \PhpSigep\Model\SolicitaEtiquetas();
        $params->setQtdEtiquetas($qnt);
        $params->setServicoDePostagem(new \PhpSigep\Model\ServicoDePostagem($codServico));
        $params->setAccessData($accessData);
  
       $phpSigep = new PhpSigep\Services\SoapClient\Real();
  
       return $phpSigep->solicitaEtiquetas($params); //o código da etiqueta fica em $var->result[0]->etiquetaSemDv
      }
  
      public function fecharPlpVariosServicos($pedido_id,$codigo)
      {
            $pedido = Pedido::find($pedido_id);
  
            //caso pedido não exista
            if(!$pedido) return back();
  
            //Verificar se o MOIP já negou a transação.
            if($pedido->status != 4) return back();
            //Verificar se o pedido não tem frete
  
            $solicitacao = Solicitacao::find($pedido->solicitacao_id);
            $carrinho = Carrinho::find($pedido->carrinho_id);
            $cliente = User::find($carrinho->user_id);
            $fornecedor = User::find($solicitacao->user_id);
  
            $diametro = sqrt(($solicitacao->largura*$solicitacao->largura) + ($solicitacao->comprimento*$solicitacao->comprimento));
            $dimensao = new \PhpSigep\Model\Dimensao();
            $dimensao->setAltura($solicitacao->altura);
            $dimensao->setLargura($solicitacao->largura);
            $dimensao->setComprimento($solicitacao->comprimento);
            $dimensao->setDiametro($diametro);
            $dimensao->setTipo(\PhpSigep\Model\Dimensao::TIPO_PACOTE_CAIXA);
  
            $destinatario = new \PhpSigep\Model\Destinatario();
            $destinatario->setNome($cliente->razao_social);
            $destinatario->setLogradouro($cliente->endereco_comercial);
            $destinatario->setNumero($cliente->numero);
            $destinatario->setComplemento('Sem complemento');
  
            $destino = new \PhpSigep\Model\DestinoNacional();
            $destino->setBairro($cliente->bairro);
            $destino->setCep(str_replace('-','',$cliente->cep));
            $destino->setCidade($cliente->cidade);
            $destino->setUf($cliente->estado);
  
            $etiqueta = new \PhpSigep\Model\Etiqueta();
            $etiqueta->setEtiquetaComDv($pedido->etiqueta_id);
  
            $servicoAdicional = new \PhpSigep\Model\ServicoAdicional();
            $servicoAdicional->setCodigoServicoAdicional(\PhpSigep\Model\ServicoAdicional::SERVICE_REGISTRO);
            // Se não tiver valor declarado informar 0 (zero)
            $servicoAdicional->setValorDeclarado(0);
  
            $encomenda = new \PhpSigep\Model\ObjetoPostal();
            $encomenda->setServicosAdicionais(array($servicoAdicional));
            $encomenda->setDestinatario($destinatario);
            $encomenda->setDestino($destino);
            $encomenda->setDimensao($dimensao);
            $encomenda->setEtiqueta($etiqueta);
            $encomenda->setPeso($solicitacao->peso);//0.500 = 500 gramas
            $encomenda->setServicoDePostagem(new \PhpSigep\Model\ServicoDePostagem((integer)$codigo));
  
            // *** DADOS DO REMETENTE *** //
            $remetente = new \PhpSigep\Model\Remetente();
            $remetente->setNome($fornecedor->razao_social);
            $remetente->setLogradouro($fornecedor->endereco_comercial);
            $remetente->setNumero($fornecedor->numero);
            $remetente->setComplemento('Sem complemento');
            $remetente->setBairro($fornecedor->bairro);
            $remetente->setCep(str_replace('-','',$fornecedor->cep));
            $remetente->setUf($fornecedor->estado);
            $remetente->setCidade($fornecedor->cidade);
            // *** FIM DOS DADOS DO REMETENTE *** //
            $encomendas[] = $encomenda;
  
          //}
          $plp = new \PhpSigep\Model\PreListaDePostagem();
          $plp->setAccessData(new \PhpSigep\Model\AccessDataHomologacao());
          $plp->setEncomendas($encomendas);
          $plp->setRemetente($remetente);
          // dd($plp);
          $phpSigep = new PhpSigep\Services\SoapClient\Real();
          $result = $phpSigep->fechaPlpVariosServicos($plp);
          //dd($result->getResult()->getIdPlp());
          //echo $result;
          //}
          if($result->getResult() == null){
               echo "Parece que esse produto já faz parte de uma pré lista de postagem. <br> Entre em contato com o administrador da plataforma para mais detalhes.";
             dd($result->getResult());
          }
        //Salvar id PLP
        $pedido->plp_id = $result->getResult()->getIdPlp();
        $pedido->update();
        //Imprimir etiqueta
        // Logo da empresa remetente
        $logoFile = asset('assets/images/favicon.png');
        //$pdf  = new \PhpSigep\Pdf\ListaDePostagem($plp, time());
        //dd($pdf->render('I'));
        //Parametro opcional indica qual layout utilizar para a chancela. Ex.: CartaoDePostagem::TYPE_CHANCELA_CARTA, CartaoDePostagem::TYPE_CHANCELA_CARTA_2016
        if($codigo == 40096) $layoutChancela = array(\PhpSigep\Pdf\CartaoDePostagem2016::TYPE_CHANCELA_SEDEX_2016);
        else if($codigo == 41068) $layoutChancela = array(\PhpSigep\Pdf\CartaoDePostagem2016::TYPE_CHANCELA_PAC_2016);
        else return 'erro';
        //$layoutChancela = array(); //array(\PhpSigep\Pdf\CartaoDePostagem2016::TYPE_CHANCELA_SEDEX_2016);
        $pdf = new \PhpSigep\Pdf\CartaoDePostagem2016($plp, time(), asset('assets/images/favicon.png'), $layoutChancela);
        $pdf->render('I');
        //return $result;
      }
  
      public function buscaCliente()
      {
        $accessData = new \PhpSigep\Model\AccessDataHomologacao();
  
       $phpSigep = new PhpSigep\Services\SoapClient\Real();
        $result = $phpSigep->buscaCliente($accessData);
  
       if (!$result->hasError()) {
            /** @var $buscaClienteResult \PhpSigep\Model\BuscaClienteResult */
            $buscaClienteResult = $result->getResult();
  
           // Anula as chancelas antes de imprimir o resultado, porque as chancelas não estão é liguagem humana
            $servicos = $buscaClienteResult->getContratos()->cartoesPostagem->servicos;
            foreach ($servicos as &$servico) {
                    $servico->servicoSigep->chancela->chancela = 'Chancelas anulada via código.';
            }
        }
        return $result;
      }
      
   
}
