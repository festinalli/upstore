<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MidiaServico extends Model
{
    protected $table="midia_servicos";

    public function servico()
    {
        return $this->belongsTo('App\Servico','servico_id');
    }
}
