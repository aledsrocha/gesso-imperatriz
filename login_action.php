<?php
require_once 'config.php';   // Inclui configurações do banco e conexão PDO
require_once 'models/Auth.php';  // Inclui a classe de autenticação

// Recebe e valida o email enviado via POST (verifica formato válido)
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

// Recebe a senha enviada via POST (sem filtro para manter o valor original)
$password = filter_input(INPUT_POST, 'password'); 

// Verifica se email e senha foram informados corretamente
if ($email && $password) {
    
    // Instancia o objeto Auth com conexão PDO e base URL
    $auth = new Auth($pdo, $base);
    
    // Tenta validar o login usando email e senha
    if ($auth->validateLogin($email, $password)) {
        
        // Se válido, redireciona para a página base (home)
        header("Location:" . $base);
        exit;  // Finaliza a execução para evitar código extra
    }
}

// Se email ou senha forem inválidos, define mensagem de erro na sessão
$_SESSION['flash'] = 'usuario ou senha invalidos';

// Redireciona de volta para a página de login
header("Location:" . $base . "/login.php");
exit;
?>
