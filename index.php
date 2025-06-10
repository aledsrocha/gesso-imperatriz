<?php 

require_once 'config.php';
require_once 'models/Auth.php';

$auth = new Auth($pdo, $base);
$userInfo = $auth->checktoken();

require_once 'partials/menu.php';

?>

<div class="main-content">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      font-family: Arial, sans-serif;
      padding: 20px;
      margin: 0;
    }
    h1, h2 {
      text-align: center;
    }
    .graficos-container {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 30px;
      padding: 20px;
    }
    .grafico-box {
      width: 100%;
      max-width: 400px;
      flex: 1 1 300px;
      background: #f9f9f9;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    canvas {
      width: 100% !important;
      height: auto !important;
    }
    .filtros {
      text-align: center;
      margin: 20px 0;
    }
  </style>

  <h1>Gráficos de Saída de Produtos</h1>

  <!-- Filtros de mês e ano -->
  <div class="filtros">
    <form id="formFiltros">
        <label>Dia:</label>
<select name="dia">
  <?php for ($d = 1; $d <= 31; $d++): ?>
    <option value="<?=$d?>" <?=($d == date('d') ? 'selected' : '')?>><?=$d?></option>
  <?php endfor; ?>
</select>
      <label>Mês:</label>
      <select id="mes" name="mes">
        <?php 
          for ($i = 1; $i <= 12; $i++) {
            $valor = str_pad($i, 2, '0', STR_PAD_LEFT);
            echo "<option value=\"$i\">$valor</option>";
          }
        ?>
      </select>
      <label>Ano:</label>
      <select id="ano" name="ano">
        <?php
          $ano_atual = date('Y');
          for ($i = 0; $i <= 8; $i++) {
            $ano = $ano_atual + $i;
            echo "<option value=\"$ano\">$ano</option>";
          }
        ?>
      </select>
      <button type="submit">Filtrar</button>
    </form>
  </div>

  <div class="graficos-container">
    <div class="grafico-box">
      <h2>Diário</h2>
      <canvas id="graficoDiario"></canvas>
    </div>
    <div class="grafico-box">
      <h2>Semanal</h2>
      <canvas id="graficoSemanal"></canvas>
    </div>
    <div class="grafico-box">
      <h2>Mensal</h2>
      <canvas id="graficoMensal"></canvas>
    </div>
  </div>

  <script>
    let graficos = {};

    document.getElementById('formFiltros').addEventListener('submit', function(e) {
      e.preventDefault();
      carregarDados();
    });
    
    async function carregarDados() {
const dia = document.querySelector('select[name="dia"]').value;
const mes = document.getElementById('mes').value;
const ano = document.getElementById('ano').value;
      try {
        const resposta = await fetch(`dados_grafico.php?dia=${dia}&mes=${mes}&ano=${ano}`);
        if (!resposta.ok) throw new Error('Erro ao buscar dados');
        const dados = await resposta.json();

        renderPizzaChart('graficoDiario', dados.diario);
        renderPizzaChart('graficoSemanal', dados.semanal);
        renderPizzaChart('graficoMensal', dados.mensal);
      } catch (error) {
        console.error('Erro:', error);
        alert('Falha ao carregar os dados dos gráficos.');
      }
    }

    function renderPizzaChart(canvasId, dados) {
      const ctx = document.getElementById(canvasId).getContext('2d');
      if (graficos[canvasId]) {
        graficos[canvasId].destroy();
      }

      const totais = {};
      for (let produto in dados) {
        totais[produto] = Object.values(dados[produto]).reduce((a, b) => a + b, 0);
      }

      const produtos = Object.keys(totais);
      const valores = Object.values(totais);

      const cores = produtos.map((_, i) => `hsl(${i * 360 / produtos.length}, 70%, 60%)`);

      graficos[canvasId] = new Chart(ctx, {
        type: 'pie',
        data: {
          labels: produtos,
          datasets: [{
            data: valores,
            backgroundColor: cores
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: {
              position: 'bottom'
            },
            tooltip: {
              callbacks: {
                label: function(context) {
                  const total = valores.reduce((a, b) => a + b, 0);
                  const percent = (context.raw / total * 100).toFixed(1);
                  return `${context.label}: ${context.raw} (${percent}%)`;
                }
              }
            }
          }
        }
      });
    }

    // Carregar dados ao abrir a página
    window.onload = carregarDados;
  </script>
</div>

<?php
require_once 'partials/footer.php';
?>
