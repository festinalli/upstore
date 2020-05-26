<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderServico extends Model {

    protected $fillable = [];

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];

    protected $table = 'order_servicos';

    // Relationships

    public function servico(){
        return $this->belongsTo('App\Models\Servico','servico_id');
    }

}
