<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Marca extends Model {

    protected $fillable = [
        'nome',
        'foto'
    ];

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];

    protected $table = 'marcas';

    public function modelosAtivos()
    {
        return $this->hasMany('App\Models\Modelo','marca_id')->where('status','ATIVO');
    }

}
