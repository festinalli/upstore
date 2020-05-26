<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Observacao extends Model
{
    protected $fillable = ['descricao','servico_id','status'];
    protected $table="observacoes";

    public function servico()
    {
        return $this->belongsTo('App\Servico','servico_id');
    }

}
