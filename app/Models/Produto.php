<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produto extends Model
{
    protected $fillable = [
        'nome', 'descricao', 'valor', 'voltagem', 'quantidade', 'status','marca_id','modelo_id','capacidade_id'
    ];

    protected $table = 'produtos';

    public function categorias()
    {
        return $this->hasMany('App\Models\ProdutoCategoria','produto_id');
    }

    public function fotos()
    {
        return $this->hasMany('App\Models\Foto','produto_id');
    }

    public function views()
    {
        return $this->hasMany('App\Models\View','produto_id')->inRandomOrder()->limit(9);
    }

    public function estoques()
    {
        return $this->hasMany('App\Models\EstoqueLoja','produto_id');
    }

    public function hasEstoque(){
        return $this->hasMany('App\Models\EstoqueLoja','produto_id')->where('quantidade','>',0)->get()->count();
    }

    public function descontos(){
        return $this->hasMany('App\Models\Desconto','produto_id');
    }

    public function produtoEstoqueLog(){
        return $this->hasMany('App\Models\ProdutoEstoqueLog','produto_id');
    }
}
