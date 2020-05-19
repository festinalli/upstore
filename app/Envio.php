<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Envio extends Model
{
    protected $fillable = [
        'servico_id',
        'etiqueta_id',
        'status',
        'codigo_rastreio',
        'order_id'
    ];

    protected $table = 'envios';
    // Relationships

    public function servico(){
        return $this->belongsTo('App\Servico','servico_id');
    }

    public function usuario(){
        return $this->belongsTo('App\User');
    }

    public function order(){
        return $this->belongsTo('App\Order','order_id');
    }

    public function logsEnvio(){
        return $this->hasMany('App\LogEnvio','envio_id');
    }
}
