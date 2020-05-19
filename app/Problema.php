<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Problema extends Model
{
    use SoftDeletes;
    protected $table="problemas";
    protected $dates = ['deleted_at'];

    public function aparelhos(){
        return $this->hasMany('App\AparelhoProblema','problema_id');
    }

    public function modelo(){
    	return $this->belongsTo('App\Modelo','modelo_id');
    }
}
