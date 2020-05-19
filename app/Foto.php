<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Foto extends Model
{
    protected $table="fotos";

    public function produto()
    {
        return $this->belongsTo('App\Produto','produto_id');
    }
}
