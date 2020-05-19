<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AparelhoProblema extends Model
{
    protected $fillable = ['problema_id','aparelho_id','marca_id','modelo_id','capacidade_id'];

    protected $table = 'aparelhos_problemas';
    // Relationships
    public function aparelho(){
        return $this->belongsTo('App\Aparelho','aparelho_id');
    }

    public function problema(){
        return $this->belongsTo('App\Problema','problema_id');
    }

    public function marca(){
    	return $this->belongsTo('App\Marca','marca_id');
    }

    public function modelo(){
    	return $this->belongsTo('App\Modelo','modelo_id');
    }

    public function capacidade(){
    	return $this->belongsTo('App\Capacidade','capacidade_id');
    }
}
