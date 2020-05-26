<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ObservacaoManutencao extends Model
{
    protected $fillable = ['observacao','servico_id'];

    protected $table = 'observacao_manutencoes';

    public function servico(){
        return $this->belongsTo('App\Servico','servico_id');
    }
}
