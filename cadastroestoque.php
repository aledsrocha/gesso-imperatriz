<?php 

	require_once 'config.php';
	require_once 'models/Auth.php';


	$auth = new Auth($pdo, $base);

	$userInfo = $auth->checktoken();


    require_once 'partials/menu.php';

	
	

	
	
 ?>

<div class="main-content">
   <link rel="stylesheet" href="<?=$base;?>/assents/css/estoque.css">

<h2>Cadastrar Novo Produto</h2>

<form action="<?=$base;?>/cadastrar_action.php" method="post">
    <label for="nome_produto">Nome do Produto:</label>
    <input type="text" id="nome_produto" name="nome_produto" required>

    <label for="descricao">Descrição:</label>
    <textarea id="descricao" name="descricao" required></textarea>

    <label for="categoria">Categoria:</label>
    <input type="text" id="categoria" name="categoria" required>

    <label for="fornecedor">Fornecedor:</label>
    <input type="text" id="fornecedor" name="fornecedor" required>

    <label for="quantidade_em_estoque">Quantidade em Estoque:</label>
    <input type="number" id="quantidade_em_estoque" name="quantidade_em_estoque" min="0" required>

    <label for="preco_custo">Preço de Custo (R$):</label>
    <input type="text" id="preco_custo" name="preco_custo" required>

    <label for="data_entrada">Data de Entrada:</label>
    <input type="text" id="data_entrada" name="data_entrada">

    <label for="data_saida">Data de Saída:</label>
    <input type="text" id="data_saida" name="data_saida">

    <label for="responsavel">Responsável:</label>
    <input type="text" id="responsavel" name="responsavel" required>

    <label for="observacoes">Observações:</label>
    <textarea id="observacoes" name="observacoes"></textarea>

    <button class="salvar" type="submit">Salvar Produto</button>
</form>


  </div>

  <script src="https://unpkg.com/imask"></script>
    <script >
        IMask(
            document.getElementById("data_entrada"),
            {mask: '00/00/0000'}
            );


         IMask(
            document.getElementById("data_saida"),
            {mask: '00/00/0000'}
            );
    </script>






<?php
	require_once 'partials/footer.php';

?>
