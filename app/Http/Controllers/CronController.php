<?php

namespace App\Http\Controllers;

use App\Notificacao;
use Illuminate\Http\Request;
use App\Http\Controllers\CorreiosController;
use App\Http\Controllers\MoipController;
use App\Http\Controllers\MailController;
use App\Notifications\BoletoMail;

use App\Servico;
use App\Envio;
use App\Telemetria;
use App\Order;
use App\User;
use App\ProdutoEstoqueLog;
use App\EstoqueLoja;
use App\OrderServico;
use App\AparelhoProblema;
use App\Aparelho;

class CronController extends Controller
{
    public function createEnvio()
    {
        try{
            $servicos = Servico::where('status','CRIADO')->where('metodo','CORREIO')->get();
            foreach($servicos as $s){
                $correio = new CorreiosController();
                $etiqueta = $correio->solicitarEtiquetas(1,'40096');
                $envio = new Envio;
                $envio->servico_id = $s->id;
                $envio->etiqueta_id = $etiqueta->getResult()[0]->getEtiquetaSemDv();
                $envio->save();

                $s->status = 'ENVIO_GERADO';
                $s->save();
            }
        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = 0;
            $telemetry->metodo = 'Admin/CronController@createEnvio';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();
        }
    }

    public function geraPagamento()
    {
        try{
            $orders = Order::whereIn('status',['ANALISE', 'REFUND_TROCA_ACEITA'])->get();

            foreach($orders as $o){
                try {
                    \Log::alert("Iniciando pagamento para a order" . $o->id);
                    $moip = new MoipController;
                    $usuario = User::findOrFail($o->user_id);
                    if($usuario->customer_id == null){
                        $customer_id = $moip->createCustomer($usuario);
                        $usuario->customer_id = $customer_id;
                        $usuario->update();
                    }

                    try {
                        $customer = $moip->getCustomer($usuario->customer_id);

                    } catch(\Exception $e) {
                        $usuario->customer_id = null;
                        $usuario->update();
                        continue;
                    }

                    $discount = 0;
                    if($o->status == 'REFUND_TROCA_ACEITA') {
                        $discount = $o->servico->valor;
                    }
                    $orderCreated = $moip->createOrder($customer,$o, $discount);
                    \Log::alert("MOIPOrder criada para a order #" . $o->id);
                   
                    if($o->forma_pagamento == 'CARTAO'){
                        try {
                            \Log::alert("Tentativa de criação de PaymentCreditCard #" . $o->id);

                            $paymentCreated = $moip->createPaymentCreditCard($orderCreated,$usuario,$o->parcelamento, $o);
                            if(!$paymentCreated) {
                                \Log::alert("Cartão não criado porque senhor #" . $o->id);

                                $o->status = 'CARTÃO INVÁLIDO';
                                $o->update();
                                continue;
                            }

                        } catch(\Exception $e) {
                            \Log::alert("erro ao gerar pagamento pelo Cartão - talvez cartão inválido");
                            throw($e);
                        }
                    }
                    else{
                        $paymentCreated = $moip->createPaymentBoleto($orderCreated);
                        $o->link_pagamento = $paymentCreated->getLinks()->getLink('payBoleto');
                    }

                    $o->gateway_order_id = $orderCreated->getId();
                    $o->gateway_payment_id = $paymentCreated->getId();

                    if($o->status == 'REFUND_TROCA_ACEITA') {
                        $o->status = 'AGUARDANDO_PAGAMENTO_TROCA';
                    } else {
                        $o->status = 'AGUARDANDO_PAGAMENTO';
                    }
                    $o->update();

                    \Log::alert("Order #" . $o->id . " analisada com sucesso."); 

                } catch(\Exception $e) {
                    \Log::alert("Erro ao gerar Pagmento para a Order #" . $o->id);
                    \Log::alert($e);
                }
            }
        }catch(\Exception $e){
            \Log::alert("Erro crítico order " . $o->id);
            \Log::alert($e);
            $telemetry = new Telemetria;
            $telemetry->user_id = 0;
            $telemetry->metodo = 'Admin/CronController@createEnvio';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();
        }
    }

    public function listarPagamentos(){
        try{
            $orders = Order::where('status','AGUARDANDO_PAGAMENTO')->get();
            foreach($orders as $o){
                $moip = new MoipController;
                $order = $moip->getPayment($o->gateway_payment_id);
            }
        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = 0;
            $telemetry->metodo = 'Admin/CronController@listarPagamentos';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();
        }
    }

    public function verificaPagamento()
    {
        try{
            $orders = Order::where('status','AGUARDANDO_PAGAMENTO')->where('forma_pagamento','CARTAO')->where('servico_id', null)->get();
            foreach($orders as $o){
                $moip = new MoipController;
                $payment = $moip->getPayment($o->gateway_payment_id);
                $caputuredPayment = $moip->capturePayment($payment);
                $o->status = 'CAPTURADO';
                $o->update();
            }
        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = 0;
            $telemetry->metodo = 'Admin/CronController@verificaPagamento';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();
        }
    }

