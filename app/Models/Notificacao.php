<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notificacao extends Model {

    protected $fillable = [
        'titulo',
        'descricao',
        'link',
        'lido',
        'user_id',
        'icone'
    ];

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];

    protected $table = 'notificacoes';

    // Relationships

    public function user()
    {
        return $this->belongsTo('App\User','user_id');
    }

}
