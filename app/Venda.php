<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Venda extends Model
{
    protected $fillable = [
        'user_id',
        'produto_id',
        'order_id',
        'loja_id',
        'valor_unitario',
        'quantidade',
        'status',
    ];

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];

    protected $table = 'vendas';
    // Relationships
    public function order(){
        return $this->belongsTo('App\Order','order_id');
    }

    public function loja(){
        return $this->belongsTo('App\Loja','loja_id');
    }

    public function produto(){
        return $this->belongsTo('App\Produto','produto_id');
    }
}
