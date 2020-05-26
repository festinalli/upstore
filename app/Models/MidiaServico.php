<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MidiaServico extends Model {

    protected $fillable = [
        'servico_id','foto','video','status'
    ];

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];

    protected $table = 'midia_servicos';

    // Relationships
    public function servico(){
        return $this->belongsTo('App\Servico','servico_id');
    }

}
