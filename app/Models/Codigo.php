<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Codigo extends Model {

    protected $fillable = [
        'codigo',
        'user_id',
        'valor',
        'porcentagem'
    ];

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];

    protected $table="codigos";

    public function user()
    {
        return $this->belongsTo('App\User','user_id');
    }

    // Relationships

}
