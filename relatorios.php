<?php require_once 'config.php'; 

    require_once 'partials/menu.php';
?>

    <style>
        /* Estilização geral da página */
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
        }

        /* Container centralizado e com sombra para destaque */
        .container {
            max-width: 500px;
            margin: 40px auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0px 2px 8px rgba(0,0,0,0.1);
        }

        /* Título da página */
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        /* Estilos para os rótulos dos campos */
        form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        /* Estilo padrão para inputs e selects */
        form select, form button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        /* Botão de envio */
        form button {
            background-color: #28a745;
            color: white;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        /* Efeito ao passar o mouse no botão */
        form button:hover {
            background-color: #218838;
        }

        /* Estilo responsivo para telas menores */
        @media (max-width: 600px) {
            .container {
                margin: 20px;
                padding: 15px;
            }
        }
    </style>


<div class="container">
    <h2>Exportar Relatório Excel</h2>

    <!-- Formulário que envia dados para exportar_excel.php via GET -->
    <form method="get" action="exportar_excel.php" target="_blank">
        
        <!-- Campo para selecionar o tipo de relatório: entrada ou saída -->
        <label for="tipo">Tipo de Movimento</label>
        <select name="tipo" id="tipo" required>
            <option value="" disabled selected>Selecione</option>
            <option value="entrada">Entrada</option>
            <option value="saida">Saída</option>
        </select>

        <!-- Campo para selecionar o mês -->
        <label for="month">Mês</label>
        <select name="month" id="month" required>
            <?php 
            // Gera opções de mês de 1 a 12
            for ($m = 1; $m <= 12; $m++): ?>
                <option value="<?= $m ?>" <?= ($m == date('m')) ? 'selected' : '' ?>>
                    <?= str_pad($m, 2, '0', STR_PAD_LEFT) ?>
                </option>
            <?php endfor; ?>
        </select>

        <!-- Campo para selecionar o ano -->
        <label for="year">Ano</label>
        <select name="year" id="year" required>
            <?php 
            // Gera opções de ano do atual - 1 até atual + 5
            $currentYear = date('Y');
            for ($y = $currentYear - 1; $y <= $currentYear + 5; $y++): ?>
                <option value="<?= $y ?>" <?= ($y == $currentYear) ? 'selected' : '' ?>><?= $y ?></option>
            <?php endfor; ?>
        </select>

        <!-- Botão para enviar o formulário -->
        <button type="submit">Exportar para Excel</button>
    </form>
</div>


<?php require_once 'partials/footer.php'; ?>