<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model {

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
        return $this->hasMany('App\Models\Venda','order_id');
    }

    public function cartao(){
        return $this->belongsTo('App\Models\Cartao','cartao_id');
    }

    public function endereco(){
        return $this->belongsTo('App\Models\Endereco','endereco_id');
    }

    public function envio(){
        return $this->hasOne('App\Models\Envio','order_id');
    }

    public function produtoEstoqueLog(){
        return $this->hasMany('App\Models\ProdutoEstoqueLog','order_id');
    }
}
