<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstoqueLoja extends Model {

    protected $fillable = [
        //'user_id',
        'produto_id',
        'loja_id',
        'quantidade'
    ];

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];


    protected $table = 'estoque_loja';
    // Relationships

    public function produto()
    {
        return $this->belongsTo('App\Models\Produto','produto_id');
    }

    public function loja()
    {
        return $this->belongsTo('App\Models\Loja','loja_id');
    }

}
