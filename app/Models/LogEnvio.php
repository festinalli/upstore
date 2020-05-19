<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogEnvio extends Model {

    protected $fillable = ['data','descricao','envio_id'];

    protected $dates = ['data'];

    public static $rules = [
        // Validation rules
    ];

    protected $table = 'log_envios';

    // Relationships
    public function envio(){
        return $this->belongsTo('App\Models\Envio','envio_id');
    }
}
