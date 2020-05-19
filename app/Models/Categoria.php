<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categoria extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nome', 'status',
    ];


    protected $table = 'categorias';

    public function produtos()
    {
        return $this->hasMany('App\Models\ProdutoCategoria','categoria_id');
    }
}
