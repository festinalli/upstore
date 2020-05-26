<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Aparelho extends Model
{
    protected $fillable = ['capacidade_id','senha'];

    protected $table = 'aparelhos';

    // Relationships
    public function capacidade(){
        return $this->belongsTo('App\Capacidade','capacidade_id');
    }

    public function marca(){
        return $this->belongsTo('App\Marca','marca_id');
    }

    public function modelo(){
        return $this->belongsTo('App\Modelo','modelo_id');
    }

    public function servico(){
        return $this->hasOne('App\Servico','aparelho_id');
    }

    public function problemas(){
        return $this->hasMany('App\AparelhoProblema','aparelho_id');
    }

    public function acessorios(){
        return $this->hasMany('App\AparelhoAcessorio','aparelho_id');
    }
}
