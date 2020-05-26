<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AparelhoAcessorio extends Model {

    protected $fillable = ['acessorio_id','aparelho_id'];

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];

    protected $table = 'aparelhos_acessorios';
    // Relationships
    public function aparelho(){
        return $this->belongsTo('App\Models\Aparelho','aparelho_id');
    }

    public function acessorio(){
        return $this->belongsTo('App\Models\Acessorio','acessorio_id');
    }

}
