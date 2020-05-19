<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AparelhoProblema extends Model {

    protected $fillable = ['problema_id','aparelho_id'];

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];

    protected $table = 'aparelhos_problemas';
    // Relationships
    public function aparelho(){
        return $this->belongsTo('App\Models\Aparelho','aparelho_id');
    }

    public function problema(){
        return $this->belongsTo('App\Models\Problema','problema_id');
    }

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
    
}
