<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Banco extends Model
{
    protected $fillable = [
        'banco','agencia','conta','tipo_conta','titular','documento_titular','foto_comprovante','servico_id',
    ];

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];

    protected $table = 'bancos';

    // Relationships

    public function servico(){
        return $this->belongsTo('App\Servico','servico_id');
    }
}
