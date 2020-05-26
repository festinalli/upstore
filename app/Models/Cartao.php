<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cartao extends Model {

    protected $fillable = [
        'user_id',
        'hash',
        'ultimos4',
        'bandeira',
        'mes',
        'ano',
        'cvc'
    ];

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];

    protected $table="cartoes";

    // Relationships

    public function user()
    {
        return $this->belongsTo('App\User','user_id');
    }

}
