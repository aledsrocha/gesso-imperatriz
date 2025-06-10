<?php
require_once '../config.php';
require_once 'dao/TarefaDAO.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['nome']) && !empty($_POST['data'])) {
    $tarefaDao = new TarefaDAO($pdo);
    $tarefaDao->adicionar($_POST['nome'], $_POST['data']);
}

header("Location: tarefas.php");
exit;