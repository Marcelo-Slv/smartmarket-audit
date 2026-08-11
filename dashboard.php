<?php
require_once 'conexao.php';
$pagina_atual = 'dashboard';

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
<div class="layout">

    <?php include '_nav.php'; ?>

    <main class="conteudo">
        <div class="topo">
            <div class="topo-titulo">
                <h1>Dashboard Auditoria</h1>
                <span class="badge badge-ghost">Sistema real</span>
            </div>
            <p>Sistema de auditoria de mercado autônomo — compare o que as câmeras registraram com as vendas da planilha.</p>
        </div>

        <?php if ($msg) : ?>
            <div class="alert alert-ok"><?= h($msg) ?></div>
        <?php endif; ?>
        <?php if ($erro) : ?>
            <div class="alert alert-bad"><?= h($erro) ?></div>
        <?php endif; ?>

        <div class="kpis">
            <div class="kpi">
                <h3>Total de auditorias</h3>
                <div class="valor accent"><?= $total_auditorias ?></div>
                <div class="detalhe">registradas no período</div>
            </div>
            <div class="kpi">
                <h3>OK</h3>
                <div class="valor ok"><?= $total_ok ?></div>
                <div class="detalhe">itens conferidos e pagos</div>
            </div>
            <div class="kpi">
                <h3>Divergências</h3>
                <div class="valor bad"><?= $total_div ?></div>
                <div class="detalhe">câmera viu mais do que o pago</div>
            </div>
            <div class="kpi">
                <h3>Taxa de divergência</h3>
                <div class="valor warn"><?= $taxa ?>%</div>
                <div class="detalhe">do total de auditorias</div>
            </div>
            <div class="kpi">
                <h3>Auditorias hoje</h3>
                <div class="valor accent"><?= $total_hoje ?></div>
                <div class="detalhe">neste dia</div>
            </div>
        </div>

        <div class="grid">
            <div class="card">
                <h2>Status das auditorias</h2>
                <div class="grafico-box">
                    <canvas id="grafico"></canvas>
                </div>
                <div style="text-align:center; margin-top:14px;">
                    <a class="btn" href="auditoria_nova.php">+ Lançar Auditoria</a>
                </div>
            </div>

            <div class="card">
                <h2>Histórico de auditorias</h2>
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
                    <button type="submit" class="btn">Filtrar</button>
                    <a href="dashboard.php" class="btn btn-secundario">Limpar</a>
                </form>

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
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($historico->num_rows === 0) : ?>
                                <tr><td colspan="7" class="vazio">Nenhuma auditoria encontrada.</td></tr>
                            <?php endif; ?>
                            <?php while ($linha = $historico->fetch_assoc()) : ?>
                                <tr>
                                    <td>#<?= (int) $linha['id'] ?></td>
                                    <td><?= formatar_data($linha['data_hora_video']) ?></td>
                                    <td><?= h($linha['unidade']) ?></td>
                                    <td><?= h($linha['itens']) ?></td>
                                    <td>
                                        <span class="status <?= $linha['status'] === 'OK' ? 'ok' : 'bad' ?>">
                                            <?= h($linha['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= h($linha['observacao']) ?></td>
                                    <td>
                                        <a class="btn btn-bad btn-pequeno" href="remover.php?id=<?= (int) $linha['id'] ?>"
                                           onclick="return confirm('Excluir esta auditoria?');">Excluir</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

</div>

<script>
    new Chart(document.getElementById('grafico'), {
        type: 'doughnut',
        data: {
            labels: ['OK', 'Divergente'],
            datasets: [{
                data: [<?= $total_ok ?>, <?= $total_div ?>],
                backgroundColor: ['#16a34a', '#dc2626'],
                borderWidth: 0
            }]
        },
        options: {
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
</script>
</body>
</html>
