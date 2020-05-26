<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aparelho extends Model {

    protected $fillable = ['capacidade_id','senha'];

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];
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
        return $this->hasOne('App\Models\Servico','aparelho_id');
    }

    public function problemas(){
        return $this->hasMany('App\Models\AparelhoProblema','aparelho_id');
    }

    public function acessorios(){
        return $this->hasMany('App\Models\AparelhoAcessorio','aparelho_id');
    }
}
