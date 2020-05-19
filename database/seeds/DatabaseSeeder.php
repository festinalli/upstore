<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Capacidade;
use App\Marca;
use App\Modelo;
use App\Loja;
use App\Servico;
use App\Aparelho;
use App\Problema;
use App\AparelhoProblema;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //$this->call(UserSeeder::class);
        $this->call(MarcaSeeder::class);
        $this->call(ModeloSeeder::class);
        $this->call(CapacidadeSeeder::class);
        $this->call(ProblemasSeeder::class);
        $this->call(AcessoriosSeeder::class);
        $this->call(LojaSeeder::class);
        $this->call(ServicosSeeder::class);
        $this->call(TecnicoSeeder::class);
    }
}

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user = new User;
        $user->nome = 'Admin';
        $user->sobrenome = 'Admin';
        $user->email = 'admin@admin.com';
        $user->password = bcrypt('123456');
        $user->status = 'ATIVO';
        $user->tipo = 'ADMIN';
        $user->save();
    }
}

class MarcaSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        for($i=0 ; $i<5 ; $i++){
            $marca = new Marca;
            $marca->nome = str_random(9);
            $marca->foto = str_random(9);
            $marca->status = 'ATIVO';
            $marca->save();
        }
    }
}


class ProblemasSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        // foreach(Modelo::all() as $modelo){
        //     for($i=0 ; $i<5 ; $i++){
        //         $problema = new Problema;
        //         $problema->nome = str_random(9);
        //         $problema->valor = rand(1000,9999);
        //         $problema->status = 'ATIVO';
        //         $problema->tipo = 'M';
        //         $problema->modelo_id = $modelo->id;
        //         $problema->save();
        //     }
        // }

        foreach(Aparelho::all() as $aparelho){
            foreach(Problema::inRandomOrder()->limit(5)->get() as $problema){
                $ap = new AparelhoProblema;
                $ap->valido = 0;
                $ap->problema_id = $problema->id;
                $ap->aparelho_id = $aparelho->id;
                $ap->save();
            }
        }
    }
}


class AcessoriosSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        for($i=0 ; $i<5 ; $i++){
            
        }
    }
}

class ModeloSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        foreach(Marca::all() as $marca){
            for($i=0 ; $i<5 ; $i++){
                $modelo = new Modelo;
                $modelo->nome = str_random(9);
                $modelo->marca_id = $marca->id;
                $modelo->foto = str_random(9);
                $modelo->status = 'ATIVO';
                $modelo->save();
            }
        }

        
    }
}

class CapacidadeSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        foreach(Modelo::all() as $modelo){
            for($i=0 ; $i<5 ; $i++){
                $capacidade =  new Capacidade;
                $capacidade->modelo_id = $modelo->id;
                $capacidade->memoria = rand(10,200);
                $capacidade->valor = rand(10000,99999);
                $capacidade->status = 'ATIVO';
                $capacidade->save();
            }
        }
    }
}

class LojaSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        
        for($i=0 ; $i<5 ; $i++){
            $loja =  new Loja;
            $loja->titulo = str_random(9);
            $loja->cnpj = str_random(9);
            $loja->cep = str_random(9);
            $loja->endereco = str_random(9);
            $loja->cidade = str_random(9);
            $loja->bairro = str_random(9);
            $loja->numero = str_random(9);
            $loja->estado = str_random(9);
            $loja->status = 'ATIVO';
            $loja->save();
        }
    }
}

class TecnicoSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        
        for($i=0 ; $i<5 ; $i++){
            $user = new User;
            $user->nome = str_random(9);
            $user->sobrenome = str_random(9);
            $user->email = str_random(4).'@mail.com';
            $user->password = app('hash')->make('123456');
            $user->status = 'ATIVO';
            $user->tipo = 'TECNICO';
            $user->save();
        }
    }
}

class ServicosSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */

    public function createAparelho()
    {
        $aparelho = new Aparelho;
        $aparelho->capacidade_id = Capacidade::inRandomOrder()->first()->id;
        $aparelho->senha = str_random(9);
        $aparelho->loja_senha = str_random(9);
        $aparelho->loja_login = str_random(9);
        $aparelho->save();

        return $aparelho;
    }

    public function createServico($aparelho_id,$user_id,$tipo)
    {
       $servico = new Servico;
       $servico->aparelho_id = $aparelho_id;
       $servico->user_id = $user_id;
       $servico->loja_id = Loja::inRandomOrder()->first()->id;
       $servico->metodo = 'CORREIOS';
       $servico->tipo = $tipo;
       $servico->valor = rand(10,100);
       $servico->descricao = str_random(10);
       $servico->save();
    }

    public function run()
    {
        foreach (User::where('tipo','CLIENTE')->where('status','ATIVO')->get() as $cliente) {
            $aparelho = $this->createAparelho();
            $this->createServico($aparelho->id,$cliente->id,'M');
            $this->createServico($aparelho->id,$cliente->id,'V');
            $this->createServico($aparelho->id,$cliente->id,'T');
        }
    }
}
