<?php

// Importa o arquivo que contém a classe de acesso ao banco de dados para usuários
require_once 'dao/UserDaoMysql.php';

// Classe responsável por autenticação e controle de login de usuários
class Auth {
    private $pdo;         // Conexão com o banco de dados
    private $base;        // URL base para redirecionamentos
    private $userDao;     // Instância de UserDaoMysql para manipular usuários

    // Construtor recebe a conexão PDO e a base URL
    public function __construct(PDO $pdo, $base) {
        $this->pdo = $pdo;
        $this->base = $base;
        $this->userDao = new UserDaoMysql($this->pdo); // Instancia o DAO passando o PDO
    }

    // Verifica se o token do usuário está presente e válido
    public function checkToken() {
        // Verifica se há um token salvo na sessão
        if (!empty($_SESSION['token'])) {

            $token = $_SESSION['token'];

            // Busca o usuário com base no token
            $user = $this->userDao->findByToken($_SESSION['token']);
            if ($user) {
                return $user; // Retorna o usuário autenticado
            }
        }

        // Se o token for inválido ou não existir, redireciona para login
        header("Location:" . $this->base . "/login.php");
        exit;
    }

    // Valida login do usuário comparando o e-mail e senha informados
    public function validateLogin($email, $password) {
        // Busca o usuário pelo e-mail
        $user = $this->userDao->findByEmail($email);
        if ($user) {
            // Verifica se a senha digitada bate com a senha criptografada
            if (password_verify($password, $user->password)) {
                // Gera novo token aleatório para a sessão
                $token = md5(time().rand(0,9999));
                $_SESSION['token'] = $token;
                $user->token = $token;

                // Atualiza o token do usuário no banco de dados
                $this->userDao->update($user);
                return true; // Login válido
            }
        }
        return false; // Login inválido
    }

    // Verifica se um e-mail já está cadastrado no sistema
    public function emailExists($email) {
        return $this->userDao->findByEmail($email) ? true : false;
    }

    // Cadastra um novo usuário no sistema
    public function registerUser($name, $password, $email, $tipo) {
        // Criptografa a senha do usuário
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // Gera um token único para o novo usuário
        $token = md5(time().rand(0, 9999));

        // Cria um novo objeto User com os dados fornecidos
        $user = new User();
        $user->name = $name;
        $user->email = $email;
        $user->password = $hash;
        $user->token = $token;
        $user->tipo = $tipo;

        // Insere o usuário no banco de dados
        $this->userDao->insert($user);

        // Define o token na sessão (a linha abaixo está incompleta)
        $_SESSION['token'] = $token; // ← Aqui seria ideal definir: $_SESSION['token'] = $token;
    }

} // Fim da classe Auth

?>
