<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notificacao extends Model
{
    protected $fillable = ['user_id','titulo','descricao','tipo','link','lido','icones'];

    protected $table = 'notificacoes';

    public function tecnico(){
        return $this->belongsTo('App\User','user_id');
    }

    public function usuario(){
        return $this->belongsTo('App\User','user_id');
    }
}
