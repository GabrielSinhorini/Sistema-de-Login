<?php
require('config/conexao.php');

//VERIFICAR SE TEM AUTORIZAÇÃO
$user = auth($_SESSION['TOKEN']);
if ($user){
    echo "<h1> SEJA BEM-VINDO <B style='color:red'>".$user['nome']."</b></h1>";
    echo "<br><br> <a style='background:green;text-decoration: none; color:white; padding:20px; border-radius:5px' href='logout.php'>Sair do Sistema</a>";
}else{
    header('location: index.php');
}
