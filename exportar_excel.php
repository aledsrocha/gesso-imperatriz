<?php
require_once 'config.php'; // Inclui a conexão com o banco de dados via PDO

// Captura os parâmetros da URL via GET
$tipo = $_GET['tipo'] ?? 'entrada'; // Define o tipo: 'entrada' (padrão) ou 'saida'
$month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m'); // Mês selecionado ou mês atual
$year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y'); // Ano selecionado ou ano atual

// Valida o tipo: só são aceitos 'entrada' ou 'saida'
if (!in_array($tipo, ['entrada', 'saida'])) {
    die('Tipo inválido. Use "entrada" ou "saida".');
}

// Define o nome do campo de data, baseado no tipo
$dataCampo = $tipo === 'saida' ? 'data_saida' : 'data_entrada';

// Define o nome do arquivo que será gerado para download
$nomeArquivo = "relatorio_{$tipo}_{$month}_{$year}.xls";

// Define os headers HTTP para forçar o download de um arquivo Excel
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=\"$nomeArquivo\""); // nome do arquivo
header("Cache-Control: max-age=0");
header("Expires: 0");

// Prepara a consulta SQL para buscar os dados filtrados por mês/ano
$sql = $pdo->prepare("
    SELECT 
        nome_produto, 
        quantidade_em_estoque, 
        preco_custo, 
        responsavel, 
        $dataCampo as data_movimentacao 
    FROM estoque 
    WHERE MONTH($dataCampo) = :month 
      AND YEAR($dataCampo) = :year 
    ORDER BY $dataCampo ASC
");

// Define os valores dos parâmetros no SQL
$sql->bindValue(':month', $month, PDO::PARAM_INT);
$sql->bindValue(':year', $year, PDO::PARAM_INT);
$sql->execute();

// Busca todos os resultados como array associativo
$produtos = $sql->fetchAll(PDO::FETCH_ASSOC);

// Inicia a construção da tabela HTML (que será interpretada pelo Excel)
echo '<table border="1">';
echo '<thead>
        <tr>
            <th>Nome do Produto</th>
            <th>Quantidade</th>
            <th>Preço de Custo</th>
            <th>Responsável</th>
            <th>Data ' . ucfirst($tipo) . '</th> <!-- Capitaliza a primeira letra -->
        </tr>
      </thead>
      <tbody>';

// Verifica se houve resultados
if (count($produtos) === 0) {
    // Nenhum resultado encontrado
    echo '<tr><td colspan="5">Nenhum registro encontrado para o filtro informado.</td></tr>';
} else {
    // Loop pelos produtos encontrados
    foreach ($produtos as $produto) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($produto['nome_produto']) . '</td>'; // Nome do produto
        echo '<td>' . htmlspecialchars($produto['quantidade_em_estoque']) . '</td>'; // Quantidade
        echo '<td>R$ ' . number_format($produto['preco_custo'], 2, ',', '.') . '</td>'; // Preço de custo formatado
        echo '<td>' . htmlspecialchars($produto['responsavel'] ?? '-') . '</td>'; // Responsável (ou '-' se nulo)
        echo '<td>' . htmlspecialchars(date('d/m/Y', strtotime($produto['data_movimentacao']))) . '</td>'; // Data formatada
        echo '</tr>';
    }
}

// Fecha a tabela
echo '</tbody>';
echo '</table>';

// Encerra o script para evitar que qualquer saída extra atrapalhe o arquivo
exit;
