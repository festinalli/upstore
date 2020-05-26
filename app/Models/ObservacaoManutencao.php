<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ObservacaoManutencao extends Model {

    protected $fillable = ['observacao','servico_id'];

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];

    protected $table = 'observacao_manutencoes';

    // Relationships

    public function servico(){
        return $this->belongsTo('App\Models\Servico','servico_id');
    }


}
