<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Endereco extends Model {

    protected $fillable = [
        'user_id',
        'cep',
        'numero',
        'rua',
        'bairro',
        'cidade',
        'estado',
        'complemento',
        'status'
    ];

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];

    protected $table = 'enderecos';

    // Relationships

}
