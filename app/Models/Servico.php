<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servico extends Model {

    protected $fillable = ['aparelho_id','user_id','loja_id','metodo','tipo','status','os'];

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];

    protected $table = 'servicos';
    // Relationships
    public function aparelho(){
        return $this->belongsTo('App\Models\Aparelho','aparelho_id');
    }

    public function cliente(){
        return $this->belongsTo('App\User','user_id');
    }

    public function midia(){
        return $this->hasMany('App\MidiaServico','servico_id');
    }

    public function envio(){
        return $this->hasOne('App\Models\Envio','servico_id');
    }

    public function order(){
        return $this->hasOne('App\Models\Order','servico_id');
    }

    public function observacoes(){
        return $this->hasMany('App\Models\Observacao');
    }
}
