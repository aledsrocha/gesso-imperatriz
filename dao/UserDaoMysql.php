<?php
// Inclui o modelo da entidade User
require_once 'models/User.php';

// Classe que implementa as operações de acesso ao banco de dados para usuários
class UserDaoMysql implements UserDao {
    private $pdo;

    // Construtor recebe uma instância PDO para conexão com o banco de dados
    public function __construct(PDO $driver) {
        $this->pdo = $driver;
    }

    /**
     * Método privado que cria e retorna um objeto User com base nos dados do banco
     * @param array $array Dados do usuário vindos do banco
     * @return User Objeto usuário preenchido
     */
    private function generateUser($array) {
        $u = new User();
        $u->id = $array['id'] ?? 0;
        $u->email = $array['email'] ?? '';
        $u->password = $array['password'] ?? '';
        $u->name = $array['name'] ?? '';
        $u->token = $array['token'] ?? '';
        $u->tipo = $array['tipo'] ?? '';

        return $u;
    }

    /**
     * Busca um usuário pelo token
     * @param string $token Token de autenticação do usuário
     * @return User|false Retorna o usuário correspondente ou false se não encontrar
     */
    public function findByToken($token) {
        if (!empty($token)) {
            $sql = $this->pdo->prepare("SELECT * FROM usuarios WHERE token = :token");
            $sql->bindValue(':token', $token);
            $sql->execute();

            // Verifica se encontrou o usuário
            if ($sql->rowCount() > 0) {
                $data = $sql->fetch(PDO::FETCH_ASSOC);
                $user = $this->generateUser($data);
                return $user;
            }
        }

        return false;
    }

    /**
     * Busca um usuário pelo e-mail
     * @param string $email E-mail do usuário
     * @return User|false Retorna o usuário correspondente ou false se não encontrar
     */
    public function findByEmail($email) {
        if (!empty($email)) {
            $sql = $this->pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
            $sql->bindValue(':email', $email);
            $sql->execute();

            // Verifica se encontrou o usuário
            if ($sql->rowCount() > 0) {
                $data = $sql->fetch(PDO::FETCH_ASSOC);
                $user = $this->generateUser($data);
                return $user;
            }
        }

        return false;
    }

    /**
     * Insere um novo usuário no banco
     * @param User $u Objeto usuário com os dados a serem inseridos
     * @return bool Retorna true se inserido com sucesso
     */
    public function insert(User $u) {
        $sql = $this->pdo->prepare("INSERT INTO usuarios (email, password, name, token, tipo) 
                                    VALUES (:email, :password, :name, :token, :tipo)");
        $sql->bindValue(':email', $u->email);
        $sql->bindValue(':password', $u->password);
        $sql->bindValue(':name', $u->name);
        $sql->bindValue(':token', $u->token);
        $sql->bindValue(':tipo', $u->tipo);
        $sql->execute();

        return true;
    }

    /**
     * Atualiza os dados de um usuário existente
     * @param User $u Objeto usuário com os dados atualizados
     * @return bool Retorna true se atualizado com sucesso
     */
    public function update(User $u) {
        $sql = $this->pdo->prepare("UPDATE usuarios SET 
                                        email = :email,
                                        password = :password,
                                        name = :name,
                                        token = :token,
                                        tipo = :tipo
                                    WHERE id = :id");

        $sql->bindValue(':email', $u->email);
        $sql->bindValue(':password', $u->password);
        $sql->bindValue(':name', $u->name);
        $sql->bindValue(':token', $u->token);
        $sql->bindValue(':tipo', $u->tipo);
        $sql->bindValue(':id', $u->id);
        $sql->execute();

        return true;
    }
}
?>
