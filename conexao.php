<?php
/**
 * Conexão com o banco de dados.
 *
 * Usa config.php (não versionado). Se ele não existir, cai no
 * config.example.php para o sistema rodar "de primeira".
 */
$arquivoConfig = __DIR__ . '/config.php';
if (!is_file($arquivoConfig)) {
    $arquivoConfig = __DIR__ . '/config.example.php';
}
require_once $arquivoConfig;
require_once __DIR__ . '/funcoes.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    die('Erro ao conectar no banco. Verifique se o MySQL está rodando e se o config.php está correto.');
}
