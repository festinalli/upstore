<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'gateway_order_ip',
        'gateway_payment_id',
        'forma_pagamento',
        'endereco_id',
        'cartao_id',
        'codigo_id',
        'status',
        'frete_valor',
        'frete_prazo',
        'valor_total',
        'desconto_valor',
        'link_pagamento',
        'parcelamento',
        'token'
    ];

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];

    protected $table = 'orders';
    // Relationships

    public function vendas(){
        return $this->hasMany('App\Venda','order_id');
    }

    public function codigo(){//Aparelho como entrada ou cupom de desconto
        return $this->belongsTo('App\Codigo','codigo_id');
    }

    public function cartao(){
        return $this->belongsTo('App\Cartao','cartao_id');
    }

    public function endereco(){
        return $this->belongsTo('App\Endereco','endereco_id');
    }

    public function envio(){
        return $this->hasOne('App\Envio','order_id');
    }

    public function servico(){
        return $this->hasOnde('App\Servico','order_id');
    }

    public function usuario(){
        return $this->belongsTo('App\User','user_id');
    }

    public function getLojaId() {
        foreach($this->vendas as $venda) {
            return $venda->loja_id;
        }

        return 0;
    }
}
