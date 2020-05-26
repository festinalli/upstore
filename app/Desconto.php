<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Desconto extends Model
{
    protected $fillable = ['produto_id','desconto','status'];

    protected $table = 'descontos';

    public function produtos(){
        return $this->belongsTo('App\Produto','produto_id');
    }
}
