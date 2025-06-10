<?php
require_once  'models/Tarefas.php';

class TarefaDAO {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function adicionar($nome, $data) {
        $sql = $this->pdo->prepare("INSERT INTO tarefas (nome, data) VALUES (:nome, :data)");
        $sql->bindValue(':nome', $nome);
        $sql->bindValue(':data', $data);
        $sql->execute();
    }

    public function listarTodos() {
        $sql = $this->pdo->query("SELECT * FROM tarefas ORDER BY data ASC");
        if ($sql->rowCount() > 0) {
            return $sql->fetchAll(PDO::FETCH_CLASS, 'Tarefa');
        }
        return [];
    }

    public function marcarComoConcluida($id) {
        $sql = $this->pdo->prepare("UPDATE tarefas SET concluida = 1 WHERE id = :id");
        $sql->bindValue(':id', $id);
        $sql->execute();
    }
}
