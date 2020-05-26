<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contato extends Model {

    protected $fillable = [
        'nome',
        'email',
        'mensagem'
    ];

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];

    protected $table = 'contatos';

    // Relationships

}
