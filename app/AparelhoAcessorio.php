<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AparelhoAcessorio extends Model
{
    protected $fillable = ['acessorio_id','aparelho_id'];

    protected $table = 'aparelhos_acessorios';
    // Relationships
    public function aparelho(){
        return $this->belongsTo('App\Aparelho','aparelho_id');
    }

    public function acessorio(){
        return $this->belongsTo('App\Acessorio','acessorio_id');
    }
}
