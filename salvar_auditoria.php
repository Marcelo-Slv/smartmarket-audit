<?php
require_once 'conexao.php';

$unidade     = (int) ($_POST['unidade_id'] ?? 0);
$data_video  = trim($_POST['data_hora_video'] ?? '');
$obs         = trim($_POST['observacao'] ?? '');
$produtos    = (array) ($_POST['produto_id'] ?? []);
$quantidades = (array) ($_POST['quantidade'] ?? []);

// Validação básica
if ($unidade <= 0 || $data_video === '') {
    header('Location: auditoria_nova.php?erro=' . urlencode('Preencha a unidade e a data/hora do vídeo.'));
    exit;
}

$items_validos = 0;
foreach ($produtos as $pid) {
    if ((int) $pid > 0) {
        $items_validos++;
    }
}
if ($items_validos === 0) {
    header('Location: auditoria_nova.php?erro=' . urlencode('Adicione pelo menos um produto.'));
    exit;
}

// 1) Cria o registro da auditoria
$stmt_aud = $conn->prepare(
    'INSERT INTO auditoria_camera (unidade_id, data_hora_video, status, observacao) VALUES (?, ?, ?, ?)'
);
$status_inicial = 'OK';
$stmt_aud->bind_param('isss', $unidade, $data_video, $status_inicial, $obs);
$stmt_aud->execute();
$auditoria_id = $conn->insert_id;

// 2) Para cada produto, compara com as vendas da planilha (±10 min)
$stmt_venda = $conn->prepare(
    "SELECT COUNT(*) FROM vendas_importadas
     WHERE produto_id = ?
       AND CONCAT(data, ' ', hora) BETWEEN DATE_SUB(?, INTERVAL 10 MINUTE) AND DATE_ADD(?, INTERVAL 10 MINUTE)"
);
$stmt_item = $conn->prepare(
    'INSERT INTO auditoria_itens (auditoria_id, produto_id, quantidade) VALUES (?, ?, ?)'
);

$status_final = 'OK';

foreach ($produtos as $i => $pid) {
    $pid = (int) $pid;
    $qtd = max(1, (int) ($quantidades[$i] ?? 1));

    if ($pid <= 0) {
        continue;
    }

    $stmt_venda->bind_param('iss', $pid, $data_video, $data_video);
    $stmt_venda->execute();
    $qtd_paga = (int) $stmt_venda->get_result()->fetch_row()[0];

    // Viu na câmera mais do que foi pago => divergência
    if ($qtd_paga < $qtd) {
        $status_final = 'Divergente';
    }

    $stmt_item->bind_param('iii', $auditoria_id, $pid, $qtd);
    $stmt_item->execute();
}

// 3) Atualiza o status final da auditoria
$stmt_upd = $conn->prepare('UPDATE auditoria_camera SET status = ? WHERE id = ?');
$stmt_upd->bind_param('si', $status_final, $auditoria_id);
$stmt_upd->execute();

header('Location: dashboard.php?msg=' . urlencode("Auditoria #$auditoria_id concluída! Status: $status_final"));
exit;
