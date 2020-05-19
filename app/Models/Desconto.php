<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Desconto extends Model {

    protected $fillable = ['produto_id','desconto','status'];

    protected $dates = [];

    protected $table = 'descontos';
    public static $rules = [
        // Validation rules
    ];

    // Relationships

    public function produtos(){
        return $this->belongsTo('App\Models\Produto','produto_id');
    }

}
