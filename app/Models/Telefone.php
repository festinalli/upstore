<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Telefone extends Model {

    protected $fillable = [
        'user_id',
        'numero'
    ];

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];

    protected $table = 'telefone';

    // Relationships

    public function user()
    {
        return $this->belongsTo('App\User','user_id');
    }
}
