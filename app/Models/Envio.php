<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Envio extends Model {

    protected $fillable = [
        'servico_id',
        'etiqueta_id',
        'status',
        'codigo_rastreio',
        'order_id'
    ];

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];

    protected $table = 'envios';
    // Relationships

    public function servico(){
        return $this->belongsTo('App\Models\Servico','servico_id');
    }

    public function order(){
        return $this->belongsTo('App\Models\Order','order_id');
    }

    public function logsEnvio(){
        return $this->hasMany('App\Models\LogEnvio','envio_id');
    }
}
