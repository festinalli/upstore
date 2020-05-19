<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    protected $table="produtos";

    public function fotos()
    {
        return $this->hasMany('App\Foto','produto_id');
    }

    public function fotoPrincipal()
    {
        return $this->hasOne('App\Foto','produto_id')->where('principal',1);
    }

    public function categorias()
    {
        return $this->hasMany('App\ProdutoCategoria','produto_id');
    }

    public function descontos(){
        return $this->hasMany('App\Desconto','produto_id');
    }

    public function promocao(){
        return $this->hasOne('App\Desconto','produto_id');
    }

    public function descontoAtivo(){
        return $this->hasMany('App\Desconto','produto_id')->where('status','ATIVO');
    }

    public function marca(){
        return $this->belongsTo('App\Marca','marca_id');
    }

    public function hasEstoque(){
        return $this->hasMany('App\EstoqueLoja','produto_id')->where('quantidade','>',0)->get()->count();
    }

    public function hasCategoria($idCategoria){
        foreach($this->categorias as $c){
            if($c->categoria->id == $idCategoria) return true;
        }

        return false;
    }

    public function estoques(){
        return $this->hasMany('App\EstoqueLoja','produto_id');
    }
}
