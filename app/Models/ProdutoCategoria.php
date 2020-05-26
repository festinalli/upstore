<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProdutoCategoria extends Model {

    protected $fillable = [];

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];

    protected $table = 'produtos_categorias';
    // Relationships

    public function produto()
    {
        return $this->belongsTo('App\Models\Produto','produto_id');
    }

    public function categoria()
    {
        return $this->belongsTo('App\Models\Categoria','categoria_id');
    }

}