    public function verificaPagamentoTroca()
    {
        try{
            $orders = Order::where('status','AGUARDANDO_PAGAMENTO')->where('forma_pagamento','CARTAO')->where('servico_id', '!=', null)->get();
            foreach($orders as $o){
                if($o->servico && $o->servico->status == 'CLIENTE_RECUSOU') {
                    $moip = new MoipController;
                    $payment = $moip->getPayment($o->gateway_payment_id);
                    $caputuredPayment = $moip->capturePayment($payment);
                    $o->status = 'CAPTURADO';
                    $o->update();
                }
                else if($o->servico && $o->servico->status == 'CLIENTE_ACEITOU') {
                    $moip = new MoipController;
                    $payment = $moip->getPayment($o->gateway_payment_id);
                    $refundedPayment = $moip->refundPayment($payment);
                    $o->status = 'REFUND_TROCA_ACEITA';
                    $o->update();
                }
            }
        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = 0;
            $telemetry->metodo = 'Admin/CronController@verificaPagamentoTroca';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();
        }
    }

    public function verificaCapturaTroca() {

        try{
            $orders = Order::where('status','AGUARDANDO_PAGAMENTO_TROCA')->where('forma_pagamento','CARTAO')->where('servico_id', '!=', null)->get();
            foreach($orders as $o){
                $moip = new MoipController;
                $payment = $moip->getPayment($o->gateway_payment_id);
                $caputuredPayment = $moip->capturePayment($payment);
                $o->status = 'CAPTURADO';
                $o->update();
            }
        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = 0;
            $telemetry->metodo = 'Admin/CronController@verificaCapturaTroca';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();
        }
    }

    public function verificaStatusPagamento()
    {
        try{
            $orders = Order::whereIn('status',['CAPTURADO', 'AGUARDANDO_PAGAMENTO'])->get();
            foreach($orders as $o){
                try {
                    $moip = new MoipController;
                    $payment = $moip->getPayment($o->gateway_payment_id);
                    if($payment->getStatus() == 'AUTHORIZED'){
                        $o->status = 'PAGO';
                        $o->update();
                    } elseif($payment->getStatus() == 'PRE_AUTHORIZED') {
                        $moip->capturePayment($payment);
                        $o->status = "CAPTURADO";
                        $o->update();
                    } else {
                        \Log::alert("Order #" . $o->id . " não paga - status: " . $payment->getStatus());
                    }
                } catch(\Exception $e) {
                    \Log::alert("Erro ao verificarPagamento da order #" . $o->id);
                }
            }
        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = 0;
            $telemetry->metodo = 'Admin/CronController@verificaStatusPagamento';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();
        }
    }

    public function removeCartao()
    {
        try{
            $orders = Order::where('status','PAGO')->get();
            foreach($orders as $o){
                if($o->cartao->status == 'DELETA'){
                    $cartao = $o->cartao;
                    $cartao->delete();
                }
            }
        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = 0;
            $telemetry->metodo = 'Admin/CronController@removeCartao';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();
        }
    }

    public function enviaEmail(){
        \Log::alert("HM");
        $notificacoes = Notificacao::where('enviado',false)->get();
        foreach ($notificacoes as $notificacao){
            $email = new MailController();

            $data['link'] = url('/').':8000/'.$notificacao->link;
            $data['titulo'] = 'Notificação de serviço upstore';
            $data['descricao'] = 'Descrição do email';
            $data['name'] = $notificacao->usuario->nome;
            $data['email'] = $notificacao->usuario->email;

            $email->html_email($data);
            $notificacao->enviado = true;
            $notificacao->update();
        }
    }

    public function enviaBoletoEmail(){
        $orders = Order::where('forma_pagamento','BOLETO')->where('link_pagamento', '!=', null)->where('enviado', 0)->get();
        foreach($orders as $o){
            \Log::alert("Cron Email - Enviando email para " . $o->usuario->email . " pedido #" . $o->id);
            $email = new MailController();
            $data['link'] = $o->link_pagamento;
            $data['titulo'] = 'Boleto de compra upstore';
            $data['descricao'] = 'Descrição do email';
            $data['name'] = $o->usuario->nome;
            $data['email'] = $o->usuario->email;

            //dd($o->toArray());
            $email->html_email($data);

            $user = $o->usuario;

            $user->notify(new BoletoMail($o));

            $o->enviado = 1;
            $o->save();

            $link = "https://api.google.com";
        }
    }

    public function verifica24Horas($timestamp)
    {
        try{

            if(time() - $timestamp > 60*60*24){
                return true;
            }else{
                return false;
            }

        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = 0;
            $telemetry->metodo = 'Admin/CronController@verifica24Horas';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();

            return false;
        }
    }

