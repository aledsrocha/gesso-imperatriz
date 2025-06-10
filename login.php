<?php
    require_once 'config.php';
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login - Gesso Imperatriz</title>
  <link rel="stylesheet" type="text/css" href="<?=$base;?>/assents/css/login.css" />
</head>
<body>

  <div class="login-box">
    <img src="<?=$base;?>/media/logo.png" alt="Logo Gesso Imperatriz" class="logo" />
    <h2>Login</h2>
    <form action="<?=$base;?>/login_action.php" method="POST">
      <?php  if(!empty($_SESSION['flash'])): ?>
                <?=$_SESSION['flash'];?>
                <?php $_SESSION['flash'] = ''; ?>

            <?php endif; ?>
      <input type="email" name="email" placeholder="email" required />
      <input type="password" name="password" placeholder="Senha" required />
      <input type="submit" value="Entrar" />
    </form>
  </div>

</body>
</html>
