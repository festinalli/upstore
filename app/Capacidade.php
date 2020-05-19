<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Capacidade extends Model
{
    use SoftDeletes;
    protected $table="capacidades";
    protected $dates = ['deleted_at'];

    public function modelo()
    {
        return $this->belongsTo('App\Modelo','modelo_id');
    }
}
