<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loja extends Model {

    protected $fillable = [
        'titulo',
        'cep',
        'endereco',
        'numero',
        'cnpj',
        'cidade',
        'bairro',
        'estado'
    ];

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];

    protected $table = 'lojas';

    // Relationships

    public function estoques()
    {
        return $this->hasMany('App\Models\EstoqueLoja','loja_id');
    }

    public function horarios()
    {
        return $this->hasMany('App\Models\Horarios','loja_id');
    }

    public function produtoEstoqueLog(){
        return $this->hasMany('App\Models\ProdutoEstoqueLog','loja_id');
    }

}
