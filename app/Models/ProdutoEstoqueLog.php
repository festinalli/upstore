<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProdutoEstoqueLog extends Model {

    protected $fillable = ['produto_id','order_id','carrinho_id', 'loja_id','quantidade','tipo','quantidade_anterior'];

    protected $dates = [];

    protected $table = 'produto_estoque_logs';
    
    public static $rules = [
        // Validation rules
    ];

    // Relationships

    public function produto(){
        return $this->belongsTo('App\Models\Produto','produto_id');
    }

    public function order(){
        return $this->belongsTo('App\Models\Order','order_id');
    }

    public function loja(){
        return $this->belongsTo('App\Models\Loja','loja_id');
    }

}
