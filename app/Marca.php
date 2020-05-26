<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Marca extends Model
{
    use SoftDeletes;
    protected $table="marcas";
    protected $dates = ['deleted_at'];

    public function modelos()
    {
        return $this->hasMany('App\Modelo','marca_id');
    }

    public function produtos(){
        return $this->hasMany('App\Marca','marca_id');
    }
    
    public function modelosAtivos()
    {
        return $this->hasMany('App\Models\Modelo','marca_id')->where('status','ATIVO');
    }
}
