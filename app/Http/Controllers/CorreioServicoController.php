<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use PhpSigep;
use App\User;
use App\Servico;
use App\Loja;
use App\Telemetria;
use App\Envio;

class CorreioServicoController extends Controller
{
    function __construct(){
        $accessDataParaAmbienteDeHomologacao = new \PhpSigep\Model\AccessDataHomologacao();

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

    /**
     * Calculo de preço e prazo do frete
     * 
     */
    public function calcPrecoPrazo($cepOrigem,$cepDestino,$altura,$largura,$comprimento,$peso)
    {
        $dimensao = new \PhpSigep\Model\Dimensao();
        $dimensao->setTipo(\PhpSigep\Model\Dimensao::TIPO_PACOTE_CAIXA);
        $dimensao->setAltura(15); // em centímetros
        $dimensao->setComprimento(17); // em centímetros
        $dimensao->setLargura(12); // em centímetros

        $params = new \PhpSigep\Model\CalcPrecoPrazo();
        $params->setAccessData(new \PhpSigep\Model\AccessDataHomologacao());
        $params->setCepOrigem($cepOrigem);
        $params->setCepDestino($cepDestino);
        $params->setServicosPostagem(array(new \PhpSigep\Model\ServicoDePostagem('04162'),new \PhpSigep\Model\ServicoDePostagem('04669')));
        $params->setAjustarDimensaoMinima(true);
        $params->setDimensao($dimensao);
        $params->setPeso(0.150);// 150 gramas

        $phpSigep = new PhpSigep\Services\SoapClient\Real();
        $result = $phpSigep->calcPrecoPrazo($params);

        return $result->getResult();
    }

    /**
     * Gera etiquetas de postagem.
     * 
     */
	public function solicitarEtiquetas($qnt,$codServico)
	{
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

    /**
     * Gerar Plp para uma order.
     * 
     * Funções auxiliares.
     */
    public function setDestinatario($cliente,$endereco){
        $destinatario = new \PhpSigep\Model\Destinatario();
        $destinatario->setNome($cliente->nome.' '.$cliente->sobrenome);
        $destinatario->setLogradouro($endereco->rua);
        $destinatario->setNumero($endereco->numero);
        $destinatario->setComplemento('Sem complemento');
        return $destinatario;
    }

    public function setDestino($endereco){
        $destino = new \PhpSigep\Model\DestinoNacional();
        $destino->setBairro($endereco->bairro);
        $destino->setCep(str_replace('-','',$endereco->cep));
        $destino->setCidade($endereco->cidade);
        $destino->setUf($endereco->estado);
        return $destino;
    }

    public function setRemetente($loja){
        $remetente = new \PhpSigep\Model\Remetente();
        $remetente->setNome($loja->titulo);
        $remetente->setLogradouro($loja->endereco);
        $remetente->setNumero($loja->numero);
        $remetente->setComplemento('Sem complemento');
        $remetente->setBairro($loja->bairro);
        $remetente->setCep(str_replace('-','',$loja->cep));
        $remetente->setUf($loja->estado);
        $remetente->setCidade($loja->cidade);
        return $remetente;
    }

    public function setEncomenda($servicoAdicional,$destinatario,$destino,$frete_codigo,$servico,$nova){
        $altura = 15;
        $largura = 12;
        $comprimento = 17;
        $peso = 0.500;
        $diametro = sqrt(($largura*$largura) + ($comprimento*$comprimento));

        $etiqueta = new \PhpSigep\Model\Etiqueta();

        if(!$servico->etiqueta_id) {
            $etq = $this->solicitarEtiquetas(1,$frete_codigo);
            $servico->etiqueta_id = $etq->getResult()[0]->getEtiquetaSemDv();
            $servico->update();
        }
        
        $etiqueta->setEtiquetaSemDv($servico->etiqueta_id);
        $dimensao = new \PhpSigep\Model\Dimensao();
        $dimensao->setAltura($altura);
        $dimensao->setLargura($largura);
        $dimensao->setComprimento($comprimento);
        $dimensao->setDiametro($diametro);
        $dimensao->setTipo(\PhpSigep\Model\Dimensao::TIPO_PACOTE_CAIXA);

        $encomenda = new \PhpSigep\Model\ObjetoPostal();
        $encomenda->setServicosAdicionais(array($servicoAdicional));
        $encomenda->setDestinatario($destinatario);
        $encomenda->setDestino($destino);
        $encomenda->setDimensao($dimensao);
        $encomenda->setEtiqueta($etiqueta);
        $encomenda->setPeso($peso);//0.500 = 500 gramas

        $encomenda->setServicoDePostagem(new \PhpSigep\Model\ServicoDePostagem($frete_codigo));

        return $encomenda;
    }

    public function gerarPlpServico($servico_id,$nova,$frete_codigo){
        $encomendas = [];

        $servico = Servico::find($servico_id);
        if(!$servico) return back();
        if($servico->status != 'MANUTENCAO_FINALIZADA' && $servico->status != 'CRIADO') return back();

        $cliente = $servico->cliente;
        $loja = $servico->loja;
        $endereco = $cliente->enderecoAtual;

        $destinatario = $this->setDestinatario($cliente,$endereco);

        $destino = $this->setDestino($endereco);

        $servicoAdicional = new \PhpSigep\Model\ServicoAdicional();
        $servicoAdicional->setCodigoServicoAdicional(\PhpSigep\Model\ServicoAdicional::SERVICE_REGISTRO);
        $servicoAdicional->setValorDeclarado(0);
        $encomenda = $this->setEncomenda($servicoAdicional,$destinatario,$destino,$frete_codigo,$servico,$nova);
        $encomendas[] = $encomenda;


        $remetente = $this->setRemetente($loja);
        
        $plp = new \PhpSigep\Model\PreListaDePostagem();
        $plp->setAccessData(new \PhpSigep\Model\AccessDataHomologacao());
        $plp->setEncomendas($encomendas);
        $plp->setRemetente($remetente);
        
        return $plp;
    }

    /**
     * Gerar Plp para um servico.
     * 
     */
    public function fecharPlpVariosServicos($servico_id,$frete_codigo = '04162')
    {
        $plp = $this->gerarPlpServico($servico_id,1,$frete_codigo);
        
        $phpSigep = new PhpSigep\Services\SoapClient\Real();
        $result = $phpSigep->fechaPlpVariosServicos($plp);
        //dd($result->getResult());
        if($result->getResult() == null){
            return -1;
        }
        //Salvar id PLP
        $envio = new Envio;
        $envio->servico_id = $servico_id;
        $envio->codigo_rastreio = $result->getResult()->getIdPlp();
        $envio->save();
        return 1;
    }

    /**
     * Cria o pdf com a chancela, ou etiqueta de postagem de uma order
     * 
     */
    public function downloadChancela($servico_id,$frete_codigo = '04162'){
        //$servico = Servico::find($servico_id);
        $plp = $this->gerarPlpServico($servico_id,0,$frete_codigo);

        if($frete_codigo == '04162') $layoutChancela = array(\PhpSigep\Pdf\CartaoDePostagem2016::TYPE_CHANCELA_SEDEX_2016);
        else if($frete_codigo == '41068') $layoutChancela = array(\PhpSigep\Pdf\CartaoDePostagem2016::TYPE_CHANCELA_PAC_2016);
        else return false;


        $pdf = new \PhpSigep\Pdf\CartaoDePostagem2016($plp, time(), 'http://correios.com.br/++theme++correios.site.tema/images/logo_correios.png', $layoutChancela);
        $pdf->render('I');
        die();
    }

    /**
     * Cria o pdf com a lista de postagem, ou plp de uma order
     * 
     */
    public function downloadPlp($servico_id,$frete_codigo = '04162'){
        $plp = $this->gerarPlpServico($servico_id,0,$frete_codigo);

        if($frete_codigo == '04162') $layoutChancela = array(\PhpSigep\Pdf\CartaoDePostagem2016::TYPE_CHANCELA_SEDEX_2016);
        else if($frete_codigo == '41068') $layoutChancela = array(\PhpSigep\Pdf\CartaoDePostagem2016::TYPE_CHANCELA_PAC_2016);
        else return -2;

        $pdf = new \PhpSigep\Pdf\ListaDePostagem($plp, time());
        $pdf->render('I');
        die();
    }


    /**
     * Buscar cliente
     * 
     */
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



    //=======================================================================================================
    // Correio Novo
    //=======================================================================================================


    public function gerarPlpServicoCliente($servico_id,$frete_codigo){
        $encomendas = [];

        $servico = Servico::findOrFail($servico_id);
        if(!$servico) return back();
        if($servico->status != 'MANUTENCAO_FINALIZADA' && $servico->status != 'CRIADO') return back();


        // Destinatário



        // Remetente
        $endereco = $servico->loja;

        $destinatario = $this->setDestinatario($servico->cliente,$endereco);

        $destino = $this->setDestino($endereco);

        $servicoAdicional = new \PhpSigep\Model\ServicoAdicional();
        $servicoAdicional->setCodigoServicoAdicional(\PhpSigep\Model\ServicoAdicional::SERVICE_REGISTRO);
        $servicoAdicional->setValorDeclarado(0);
        $encomenda = $this->setEncomendaCliente($servicoAdicional,$destinatario,$destino,$frete_codigo,$servico);
        $encomendas[] = $encomenda;


        $remetente = $this->setRemetente($servico->loja);
        
        $plp = new \PhpSigep\Model\PreListaDePostagem();
        $plp->setAccessData(new \PhpSigep\Model\AccessDataHomologacao());
        $plp->setEncomendas($encomendas);
        $plp->setRemetente($remetente);
        
        return $plp;
    }

    /**
     * Cria o pdf com a chancela, ou etiqueta de postagem de uma order
     * 
     */
    public function downloadChancelaCliente($servico_id,$frete_codigo = '04162'){
        $servico = Servico::findOrFail($servico_id);
        $plp = $this->gerarPlpServicoCliente($servico_id,$frete_codigo);

        if($frete_codigo == '04162') $layoutChancela = array(\PhpSigep\Pdf\CartaoDePostagem2016::TYPE_CHANCELA_SEDEX_2016);
        else if($frete_codigo == '41068') $layoutChancela = array(\PhpSigep\Pdf\CartaoDePostagem2016::TYPE_CHANCELA_PAC_2016);
        else return false;


        $pdf = new \PhpSigep\Pdf\CartaoDePostagem2016($plp, time(), 'http://correios.com.br/++theme++correios.site.tema/images/logo_correios.png', $layoutChancela);
        $pdf->render('I');
        die();
    }


    public function setEncomendaCliente($servicoAdicional,$destinatario,$destino,$frete_codigo,$servico){
        $altura = 15;
        $largura = 12;
        $comprimento = 17;
        $peso = 0.500;
        $diametro = sqrt(($largura*$largura) + ($comprimento*$comprimento));

        $etiqueta = new \PhpSigep\Model\Etiqueta();

        if(!$servico->etiqueta_id) {
            $etq = $this->solicitarEtiquetas(1,$frete_codigo);
            $servico->etiqueta_id = $etq->getResult()[0]->getEtiquetaSemDv();
            $servico->update();
        }
        
        $etiqueta->setEtiquetaSemDv($servico->etiqueta_id);
        $dimensao = new \PhpSigep\Model\Dimensao();
        $dimensao->setAltura($altura);
        $dimensao->setLargura($largura);
        $dimensao->setComprimento($comprimento);
        $dimensao->setDiametro($diametro);
        $dimensao->setTipo(\PhpSigep\Model\Dimensao::TIPO_PACOTE_CAIXA);

        $encomenda = new \PhpSigep\Model\ObjetoPostal();
        $encomenda->setServicosAdicionais(array($servicoAdicional));
        $encomenda->setDestinatario($destinatario);
        $encomenda->setDestino($destino);
        $encomenda->setDimensao($dimensao);
        $encomenda->setEtiqueta($etiqueta);
        $encomenda->setPeso($peso);//0.500 = 500 gramas

        $encomenda->setServicoDePostagem(new \PhpSigep\Model\ServicoDePostagem($frete_codigo));

        return $encomenda;
    }

}
