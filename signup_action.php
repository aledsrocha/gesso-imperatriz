<?php
// Inclui configurações de banco de dados e a classe de autenticação
require_once 'config.php';
require_once 'models/Auth.php';

// Recebe os dados do formulário via POST, aplicando filtro de validação onde necessário
$name = filter_input(INPUT_POST, 'name'); // Nome do usuário
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL); // E-mail validado
$password = filter_input(INPUT_POST, 'senha'); // Senha (sem validação específica aqui)
$tipo = filter_input(INPUT_POST, 'tipo'); // Tipo do usuário (ex: admin, cliente, etc)

// Verifica se todos os campos obrigatórios foram preenchidos
if($name && $email && $password && $tipo) {
    
    // Instancia o objeto Auth para manipulação de autenticação, passando a conexão e base URL
    $auth = new Auth($pdo, $base);
    
    // Verifica se o e-mail já está cadastrado no sistema
    if($auth->emailExists($email) == false) {
        
        // Se o e-mail não existir, realiza o cadastro do usuário
        $auth->registerUser($name, $password, $email, $tipo);
        
        // Redireciona para a página de login após cadastro bem sucedido
        header("Location:" . $base . "/login.php");
        exit;
    } else {
        // Se o e-mail já existir, exibe uma mensagem de erro via sessão flash
        $_SESSION['flash'] = 'Email ja cadastrado';
        
        // Redireciona de volta para a página de cadastro
        header("Location:" . $base . "/signup.php");
        exit;
    }
}
?>
