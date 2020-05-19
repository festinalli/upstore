<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LogEnvio extends Model
{
    protected $fillable = ['data','descricao','envio_id'];

    protected $table = 'log_envios';

    // Relationships
    public function envio(){
        return $this->belongsTo('App\Envio','envio_id');
    }
}
