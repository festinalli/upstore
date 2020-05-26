<?php
/**
 * Created by PhpStorm.
 * User: Fonseca
 * Date: 25/10/2018
 * Time: 14:19
 */

function gravaNotificacao($user_id,$titulo,$descricao,$tipo,$link){
    $novanotificacao = new \App\Notificacao();
    $novanotificacao->user_id = $user_id;
    $novanotificacao->titulo = $titulo;
    $novanotificacao->descricao = $descricao;
    $novanotificacao->tipo = $tipo;
    $novanotificacao->link = $link;
    $novanotificacao->save();
}