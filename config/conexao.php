<?php
session_start();
// DOIS MODOS POSSÍVEIS -> LOCAL, PRODUÇÃO
$modo = 'local';

if($modo == 'local'){
    $servidor = "localhost";
    $usuario = "root";
    $senha = "";
    $banco = "login";
}

if($modo == 'producao'){
    $servidor = "";
    $usuario = "";
    $senha = "";
    $banco = "";
}

try{
    $pdo = new PDO("mysql:host=$servidor;dbname=$banco",$usuario,$senha);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Conectado com o banco!";
}catch(PDOException $erro){
    echo "Falha ao se conectar com o banco!";

}

function limparPost($dados){
    $dados = trim($dados);
    $dados = stripslashes($dados);
    $dados = htmlspecialchars($dados);
    return $dados;
}

function auth($token){
    //VERIFICAR SE TEM AUTORIZAÇÃO
    global $pdo;
    $sql = $pdo->prepare("SELECT * FROM clientes WHERE token=? LIMIT 1");
    $sql->execute(array($token));
    $usuario = $sql->fetch(PDO::FETCH_ASSOC);
    //SE NÃO ENCONTRAR O USUÁRIO
    if(!$usuario){
        return false;
    }else{
        return $usuario;
    }
    }

?>