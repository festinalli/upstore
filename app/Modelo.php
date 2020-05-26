<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Modelo extends Model
{
    use SoftDeletes;
    protected $table="modelos";
    protected $dates = ['deleted_at'];

    public function capacidades()
    {
        return $this->hasMany('App\Capacidade','modelo_id')->where('status','ATIVO');
    }

    public function marca()
    {
        return $this->belongsTo('App\Marca','marca_id');
    }

    public function problemas(){
    	return $this->hasMany('App\Problema','modelo_id');
    }
    
    public function capacidadesAtivas()
    {
        return $this->hasMany('App\Models\Capacidade','modelo_id')->where('status','ATIVO');
    }
}
