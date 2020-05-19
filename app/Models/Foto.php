<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Foto extends Model {

    protected $fillable = [
        'diretorio'
    ];

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];

    protected $table='fotos';

    // Relationships

    public function produto()
    {
        return $this->belongsTo('App\Models\Produto','produto_id');
    }

}
