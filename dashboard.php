<?php
require_once 'conexao.php';

$msg  = $_GET['msg']  ?? '';
$erro = $_GET['erro'] ?? '';

// Filtros do histórico
$f_status = trim($_GET['status'] ?? '');
$f_inicio = trim($_GET['data_inicio'] ?? '');
$f_fim    = trim($_GET['data_fim'] ?? '');

// KPIs
$total_auditorias = (int) $conn->query('SELECT COUNT(*) FROM auditoria_camera')->fetch_row()[0];
$total_ok         = (int) $conn->query("SELECT COUNT(*) FROM auditoria_camera WHERE status = 'OK'")->fetch_row()[0];
$total_div        = (int) $conn->query("SELECT COUNT(*) FROM auditoria_camera WHERE status = 'Divergente'")->fetch_row()[0];
$total_hoje       = (int) $conn->query('SELECT COUNT(*) FROM auditoria_camera WHERE DATE(data_hora_video) = CURDATE()')->fetch_row()[0];
$taxa             = $total_auditorias > 0 ? round(($total_div / $total_auditorias) * 100, 1) : 0;

// Histórico com filtros (prepared statements)
$where  = [];
$params = [];
$types  = '';

if ($f_status !== '') {
    $where[]  = 'ac.status = ?';
    $params[] = $f_status;
    $types   .= 's';
}
if ($f_inicio !== '') {
    $where[]  = 'DATE(ac.data_hora_video) >= ?';
    $params[] = $f_inicio;
    $types   .= 's';
}
if ($f_fim !== '') {
    $where[]  = 'DATE(ac.data_hora_video) <= ?';
    $params[] = $f_fim;
    $types   .= 's';
}

$sql = "SELECT ac.id, ac.data_hora_video, ac.status, ac.observacao,
               COALESCE(u.nome_condominio, '—') AS unidade,
               (SELECT GROUP_CONCAT(CONCAT(p.nome, ' x', ai.quantidade) SEPARATOR ', ')
                FROM auditoria_itens ai
                JOIN produtos p ON p.id = ai.produto_id
                WHERE ai.auditoria_id = ac.id) AS itens
        FROM auditoria_camera ac
        LEFT JOIN unidades u ON u.id = ac.unidade_id";

if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY ac.data_hora_video DESC';

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$historico = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — SmartMarket Audit</title>
    <link rel="stylesheet" href="assets/css/estilo.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="container">

    <nav class="nav">
        <a href="dashboard.php" class="ativo">Dashboard</a>
        <a href="auditoria_nova.php">Nova Auditoria</a>
        <a href="importar.php">Importar Planilha</a>
        <a href="relatorio.php">Relatório</a>
    </nav>

    <div class="topo">
        <h1>Dashboard Auditoria</h1>
        <p>Sistema inteligente de auditoria de mercado autônomo</p>
    </div>

    <?php if ($msg) : ?>
        <div class="msg"><?= h($msg) ?></div>
    <?php endif; ?>
    <?php if ($erro) : ?>
        <div class="erro"><?= h($erro) ?></div>
    <?php endif; ?>

    <div class="kpis">
        <div class="kpi">
            <h3>Total Auditorias</h3>
            <h1><?= $total_auditorias ?></h1>
        </div>
        <div class="kpi success">
            <h3>OK</h3>
            <h1><?= $total_ok ?></h1>
        </div>
        <div class="kpi danger">
            <h3>Divergências</h3>
            <h1><?= $total_div ?></h1>
        </div>
        <div class="kpi">
            <h3>Taxa de Divergência</h3>
            <h1><?= $taxa ?>%</h1>
        </div>
        <div class="kpi">
            <h3>Auditorias Hoje</h3>
            <h1><?= $total_hoje ?></h1>
        </div>
    </div>

    <div class="grid">
        <div class="card">
            <h2>Status das Auditorias</h2>
            <canvas id="graficoAuditoria"></canvas>
            <p style="margin-top:15px; color:#666;">
                <a href="auditoria_nova.php" class="btn">+ Lançar Auditoria</a>
            </p>
        </div>

        <div class="card">
            <h2>Histórico</h2>
            <form method="GET" action="dashboard.php">
                <div class="filtros">
                    <div>
                        <label>Status</label>
                        <select name="status">
                            <option value="">Todos</option>
                            <option value="OK" <?= $f_status === 'OK' ? 'selected' : '' ?>>OK</option>
                            <option value="Divergente" <?= $f_status === 'Divergente' ? 'selected' : '' ?>>Divergente</option>
                        </select>
                    </div>
                    <div>
                        <label>De</label>
                        <input type="date" name="data_inicio" value="<?= h($f_inicio) ?>">
                    </div>
                    <div>
                        <label>Até</label>
                        <input type="date" name="data_fim" value="<?= h($f_fim) ?>">
                    </div>
                </div>
                <button type="submit">Filtrar</button>
                <a href="dashboard.php" class="btn btn-perigo" style="margin-top:15px;">Limpar</a>
            </form>

            <table>
                <tr>
                    <th>Data/Hora</th>
                    <th>Unidade</th>
                    <th>Produtos</th>
                    <th>Status</th>
                    <th>Observação</th>
                    <th>Ação</th>
                </tr>
                <?php if ($historico->num_rows === 0) : ?>
                    <tr><td colspan="6" style="text-align:center; color:#888;">Nenhuma auditoria encontrada.</td></tr>
                <?php endif; ?>
                <?php while ($linha = $historico->fetch_assoc()) : ?>
                    <tr>
                        <td><?= formatar_data($linha['data_hora_video']) ?></td>
                        <td><?= h($linha['unidade']) ?></td>
                        <td><?= h($linha['itens']) ?></td>
                        <td>
                            <span class="<?= $linha['status'] === 'OK' ? 'ok' : 'divergente' ?>">
                                <?= h($linha['status']) ?>
                            </span>
                        </td>
                        <td><?= h($linha['observacao']) ?></td>
                        <td>
                            <a class="btn btn-perigo" style="padding:6px 12px;"
                               href="remover.php?id=<?= (int) $linha['id'] ?>"
                               onclick="return confirm('Excluir esta auditoria?');">Excluir</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>

</div>

<script>
    new Chart(document.getElementById('graficoAuditoria'), {
        type: 'doughnut',
        data: {
            labels: ['OK', 'Divergente'],
            datasets: [{
                data: [<?= $total_ok ?>, <?= $total_div ?>]
            }]
        }
    });
</script>
</body>
</html>
