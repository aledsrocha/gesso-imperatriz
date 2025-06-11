
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Painel Gesso Imperatriz</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link rel="stylesheet" href="<?=$base;?>/assents/css/menu.css" />
</head>
<body>

  <!-- Topbar -->
  <div class="topbar">
    <div class="menu-toggle" onclick="toggleMenu()"><i class="fas fa-bars"></i></div>
    <img src="<?=$base;?>/media/logo.png" alt="Logo Gesso Imperatriz" />
    <button class="logout"><a href="<?=$base;?>/logout.php">Logout</a></button>
  </div>

  <!-- Sidebar -->
  <div class="sidebar" id="sidebar">
    <ul>
      <li><i class="fas fa-home"></i> <a href="<?=$base;?>/index.php">Home</a></li>
        
    </li>

      <li class="has-submenu">
        <i class="fas fa-boxes"></i> Controle de Estoque
        <ul class="submenu">
          <li><i class="fas fa-plus"></i><a href="<?=$base;?>/cadastroestoque.php">Adicionar</a></li>
          <li><i class="fas fa-edit"></i><a href="<?=$base;?>/visualizarestoque.php">Visualizar</a></li>

           </ul>
      </li>

      <li><i class="fas fa-eye"></i> <a href="<?=$base;?>/tarefas.php">Lista de Tarefas</a></li>
      <li><i class="fas fa-eye"></i> <a href="<?=$base;?>/totalestoque.php">Total estoque</a></li>

      <li class="has-submenu">
        <i class="fas fa-money-bill-wave"></i> Despesas
        <ul class="submenu">
          <li><i class="fas fa-arrow-down"></i> <a href="<?=$base;?>/entradasdespesas.php">Entradas</a></li>
          <li><i class="fas fa-arrow-up"></i> <a href="<?=$base;?>/retiradasdespesas.php">Retiradas</a></li>
        </ul>
      </li>

      <li><i class="fas fa-file-alt"></i><a href="<?=$base;?>/relatorios.php">Puxar relatórios</a></li>
      <li><i class="fas fa-file-alt"></i><a href="<?=$base;?>/relatoriosestoque.php">Puxar total estoque</a></li>
    </ul>
  </div>

  <!-- Conteúdo -->
  

  <!-- JS -->
  