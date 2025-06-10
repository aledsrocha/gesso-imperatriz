<?php
    require_once 'config.php';
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>cadastro da gesso imperatriz</title>
  <link rel="stylesheet" type="text/css" href="<?=$base;?>/assents/css/login.css" />
</head>
<body>

  <div class="login-box">
    <img src="<?=$base;?>/media/logo.png" alt="Logo Gesso Imperatriz" class="logo" />
    <h2>Login</h2>
    <form action="<?=$base;?>/signup_action.php" method="POST">
    <?php  if(!empty($_SESSION['flash'])): ?>
                <?=$_SESSION['flash'];?>
                <?php $_SESSION['flash'] = ''; ?>

                <?php endif; ?>
      <input type="text" name="name" placeholder="Nome completo" required />
      <input type="password" name="senha" placeholder="Senha" required />
      <input type="email" name="email" placeholder="email" required />
      <input type="text" name="tipo" placeholder="tipo de usuario" required />
      <input type="submit" value="cadastrar" />
    </form>
  </div>

</body>
</html>
