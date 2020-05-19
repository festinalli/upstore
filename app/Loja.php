<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Loja extends Model
{
    use SoftDeletes;
    protected $table="lojas";
    protected $dates = ['deleted_at'];

    public function estoques(){
        return $this->hasMany('App\EstoqueLoja','loja_id');
    }
    
}
