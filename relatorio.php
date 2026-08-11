<?php
require_once 'conexao.php';

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
    <title>Relatório de Auditorias — SmartMarket Audit</title>
    <link rel="stylesheet" href="assets/css/estilo.css">
    <style>
        body { background: #fff; padding: 30px; }
        .container { max-width: 1000px; }
        table { margin-top: 20px; }
    </style>
</head>
<body>
<div class="container">

    <button onclick="window.print()" class="btn no-print" style="margin-bottom:20px;">Baixar / Imprimir PDF</button>
    <a href="dashboard.php" class="btn btn-perigo no-print" style="margin-bottom:20px;">Voltar</a>

    <h1>Relatório de Auditorias</h1>
    <p style="color:#666;">Total de divergências: <strong><?= $total_div ?></strong> &nbsp;|&nbsp; Gerado em <?= date('d/m/Y H:i') ?></p>

    <table>
        <tr>
            <th>ID</th>
            <th>Data/Hora</th>
            <th>Unidade</th>
            <th>Produtos</th>
            <th>Status</th>
            <th>Observação</th>
        </tr>
        <?php if ($resultado->num_rows === 0) : ?>
            <tr><td colspan="6" style="text-align:center; color:#888;">Nenhuma auditoria registrada.</td></tr>
        <?php endif; ?>
        <?php while ($dados = $resultado->fetch_assoc()) : ?>
            <tr>
                <td><?= (int) $dados['id'] ?></td>
                <td><?= formatar_data($dados['data_hora_video']) ?></td>
                <td><?= h($dados['unidade']) ?></td>
                <td><?= h($dados['itens']) ?></td>
                <td class="<?= $dados['status'] === 'OK' ? 'ok' : 'divergente' ?>"><?= h($dados['status']) ?></td>
                <td><?= h($dados['observacao']) ?></td>
            </tr>
        <?php endwhile; ?>
    </table>

</div>
</body>
</html>
