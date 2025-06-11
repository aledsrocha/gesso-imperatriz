<?php
require_once 'config.php';

// Consulta SQL para somar o total por produto
$sql = "SELECT nome_produto, SUM(quantidade_em_estoque) as total_estoque 
        FROM estoque 
        GROUP BY nome_produto 
        ORDER BY nome_produto ASC";

$stmt = $pdo->query($sql);
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once 'partials/menu.php';
?>

  <title>Total de Estoque por Produto</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f4f4;
      padding: 20px;
    }
    h1 {
      text-align: center;
      color: #333;
    }
    table {
      width: 100%;
      max-width: 700px;
      margin: 20px auto;
      background: #fff;
      border-collapse: collapse;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    th, td {
      padding: 12px;
      border: 1px solid #ccc;
      text-align: left;
    }
    th {
      background: #333;
      color: white;
    }
    tr:nth-child(even) {
      background-color: #f9f9f9;
    }
  </style>
    <div class="main-content">
  <h1>📦 Total de Estoque por Produto</h1>
  <table>
    <thead>
      <tr>
        <th>Produto</th>
        <th>Total em Estoque</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($produtos as $produto): ?>
        <tr>
          <td><?= htmlspecialchars($produto['nome_produto']) ?></td>
          <td><?= htmlspecialchars($produto['total_estoque']) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  </div>

  <?php require_once 'partials/footer.php'; ?>

