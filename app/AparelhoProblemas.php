<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AparelhoProblemas extends Model
{
    protected $table="aparelhos_problemas";

    public function aparelho()
    {
        return $this->belongsTo('App\Aparelho','aparelho_id');
    }

    public function problema()
    {
        return $this->belongsTo('App\Problema','problema_id');
    }
}
