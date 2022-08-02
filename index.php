<?php
require('config/conexao.php');

if(isset($_POST['email']) && isset($_POST['senha']) && !empty($_POST['email']) && !empty($_POST['senha'])){
    //RECEBER OS DADOS VINDO DO POST E LIMPAR
    $email = limparPost($_POST['email']);
    $senha = limparPost($_POST['senha']);
    $senha_cript = sha1($senha);

    //VERIFICAR SE EXISTE ESTE USUÁRIO
    $sql = $pdo->prepare("SELECT * FROM clientes WHERE email=? AND senha=? LIMIT 1");
    $sql->execute(array($email,$senha_cript));
    $usuario = $sql->fetch(PDO::FETCH_ASSOC); // FETCH_ASSOC SERVE PARA RECEBER OS DADOS COMO MATRIZ ASSOATIVA
    if($usuario){
        //EXISTE O USUARIO
        //CRIAR UM TOKEN
        $token = sha1(uniqid().date('d-m-Y-H-i-s'));

        //ATUALIZAR O TOKEN DESTE USUARIO NO BANCO
        $sql = $pdo->prepare("UPDATE clientes SET token=? WHERE email=? AND senha=?");
        if($sql->execute(array($token,$email,$senha_cript))){
            //ARMAZENAR ESSE TOKEN NA SESSAO (SESSION)
            $_SESSION['TOKEN'] = $token;
            header('location: restrita.php');
        }
    }else{
        $erro_login = "Usuário ou senha incorretos!";
    }
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/estilo.css" rel="stylesheet">
    <title>Login</title>
</head>
<body>
    <form method="post">
        <h2>Login</h2>

        <?php if(isset($_GET['result']) && ($_GET['result']=="ok")){ ?>
            <div class="sucesso">
                Cadastrado com sucesso!
            </div>
        <?php } ?>

        <?php if(isset($erro_login)){ ?>
            <div class="erro-geral">
            <?php echo $erro_login; ?>
            </div>
        <?php } ?>

        <div class="input-group">
            <img class="input-icon" src="img/social-media.png">
            <input type="email" name="email" placeholder="Digite seu email" required>
        </div>

        <div class="input-group">
            <img class="input-icon" src="img/lock.png">
            <input type="password" name="senha" placeholder="Digite sua senha" required>
        </div>

        <button class="btn-blue" type="submit">Fazer Login</button>
        <a href="cadastrar.php">Ainda não tenho cadastro</a>
    </form>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <?php if(isset($_GET['result']) && ($_GET['result']=="ok")){ ?>
        <script>
            setTimeout(() => {
                $('.sucesso').addClass('oculto');
            }, 3000);
        </script>
    <?php } ?>
</body>
</html>