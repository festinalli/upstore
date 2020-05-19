<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Acessorio extends Model
{
    use SoftDeletes;
    protected $fillable = ['nome','valor','status'];
    protected $dates = ['deleted_at'];
    protected $table = 'acessorios';

    public function aparelhos(){
        return $this->hasMany('App\AparelhoAcessorio','acessorio_id');
    }

    
}
