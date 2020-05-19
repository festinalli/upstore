<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProdutoCategoria extends Model
{
    protected $table="produtos_categorias";

    public function categoria()
    {
        return $this->belongsTo('App\Categoria','categoria_id');
    }

    public function produto()
    {
        return $this->belongsTo('App\Produto','produto_id');
    }
}
