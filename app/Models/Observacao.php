<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Observacao extends Model {
    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];

    protected $table = 'observacoes';

    // Relationships

    public function servico(){
        return $this->belongsTo('App\Models\Servico','servico_id');
    }


}
