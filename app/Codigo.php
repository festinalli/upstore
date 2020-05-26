<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Codigo extends Model
{
    protected $fillable = [
        'codigo',
        'user_id',
        'status',
        'valido_ate',
        'valor',
        'porcentagem',
        'servico_id'
    ];

    protected $table="codigos";

    public function user()
    {
        return $this->belongsTo('App\User','user_id');
    }

    public function servico(){
        return $this->belongsTo('App\Servico','servico_id');
    }
}
