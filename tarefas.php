<?php
require_once 'config.php';
require_once 'dao/tarefaDaoMysql.php';

$tarefaDao = new TarefaDAO($pdo);

if (isset($_GET['concluir'])) {
    $tarefaDao->marcarComoConcluida($_GET['concluir']);
    header("Location: lista.php");
    exit;
}

$tarefas = $tarefaDao->listarTodos();
$dataAtual = date('Y-m-d');

require_once 'partials/menu.php';
?>

  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f2f2f2;
      padding: 20px;
    }
    h1, h2 {
      text-align: center;
    }
    form {
      max-width: 600px;
      margin: 0 auto 30px;
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 0 8px rgba(0,0,0,0.1);
    }
    input, .btn-salvar {
      padding: 10px;
      margin: 5px 0;
      width: 100%;
      font-size: 16px;
    }
    table {
      width: 100%;
      background: white;
      border-collapse: collapse;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    th, td {
      padding: 12px;
      border-bottom: 1px solid #ddd;
    }
    th {
      background: #333;
      color: white;
    }
    .hoje { background-color: #fff3cd; }
    .atrasada { background-color: #f8d7da; }
    .futura { background-color: #e2e3e5; }
    .concluida {
      background-color: #cce5ff;
      text-decoration: line-through;
    }
    .status {
      font-weight: bold;
      font-size: 12px;
      text-transform: uppercase;
    }
    .btn-concluir {
      font-size: 14px;
      background: #28a745;
      color: white;
      padding: 5px 10px;
      text-decoration: none;
      border-radius: 6px;
    }
    .btn-concluir:hover {
      background: #218838;
    }
  </style>

    <div class="main-content">
  <h1>📋 Lista de Tarefas</h1>

  <form method="POST" action="<?=$base;?>/adicionartarefas.php">
    <h2>Adicionar Tarefa</h2>
    <input type="text" name="nome" placeholder="Nome da Tarefa" required>
    <input type="date" name="data" required>
    <button type="submit" class="btn-salvar">Salvar</button>
  </form>
<!-- Tabela -->
  <table>
    <tr>
      <th>Tarefa</th>
      <th>Data</th>
      <th>Status</th>
      <th>Ação</th>
    </tr>

<!-- Lista de tarefas -->
   <?php foreach ($tarefas as $tarefa): 
  $classe = '';
  $status = '';

  if ($tarefa->concluida) {
      $classe = 'concluida';
      $status = 'Concluída';
  } elseif ($tarefa->data == $dataAtual) {
      $classe = 'hoje';
      $status = 'Para Hoje';
  } elseif ($tarefa->data < $dataAtual) {
      $classe = 'atrasada';
      $status = 'Atrasada';
  } else {
      $classe = 'futura';
      $status = 'Futura';
  }
?>
<tr class="<?= $classe ?>">
  <td><?= htmlspecialchars($tarefa->nome) ?></td>
  <td><?= date('d/m/Y', strtotime($tarefa->data)) ?></td>
  <td class="status"><?= $status ?></td>
  <td>
    <?php if (!$tarefa->concluida): ?>
      <a href="?concluir=<?= $tarefa->id ?>" class="btn-concluir">✔️ Concluir</a>
    <?php else: ?>
      —
    <?php endif; ?>
  </td>
</tr>
<?php endforeach; ?>

  </table>
</div>

<?php
require_once 'partials/footer.php';
?>