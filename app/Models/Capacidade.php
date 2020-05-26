<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Capacidade extends Model {

    protected $fillable = [];

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];

    protected $table = 'capacidades';

    public function modelo()
    {
        return $this->belongsTo('App\Models\Modelo','modelo_id');
    }

    public function aparelho(){
        return $this->hasMany('App\Models\Aparelho','capacidade_id');
    }
}
