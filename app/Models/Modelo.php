<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modelo extends Model {

    protected $fillable = [
        'nome',
    ];

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];

    protected $table = 'modelos';

    public function marca()
    {
        return $this->belongsTo('App\Models\Marca','marca_id');
    }

    public function capacidadesAtivas()
    {
        return $this->hasMany('App\Models\Capacidade','modelo_id')->where('status','ATIVO');
    }

}
