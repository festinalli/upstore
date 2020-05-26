<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use PhpParser\Node\Stmt\Return_;

use Moip\Moip;
use Moip\Auth\BasicAuth;
use Moip\Auth\OAuth;

use App\Notificacao;
use App\Telemetria;
use App\User;
use App\Carrinho;
use App\Plataforma;
use App\Cartao;
use App\Produtousuario;
use App\Produto;
use Carbon\Carbon;

class MoipController extends Controller
{
    private $moip;
    private $token;
    private $key;
    private $accessToken;
    private $endpoint;

    public function __construct()
    {
        $this->token = config('app.moip_token');
        $this->key = config('app.moip_key');
        $this->accessToken = config('app.moip_access_token');       
        $this->endpoint = config('app.moip_endpoint');

        $endpoint = Moip::ENDPOINT_PRODUCTION;

    	$this->moip = new Moip(new OAuth($this->accessToken), $endpoint);
    }

    public function createApp()
    {
        $guzzle = new Client();

        $body = json_encode([
            'name' => 'Plataforma Upstore',
            'description' => 'APP de integração com a plataforma Upstore',
            'site' => 'https://upstoreexpress.com.br',
            'redirectUri' => 'https://upstoreexpress.com.br/moip/callback'
        ]);

        $res = $guzzle->request('POST', 'https://api.moip.com.br/v2/channels', [
            'headers' => [
                'Authorization' => 'Basic '.base64_encode($this->token.':'.$this->key),
                'Content-Type' => 'application/json'
            ],
            'body' => $body
        ]);            


        $response = json_decode($res->getBody());

        dd($response);
    }

    public function getAccountId()
    {
        $guzzle = new Client;

        $res = $guzzle->request('GET', 'https://api.moip.com.br/v2/accounts', [
            'headers' => [
                'Authorization' => 'OAuth '.$this->accessToken,
            ]
        ]);

        $response = json_decode($res->getBody());
        dd($response);
        return $response;
    }

    public function getCustomer($customer_id)
    {
        $customer = $this->moip->customers()->get($customer_id);

        return $customer;
    }

    public function createCustomer($usuario)
    {
        $cpf =str_replace(".","",$usuario->documento);
        $cpf =str_replace("-","",$cpf);
        $tels = explode(")",$usuario->telefone);

        $dd = $tels[0];
        $dd = str_replace("(","",$dd);
        $dd = str_replace(")","",$dd);
        $num = $tels[1];
        $num = str_replace(" ","",$num);
        $num = str_replace(".","",$num);
        $num = str_replace("-","",$num);

        $data_nascimento = Carbon::createFromFormat('d/m/Y',$usuario->data_nascimento)->format('Y-m-d');
        //date('Y-m-d',strtotime(date('d/m/Y',strtotime($usuario->data_nascimento))));
        //dd($usuario->enderecoAtual);
        $customer = $this->moip->customers()->setOwnId(uniqid())
            ->setFullname($usuario->nome)
            ->setEmail($usuario->email)
            ->setTaxDocument($cpf)
            ->setPhone($dd, $num)
            ->setBirthDate($data_nascimento)
            ->addAddress('BILLING',
                $usuario->enderecoAtual->rua, (int)$usuario->enderecoAtual->numero,
                $usuario->enderecoAtual->bairro, $usuario->enderecoAtual->cidade, $usuario->enderecoAtual->estado,
                $usuario->enderecoAtual->cep, null)
            ->addAddress('SHIPPING',
                $usuario->enderecoAtual->rua, (int)$usuario->enderecoAtual->numero,
                $usuario->enderecoAtual->bairro, $usuario->enderecoAtual->cidade, $usuario->enderecoAtual->estado,
                $usuario->enderecoAtual->cep, null)
            ->create();

        return $customer->getId();
    }

    public function getOrder($order_id)
    {
        $order = $this->moip->orders()->get($order_id);
        return $order;
    }

    public function createOrder($customer,$order, $discount = 0)
    {
        //$carrinho = $order->carrinho;
        $orderMoip = $this->moip->orders()->setOwnId(uniqid());

        foreach($order->vendas as $v){
            $descricao = $v->produto->descricao;
            $descricao = strlen($descricao) > 50 ? substr($descricao,0,200)."..." : $descricao;

            $orderMoip->addItem($v->produto->nome, $v->quantidade, $descricao, $v->valor_unitario);
        }
        $orderMoip->setShippingAmount($order->frete_valor)->setAddition(0)->setDiscount($discount)
                    ->setCustomer($customer)
                    ->create();
        return $orderMoip;
    }

    public function createOrderServico($customer,$order)
    {
        //$carrinho = $order->carrinho;
        $orderMoip = $this->moip->orders()->setOwnId(uniqid());

        $orderMoip->addItem('Serviço de manutenção', 1,'Serviço de manutenção prestado pela Upstore', $order->valor_total);
        $orderMoip->setShippingAmount($order->frete_valor)->setAddition(0)->setDiscount(0)
                    ->setCustomer($customer)
                    ->create();
        return $orderMoip;
    }

