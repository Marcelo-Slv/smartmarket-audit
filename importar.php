<?php
require_once 'conexao.php';

$msg  = $_GET['msg']  ?? '';
$erro = $_GET['erro'] ?? '';

$total_importadas = (int) $conn->query('SELECT COUNT(*) FROM vendas_importadas')->fetch_row()[0];
$sem_catalogo     = $conn->query(
    'SELECT produto, COUNT(*) AS qtd FROM vendas_importadas WHERE produto_id IS NULL GROUP BY produto ORDER BY qtd DESC LIMIT 15'
);
$recentes         = $conn->query(
    "SELECT vi.id, vi.data, vi.hora, vi.produto,
            COALESCE(p.nome, '—') AS catalogado
     FROM vendas_importadas vi
     LEFT JOIN produtos p ON p.id = vi.produto_id
     ORDER BY vi.id DESC LIMIT 30"
);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Importar Planilha — SmartMarket Audit</title>
    <link rel="stylesheet" href="assets/css/estilo.css">
</head>
<body>
<div class="container">

    <nav class="nav">
        <a href="dashboard.php">Dashboard</a>
        <a href="auditoria_nova.php">Nova Auditoria</a>
        <a href="importar.php" class="ativo">Importar Planilha</a>
        <a href="relatorio.php">Relatório</a>
    </nav>

    <div class="topo">
        <h1>Importar Planilha do Fiscal</h1>
        <p>Envie o CSV com as vendas da compra e o sistema usará esses dados para conferir com as câmeras.</p>
    </div>

    <?php if ($msg) : ?>
        <div class="msg"><?= h($msg) ?></div>
    <?php endif; ?>
    <?php if ($erro) : ?>
        <div class="erro"><?= h($erro) ?></div>
    <?php endif; ?>

    <div class="grid">

        <div class="card">
            <h2>Enviar arquivo</h2>

            <p style="color:#666; font-size:14px;">
                Colunas esperadas: <strong>data</strong>, <strong>hora</strong>,
                <strong>produto</strong> e (opcional) <strong>quantidade</strong>.
                Aceita vírgula ou ponto e vírgula como separador.
                <a href="modelo.csv" download>Baixar modelo.csv</a>
            </p>

            <form action="processar_importacao.php" method="POST" enctype="multipart/form-data">
                <label>Arquivo CSV</label>
                <input type="file" name="arquivo" accept=".csv,text/csv" required>

                <button type="submit" style="margin-top:20px;">Importar Planilha</button>
            </form>

            <hr style="margin:25px 0; border:none; border-top:1px solid #eee;">

            <h3>Limpar dados importados</h3>
            <p style="color:#666; font-size:14px; margin-bottom:10px;">
                Remove todas as vendas importadas da planilha.
            </p>
            <form action="processar_importacao.php" method="POST"
                  onsubmit="return confirm('Excluir TODAS as vendas importadas da planilha?');">
                <input type="hidden" name="acao" value="limpar">
                <button type="submit" class="btn-perigo">Limpar importações</button>
            </form>
        </div>

        <div class="card">
            <h2>Resumo</h2>
            <div class="kpis" style="margin-bottom:0;">
                <div class="kpi">
                    <h3>Vendas importadas</h3>
                    <h1><?= $total_importadas ?></h1>
                </div>
            </div>

            <h3 style="margin-top:20px;">Produtos não encontrados no catálogo</h3>
            <?php if ($sem_catalogo->num_rows === 0) : ?>
                <p style="color:#28a745; font-weight:600;">Todos os produtos da planilha já estão no catálogo.</p>
            <?php else : ?>
                <table>
                    <tr>
                        <th>Produto</th>
                        <th>Linhas</th>
                    </tr>
                    <?php while ($nc = $sem_catalogo->fetch_assoc()) : ?>
                        <tr>
                            <td><?= h($nc['produto']) ?></td>
                            <td><?= (int) $nc['qtd'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </table>
                <p style="color:#888; font-size:13px; margin-top:10px;">
                    Cadastre esses produtos no banco (tabela <code>produtos</code>) e reimporte para o nome bater.
                </p>
            <?php endif; ?>
        </div>

    </div>

    <div class="card">
        <h2>Últimas importações</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Data</th>
                <th>Hora</th>
                <th>Produto na planilha</th>
                <th>Produto no catálogo</th>
            </tr>
            <?php if ($recentes->num_rows === 0) : ?>
                <tr><td colspan="5" style="text-align:center; color:#888;">Nenhuma venda importada ainda.</td></tr>
            <?php endif; ?>
            <?php while ($r = $recentes->fetch_assoc()) : ?>
                <tr>
                    <td><?= (int) $r['id'] ?></td>
                    <td><?= date('d/m/Y', strtotime($r['data'])) ?></td>
                    <td><?= h($r['hora']) ?></td>
                    <td><?= h($r['produto']) ?></td>
                    <td><?= h($r['catalogado']) ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

</div>
</body>
</html>
