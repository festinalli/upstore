<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AparelhoAcessorios extends Model
{
    protected $table="aparelhos_acessorios";

    public function aparelho()
    {
        return $this->belongsTo('App\Aparelho','aparelho_id');
    }

    public function acessorio()
    {
        return $this->belongsTo('App\Acessorio','acessorio_id');
    }
}
