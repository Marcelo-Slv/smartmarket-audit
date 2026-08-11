<?php
require_once 'conexao.php';
$pagina_atual = 'relatorio';

$total_div = (int) $conn->query("SELECT COUNT(*) FROM auditoria_camera WHERE status = 'Divergente'")->fetch_row()[0];

$sql = "SELECT ac.id, ac.data_hora_video, ac.status, ac.observacao,
               COALESCE(u.nome_condominio, '—') AS unidade,
               (SELECT GROUP_CONCAT(CONCAT(p.nome, ' x', ai.quantidade) SEPARATOR ', ')
                FROM auditoria_itens ai
                JOIN produtos p ON p.id = ai.produto_id
                WHERE ai.auditoria_id = ac.id) AS itens
        FROM auditoria_camera ac
        LEFT JOIN unidades u ON u.id = ac.unidade_id
        ORDER BY ac.data_hora_video DESC";
$resultado = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório — SmartMarket Audit</title>
    <link rel="stylesheet" href="assets/css/estilo.css">
</head>
<body>
<div class="layout">

    <?php include '_nav.php'; ?>

    <main class="conteudo">
        <div class="topo">
            <div class="topo-titulo">
                <h1>Relatório de Auditorias</h1>
                <span class="badge badge-ghost">Sistema real</span>
            </div>
            <p>Lista consolidada das auditorias. Use "Baixar / Imprimir" para gerar o PDF.</p>
        </div>

        <button onclick="window.print()" class="btn no-print">Baixar / Imprimir PDF</button>
        <a href="dashboard.php" class="btn btn-secundario no-print">Voltar ao Dashboard</a>

        <div class="card" style="margin-top:20px;">
            <h2>Relatório de Auditorias</h2>
            <p class="descricao">
                Total de divergências: <strong><?= $total_div ?></strong> &nbsp;|&nbsp;
                Gerado em <?= date('d/m/Y H:i') ?>
            </p>
            <div class="tabela-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Data/Hora</th>
                            <th>Unidade</th>
                            <th>Produtos</th>
                            <th>Status</th>
                            <th>Observação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($resultado->num_rows === 0) : ?>
                            <tr><td colspan="6" class="vazio">Nenhuma auditoria registrada.</td></tr>
                        <?php endif; ?>
                        <?php while ($dados = $resultado->fetch_assoc()) : ?>
                            <tr>
                                <td>#<?= (int) $dados['id'] ?></td>
                                <td><?= formatar_data($dados['data_hora_video']) ?></td>
                                <td><?= h($dados['unidade']) ?></td>
                                <td><?= h($dados['itens']) ?></td>
                                <td>
                                    <span class="status <?= $dados['status'] === 'OK' ? 'ok' : 'bad' ?>">
                                        <?= h($dados['status']) ?>
                                    </span>
                                </td>
                                <td><?= h($dados['observacao']) ?></td>
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