    public function cancelaCarrinhosSemUserID24Horas()
    {
        try{

            //pega todos os carrinhos sem user_id
            $carrinhos = Order::where('user_id',0)->get();

            foreach($carrinhos as $carrinho){

                //verifica se o carrinho está parado tem mais de 24 horas
                if($this->verifica24Horas(strtotime($carrinho->updated_at)) == true){
                
                    //primeiro passo é voltar o estoque dos produtos (vendas)
                    foreach($carrinho->vendas as $venda){

                        //agora pega o estoque que foi retirado
                        $estoque = EstoqueLoja::where('loja_id',$venda->loja_id)
                            ->where('produto_id',$venda->produto_id)
                            ->where('tipo', $venda->estoque_tipo)
                            ->first();

                        if($estoque){
                            $quantidade_anterior = $estoque->quantidade;
                            $quantidade = $venda->quantidade;
                            $produto_id = $venda->produto_id;
                            $loja_id    = $venda->loja_id;

                            $estoque->quantidade = $estoque->quantidade + $venda->quantidade;
                            $estoque->update();
                            $venda->delete();

                            $p = ProdutoEstoqueLog::create([
                                'quantidade' => $quantidade,
                                'quantidade_anterior'=> $quantidade_anterior,
                                'tipo' => 'REMOVEU_CARRINHO_EXPIRADO',
                                'produto_id' => $produto_id,
                                'carrinho_id' => $carrinho->id,
                                'loja_id' => $loja_id
                            ]);
                        }
                    }

                    $carrinho->delete();
                }

            }

        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = 0;
            $telemetry->metodo = 'Admin/CronController@cancelaCarrinhos2Horas';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();
        }
    }

    public function geraPagamentoServicoManutencao()
    {
        try{
            $orders = OrderServico::where('status','CRIADO')->get();

            $moip = new MoipController;

            foreach ($orders as $o) {

                $usuario = User::findOrFail($o->user_id);

                if($usuario->customer_id == null){
                    $customer_id = $moip->createCustomer($usuario);
                    $usuario->customer_id = $customer_id;
                    $usuario->update();
                }

                $customer = $moip->getCustomer($usuario->customer_id);
                $orderCreated = $moip->createOrderServico($customer,$o);

                $o->gateway_order_id = $orderCreated->getId();
                $o->link_pagamento = $orderCreated->getLinks()->getCheckout('payCheckout');
                $o->status = $orderCreated->getStatus();
                $o->update();

            }

        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = 0;
            $telemetry->metodo = 'Admin/CronController@geraPagamentoServicoManutencao';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();
        }
    }

    public function geraPagamentoServicoManutencaoIndividual($servico_id) {
        try{
            $order = OrderServico::where('status','CRIADO')->where('servico_id', $servico_id)->first();

            $moip = new MoipController;

            $usuario = User::findOrFail($order->user_id);

            if($usuario->customer_id == null){
                $customer_id = $moip->createCustomer($usuario);
                $usuario->customer_id = $customer_id;
                $usuario->update();
            }

            $customer = $moip->getCustomer($usuario->customer_id);
            $orderCreated = $moip->createOrderServico($customer,$order);

            $order->gateway_order_id = $orderCreated->getId();
            $order->link_pagamento = $orderCreated->getLinks()->getCheckout('payCheckout');
            $order->status = $orderCreated->getStatus();
            $order->update();

        }catch(\Exception $e){
            \Log::alert($e);
            $telemetry = new Telemetria;
            $telemetry->user_id = 0;
            $telemetry->metodo = 'Admin/CronController@geraPagamentoServicoManutencao';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();
        }        
    }

    public function verificaPagamentoServicoManutencao()
    {
        try{
            $orders = OrderServico::whereIn('status',['CREATED', 'WAITING'])->get();

            $moip = new MoipController;

            foreach ($orders as $o) {
                $status = $moip->getOrder($o->gateway_order_id)->getStatus();

                // if($status == 'PAID') {
                //     $servico = Servico::findOrFail($o->servico_id);

                //     if($servico->status == "PROPOSTA_ENVIADA" || $servico->status == "CLIENTE_ACEITOU") {
                //         $servico->status = "EM_MANUTENCAO";
                //         $servico->save();
                //     }
                // }

                $o->status = $status;
                $o->update();

                if($o->status == 'PAID'){
                    $servico = $o->servico;
                    $servico->status = 'EM_MANUTENCAO';
                    $servico->update();
                }

            }

        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = 0;
            $telemetry->metodo = 'Admin/CronController@geraPagamentoServicoManutencao';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();
        }
    }

    public function updateAparelhos()
    {
        try{
            foreach(Aparelho::where('marca_id',0)->get() as $aparelho){
                $aparelho->marca_id = $aparelho->capacidade->modelo->marca->id;
                $aparelho->modelo_id = $aparelho->capacidade->modelo->id;
                $aparelho->update();
            }
    
            foreach(AparelhoProblema::where('marca_id',0)->get() as $aparelho){
                $aparelho->marca_id = $aparelho->problema->modelo->marca->id;
                $aparelho->modelo_id = $aparelho->problema->modelo->id;
                $aparelho->capacidade_id = $aparelho->aparelho->capacidade_id;
                $aparelho->update();
            }
        }catch(\Exception $e){
            $telemetry = new Telemetria;
            $telemetry->user_id = 0;
            $telemetry->metodo = 'Admin/CronController@geraPagamentoServicoManutencao';
            $telemetry->descricao = $e->getMessage();
            $telemetry->save();
        }
        
    }
}
