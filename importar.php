<?php
require_once 'conexao.php';
$pagina_atual = 'importar';

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
<div class="layout">

    <?php include '_nav.php'; ?>

    <main class="conteudo">
        <div class="topo">
            <div class="topo-titulo">
                <h1>Importar Planilha do Fiscal</h1>
                <span class="badge badge-ghost">Sistema real</span>
            </div>
            <p>Envie o CSV com as vendas da compra. O sistema usa esses dados para conferir com as câmeras.</p>
        </div>

        <?php if ($msg) : ?>
            <div class="alert alert-ok"><?= h($msg) ?></div>
        <?php endif; ?>
        <?php if ($erro) : ?>
            <div class="alert alert-bad"><?= h($erro) ?></div>
        <?php endif; ?>

        <div class="grid">
            <div class="card">
                <h2>Enviar arquivo</h2>
                <p class="descricao">
                    Colunas esperadas: <code>data</code>, <code>hora</code>, <code>produto</code>
                    e (opcional) <code>quantidade</code>. Separador <code>;</code> ou <code>,</code>.
                    <a href="modelo.csv" download>Baixar modelo.csv</a>
                </p>

                <form action="processar_importacao.php" method="POST" enctype="multipart/form-data">
                    <label>Arquivo CSV</label>
                    <input type="file" name="arquivo" accept=".csv,text/csv" required>
                    <button type="submit" class="btn" style="margin-top:16px;">Importar Planilha</button>
                </form>

                <hr>

                <h3>Limpar dados importados</h3>
                <p class="descricao">Remove todas as vendas importadas da planilha.</p>
                <form action="processar_importacao.php" method="POST"
                      onsubmit="return confirm('Excluir TODAS as vendas importadas da planilha?');">
                    <input type="hidden" name="acao" value="limpar">
                    <button type="submit" class="btn btn-bad">Limpar importações</button>
                </form>
            </div>

            <div class="card">
                <h2>Resumo</h2>
                <div class="kpis" style="margin-bottom:0;">
                    <div class="kpi">
                        <h3>Vendas importadas</h3>
                        <div class="valor accent"><?= $total_importadas ?></div>
                    </div>
                </div>

                <h3 style="margin-top:18px;">Produtos não encontrados no catálogo</h3>
                <div class="tabela-wrapper">
                    <table>
                        <?php if ($sem_catalogo->num_rows === 0) : ?>
                            <tr><td class="vazio">Todos os produtos da planilha já estão no catálogo.</td></tr>
                        <?php else : ?>
                            <thead>
                                <tr><th>Produto</th><th>Linhas</th></tr>
                            </thead>
                            <tbody>
                                <?php while ($nc = $sem_catalogo->fetch_assoc()) : ?>
                                    <tr>
                                        <td><?= h($nc['produto']) ?></td>
                                        <td><?= (int) $nc['qtd'] ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        <?php endif; ?>
                    </table>
                </div>
                <p class="descricao">
                    Produtos que não existem na tabela <code>produtos</code>. Cadastre-os no banco e reimporte para o nome bater.
                </p>
            </div>
        </div>

        <div class="card">
            <h2>Últimas importações</h2>
            <div class="tabela-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Data</th>
                            <th>Hora</th>
                            <th>Produto na planilha</th>
                            <th>No catálogo?</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($recentes->num_rows === 0) : ?>
                            <tr><td colspan="5" class="vazio">Nenhuma venda importada ainda.</td></tr>
                        <?php endif; ?>
                        <?php while ($r = $recentes->fetch_assoc()) : ?>
                            <tr>
                                <td><?= (int) $r['id'] ?></td>
                                <td><?= date('d/m/Y', strtotime($r['data'])) ?></td>
                                <td><?= h($r['hora']) ?></td>
                                <td><?= h($r['produto']) ?></td>
                                <td>
                                    <span class="status <?= $r['catalogado'] !== '—' ? 'ok' : 'bad' ?>">
                                        <?= $r['catalogado'] !== '—' ? 'Sim' : 'Não' ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

</div>
</body>
</html>
