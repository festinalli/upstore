<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Endereco extends Model
{
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

    protected $table = 'enderecos';

    public function isPrincipal() {
        return $this->status == 'ATIVO' ? true : false;
    }
}
