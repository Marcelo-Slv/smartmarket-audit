<?php
require_once 'conexao.php';

$id = (int) ($_GET['id'] ?? 0);

if ($id > 0) {
    // Remove primeiro os itens (por causa da chave estrangeira)
    $stmt_itens = $conn->prepare('DELETE FROM auditoria_itens WHERE auditoria_id = ?');
    $stmt_itens->bind_param('i', $id);
    $stmt_itens->execute();

    $stmt_aud = $conn->prepare('DELETE FROM auditoria_camera WHERE id = ?');
    $stmt_aud->bind_param('i', $id);
    $stmt_aud->execute();
}

header('Location: dashboard.php?msg=' . urlencode('Auditoria excluída.'));
exit;
