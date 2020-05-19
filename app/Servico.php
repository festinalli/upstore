<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Servico extends Model
{
    protected $fillable = ['aparelho_id','user_id','loja_id','metodo','tipo','status'];

    protected $table = 'servicos';

    protected $dates = ['deleted_at'];
    
    // Relationships
    public function aparelho(){
        return $this->belongsTo('App\Aparelho','aparelho_id');
    }

    public function midia(){
        return $this->hasMany('App\MidiaServico','servico_id');
    }

    public function envio(){
        return $this->hasOne('App\Envio','servico_id');
    }

    public function cliente(){
        return $this->belongsTo('App\User','user_id');
    }

    public function loja(){
        return $this->belongsTo('App\Loja','loja_id');
    }
    public function midiaFotoMercadoria()
    {
        return $this->hasMany('App\MidiaServico','servico_id')->where('status','MARCADORIA')->where('video',null);
    }

    public function midiaVideoMercadoria()
    {
        return $this->hasMany('App\MidiaServico','servico_id')->where('status','MARCADORIA')->where('foto',null);
    }

    public function preOrcamento(){
        $soma = 0;
        $valorBase = $this->valor;
        foreach($this->aparelho->acessorios as $a){
            $soma += $a->acessorio->valor;
        }
        $soma = $valorBase + $soma;
        foreach($this->aparelho->problemas as $p){
            $soma -= $p->problema->valor;
        }

        return $soma;
    }

    public function observacoes(){
        return $this->hasMany('App\Observacao','servico_id');
    }

    public function banco(){
        return $this->hasOne('App\Banco','servico_id');
    }

    public function order(){
        return $this->belongsTo('App\Order','order_id');
    }

    public function orderServico(){
        return $this->hasOne('App\OrderServico','servico_id');
    }

    public function codigo(){
        return $this->hasOne('App\Codigo','servico_id');
    }

    public function tecnico(){
        return $this->belongsTo('App\User','tecnico_id');
    }
    

    public function isManutencao() {
        return $this->tipo == 'M' ? true : false;
    }
}
