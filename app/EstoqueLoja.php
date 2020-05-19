<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EstoqueLoja extends Model
{
    protected $fillable = [
        'produto_id',
        'loja_id',
        'quantidade'
    ];

    protected $table = 'estoque_loja';

    public function getVoltagemName() {
        switch ($this->tipo) {
            case 'q':
                return '-';
            
            case '1':
                return '110V';
            
            case '2':
                return '220V';


            default:
                return '-';
        }
    }

    public function produto()
    {
        return $this->belongsTo('App\Produto','produto_id');
    }

    public function loja()
    {
        return $this->belongsTo('App\Loja','loja_id');
    }

}
