<?php
/**
 * Funções auxiliares do SmartMarket Audit.
 */

// Escapa texto para exibição segura em HTML.
function h($valor) {
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

// Formata data do banco (YYYY-MM-DD HH:MM:SS) para exibição (DD/MM/YYYY HH:MM).
function formatar_data($data_hora) {
    if (empty($data_hora)) {
        return '—';
    }
    $ts = strtotime($data_hora);
    return $ts ? date('d/m/Y H:i', $ts) : h($data_hora);
}