    public function createPaymentCreditCard($orderMoip,$cliente, $parcelamento = 1, $order)
    {
        try{
            $cartao = Cartao::findOrFail($order->cartao_id);

            \Log::alert("Cartão #" . $cartao->id . " encontrado");

            $cpf =str_replace(".","",$cartao->holder_cpf);
            $cpf =str_replace(".","",$cpf);
            $cpf =str_replace("-","",$cpf);
            $cpf =str_replace("/","",$cpf);

            \Log::alert($cpf);

            $tels = explode(")",$cartao->holder_telefone);

            $tels = preg_replace('/\D/', '', $cartao->holder_telefone);

            $dd = mb_substr($tels, 0, 2);
            $num = substr($tels, 2);

            \Log::alert($dd);
            \Log::alert($num);

            $holder = $this->moip->holders()->setFullname($cartao->holder_nome)
                        ->setBirthDate(date('Y-m-d',strtotime($cartao->holder_data_nascimento)))
                        ->setTaxDocument($cpf, 'CPF')
                        ->setPhone(intval($dd), intval($num), 55);

            /*$payment = $orderMoip->payments()
                ->setCreditCard($mes, $ano,$numero_cartao, $cvc, $holder)
                ->execute();*/
            //dd($orderMoip);

            $payment = $orderMoip->payments()
                ->setCreditCardHash($cartao->hash, $holder)
                ->setInstallmentCount($parcelamento)
                ->setStatementDescriptor('Upstore')
                ->setDelayCapture()
                ->execute();


            return $payment;
        }catch(\Exception $e){
            \Log::alert($e);
            $telemetry = new Telemetria;
            $telemetry->user_id = 0;
            $telemetry->metodo = 'Admin/CronController@createPaymentCreditCard';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();
        }
    }

    public function createPaymentBoleto($order){
        try{
            $logo_uri = 'https://cdn.moip.com.br/wp-content/uploads/2016/05/02163352/logo-moip.png';
            $expiration_date = date('Y-m-d',strtotime('+2 days'));
            $instruction_lines = ['INSTRUÇÃO 1', 'INSTRUÇÃO 2', 'INSTRUÇÃO 3'];
            $payment = $order->payments()  
                ->setBoleto($expiration_date, $logo_uri, $instruction_lines)
                ->execute();
            
            return $payment;
        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = 0;
            $telemetry->metodo = 'Admin/CronController@createPaymentBoleto';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();
        }
    }

    public function getPayment($payment_id)
    {
        $payment = $this->moip->payments()->get($payment_id);
        return $payment;
    }

    public function capturePayment($payment)
    {
        try{
            $captured_payment = $payment->capture();
            return $captured_payment;
        }catch(\Excpetion $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = 0;
            $telemetry->metodo = 'Admin/CronController@createEnvio';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();
        }
    }

    public function cancelPayment($payment)
    {
        try{
            $canceled_payment = $payment->cancel();
            return $canceled_payment->status;
        }catch(\Excpetion $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = 0;
            $telemetry->metodo = 'Admin/CronController@createEnvio';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();
        }
    }

    public function refundPayment($payment){
        try{
            $refund = $payment->refunds()->creditCardFull();
            return $refund;
        }catch(\Excpetion $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = 0;
            $telemetry->metodo = 'Admin/CronController@createEnvio';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();
        }
    }

    public function createCreditCard($customerId,$cartao)
    {
        $tels = explode(")",$cartao['holder_telefone']);

        $dd = $tels[0];
        $dd = str_replace("(","",$dd);
        $dd = str_replace(")","",$dd);
        $num = $tels[1];
        $num = str_replace(" ","",$num);
        $num = str_replace(".","",$num);
        $num = str_replace("-","",$num);

        $response = $this->moip->customers()->creditCard()
                    ->setExpirationMonth($cartao['mes_expiracao'])
                    ->setExpirationYear($cartao['ano_expiracao'])
                    ->setNumber($cartao['numero'])
                    ->setCVC($cartao['ccv'])
                    ->setFullName($cartao['holder_name'])
                    ->setBirthDate($cartao['holder_data_nascimento'])
                    ->setTaxDocument('CPF', $cartao['holder_cpf'])
                    ->setPhone('55',$dd,$num)
                    ->create($customerId);

        return $response;
    }

   public function createProvider($user)
    {  
        /*$cnpj =str_replace(".","",$user->documento);
        $cnpj =str_replace("-","",$cnpj);*/

        $cpf =str_replace(".","",$user->cpf);
        $cpf =str_replace("-","",$cpf);

        $tels = explode(" ",$user->telefone);

        $dd = $tels[0];
        $dd = str_replace("(","",$dd);
        $dd = str_replace(")","",$dd);
        $num = $tels[1];
        $num = str_replace(".","",$num);
        $num = str_replace("-","",$num);
        

        $account = $this->moip->accounts()
                ->setName($user->nome)
                ->setLastName($user->sobrenome)
                ->setEmail($user->email)
                ->setBirthDate(date('Y-m-d',strtotime($user->data_nascimento)))
                ->setTaxDocument($cpf)
                ->setType('MERCHANT')
                ->setPhone(intval($dd), intval($num), 55)
                ->addAddress($user->rua, $user->numero, $user->bairro, $user->cidade, $user->estado, $user->cep, null, 'BRA')        
                ->create();
                //->setCompanyName($user->nome_fantasia, $user->razao_social)
                //->setCompanyOpeningDate(date('Y-m-d',strtotime($user->data_abertura)))
                //->setCompanyPhone(intval($dd), intval($num), 55)
                //->setCompanyTaxDocument($cnpj)
                //->setCompanyAddress($user->rua, $user->numero, $user->bairro, $user->cidade, $user->estado, $user->cep, null, 'BRA')
                //->setCompanyMainActivity($user->cnae, $user->cnae_descricao)

        return $account->getId();
    }
}