<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderServico extends Model
{
    protected $fillable = [];

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];

    protected $table = 'order_servicos';

    // Relationships

    public function servico(){
        return $this->belongsTo('App\Servico','servico_id');
    }
}
