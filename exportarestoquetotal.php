<?php
require_once 'config.php';
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Consulta dos dados
$sql = "SELECT nome_produto, SUM(quantidade_em_estoque) as total_estoque 
        FROM estoque 
        GROUP BY nome_produto 
        ORDER BY nome_produto ASC";

$stmt = $pdo->query($sql);
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Criação do Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Estoque Total");

// Cabeçalhos
$sheet->setCellValue('A1', 'Produto');
$sheet->setCellValue('B1', 'Total em Estoque');

// Dados
$linha = 2;
foreach ($produtos as $produto) {
    $sheet->setCellValue("A$linha", $produto['nome_produto']);
    $sheet->setCellValue("B$linha", $produto['total_estoque']);
    $linha++;
}

// Envio para download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="estoque_total.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
