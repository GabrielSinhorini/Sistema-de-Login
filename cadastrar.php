<?php
require("config/conexao.php");

//VERIFICAR SE A POSTAGEM EXISTE DE ACORDO COM OS CAMPOS
if(isset($_POST['nome_completo']) && isset($_POST['email']) && isset($_POST['senha']) && isset($_POST['repete_senha'])){
    if(empty($_POST['nome_completo']) && empty($_POST['email']) && empty($_POST['senha']) && empty($_POST['repete_senha'])){
        $erro_geral = 'Todos os campos são obrigatórios';
    }else{
        //RECEBER VALORES VINDOS DO POST E LIMPAR
        $nome = limparPost($_POST['nome_completo']);
        $email = limparPost($_POST['email']);
        $senha = limparPost($_POST['senha']);
        $senha_cript = sha1($senha); //CRIPTOGRAFANDO A SENHA COM O SHA1
        $repete_senha = limparPost($_POST['repete_senha']);

        //VERIFICAR SE NOME É APENAS LETRAS E ESPAÇOS
        if (!preg_match("/^[a-zA-Z-' ]*$/",$nome)) {
            $erro_nome = "Somente permitido letras e espaços em branco!";
          }

        //VERIFICAR SE EMAIL É VALIDO
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erro_email = "Formato de e-mail inválido!";
        }
        
        //VERIFICAR SE SENHA TEM MAIS DE 6 DIGITOS
        if (strlen($senha) < 6){
            $erro_senha = "Senha deve ter 6 caracteres ou mais";
        }

        //VERIFICAR SE SENHA É IGUAL REPETE SENHA
        if($senha !== $repete_senha){
            $erro_repete_senha = "As senhas precisam ser iguais!";
        }

        if(!isset($erro_geral) && !isset($erro_nome) && !isset($erro_email) && !isset($erro_senha) && !isset($erro_repete_senha)){
            //VERIFICAR SE O EMAIL JÁ ESTÁ CADASTRADO NO BANCO
            $sql = $pdo->prepare("SELECT * FROM clientes WHERE email=? LIMIT 1");
            $sql->execute(array($email));
            $usuario = $sql->fetch();
            //SE NÃO EXISITR O USUARIO - ADICIONAR NO BANCO
            if(!$usuario){
                $recupera_senha = "";
                $token = "";
                $status = "novo"; // ISSO SERÁ USADO PARA CONFIRMAR O EMAIL
                $data_cadastro = date('d-m-Y');
                $sql = $pdo->prepare("INSERT INTO clientes VALUE(null,?,?,?,?,?,?,?)");
                if($sql->execute(array($nome,$email,$senha_cript,$recupera_senha,$token,$status, $data_cadastro))){
                    header('location: index.php?result=ok'); // SE CONSEGUIU CADASTRAR REDIRECIONAR P LOGIN
                }
            }else{
                //JÁ EXISTE USUARIO APRESENTAR ERRO
                $erro_geral = "Usuário já cadastrado";
            }

        }

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

    <title>Cadastrar</title>
</head>
<body>
    <form method="post">
        <h2>Cadastrar</h2>
        
        <?php if(isset($erro_geral)){ ?>
            <div class="erro-geral">
            <?php echo $erro_geral; ?>
            </div>
        <?php } ?>

        <div class="input-group">
                <img class="input-icon" src="img/id.png">
                <input <?php if(isset($erro_geral) or isset($erro_nome)){echo 'class="erro-input"';} ?>name="nome_completo" type="text" placeholder="Nome completo" <?php if(isset($_POST['nome_completo'])){ echo "value='".$_POST['nome_completo']."'";}?> required>
                <?php if(isset($erro_nome)){ ?>
                    <div class="erro"><?php echo $erro_nome; ?></div>
                <?php } ?>
            </div>

        <div class="input-group">
            <img class="input-icon" src="img/social-media.png">
            <input <?php if(isset($erro_geral) or isset($erro_email)){echo 'class="erro-input"';} ?>type="email" name="email" placeholder="Seu email" <?php if(isset($_POST['email'])){ echo "value='".$_POST['email']."'";}?> required>
            <?php if(isset($erro_email)){ ?>
                <div class="erro"><?php echo $erro_email; ?></div>
            <?php } ?>
        </div>

        <div class="input-group">
            <img class="input-icon" src="img/lock.png">
            <input <?php if(isset($erro_geral) or isset($erro_senha)){echo 'class="erro-input"';} ?> type="password" name="senha" placeholder="Senha de pelo menos 6 digitos" <?php if(isset($_POST['senha'])){ echo "value='".$_POST['senha']."'";}?> required>
            <?php if(isset($erro_senha)){ ?>
                <div class="erro"><?php echo $erro_senha; ?></div>
            <?php } ?>
        </div>

        <div class="input-group">
            <img class="input-icon" src="img/unlock.png">
            <input <?php if(isset($erro_geral) or isset($erro_repete_senha)){echo 'class="erro-input"';} ?> type="password" name="repete_senha" placeholder="Repita a senha" <?php if(isset($_POST['repete_senha'])){ echo "value='".$_POST['repete_senha']."'";}?> required>
            <?php if(isset($erro_repete_senha)){ ?>
                <div class="erro"><?php echo $erro_repete_senha; ?></div>
            <?php } ?>
        </div>

        <button class="btn-blue" type="submit">Cadastrar</button>
        <a href="index.php">Já tenho uma conta</a>
    </form>

</body>
</html>