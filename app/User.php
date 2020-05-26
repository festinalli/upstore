<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nome', 
        'email',
        'sobrenome',
        'customer_id',
        'telefone',
        'documento',
        'sexo',
        'status',
        'tipo',
        'data_nascimento',
        'rua',
        'numero',
        'bairro',
        'cidade',
        'estado',
        'cep',
        'password',
        'tipo',
        'status',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    protected $table = 'users';

   //Desabilita o uso do remember token
   public function getRememberToken(){
        return null;
    }

    //Desabilita o uso do remember token
    public function setRememberToken($value){}

    //Desabilita o uso do remember token
    public function getRememberTokenName(){
    return null;
    }

    //Desabilita o uso do remember token
    public function setAttribute($key, $value){
        $isRememberTokenAttribute = $key == $this->getRememberTokenName();
        if (!$isRememberTokenAttribute){
            parent::setAttribute($key, $value);
        }
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier() {
        return $this->getKey(); // Eloquent Model method
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims() {
        return [];
    }

    public function enderecos()
    {
        return $this->hasMany('App\Endereco','user_id')->where('status','!=','OCULTO');
    }

    public function enderecoAtual()
    {
        return $this->hasOne('App\Endereco','user_id')->where('status','ATIVO');
    }

    public function cartoes()
    {
        return $this->hasMany('App\Cartao','user_id')->where('status','!=','OCULTO');
    }

    public function cartaoAtual()
    {
        return $this->hasOne('App\Cartao','user_id')->where('status','=','ATIVO');
    }

    public function tokenPassword()
    {
        return $this->hasOne('App\Token','user_id')->where('tipo',2);
    }

    public function tokenEmail()
    {
        return $this->hasOne('App\Token','user_id')->where('tipo',1);
    }

    public function token()
    {
        return $this->hasOne('App\Token','user_id');
    }

    public function notificacoesLidas()
    {
        return $this->hasMany('App\Models\Notificacao')->where('notificacoes.lido',1);
    }

    public function notificacoesNaoLidas()
    {
        return $this->hasMany('App\Models\Notificacao')->where('notificacoes.lido',0);
    }

    public function codigos()
    {
        return $this->hasMany('App\Models\Codigo','user_id');
    }

    public function cuponsValidos()
    {
        return $this->hasMany('App\Models\Codigo','user_id')->where('status','ATIVO');
    }

    public function views()
    {
        return $this->hasMany('App\Models\View','user_id')->inRandomOrder();
    }


    public function telefones()
    {
        return $this->hasMany('App\Models\Telefone','user_id');
    }
}
