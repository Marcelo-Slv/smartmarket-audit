<?php
/**
 * Sidebar compartilhada. Defina $pagina_atual antes de incluir.
 * Valores: dashboard, auditoria, importar, relatorio, tutorial.
 */
$itens_menu = [
    'dashboard' => ['dashboard.php', 'Dashboard', '<svg class="icone" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>'],
    'auditoria' => ['auditoria_nova.php', 'Nova Auditoria', '<svg class="icone" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1"/><path d="m9 14 2 2 4-4"/></svg>'],
    'importar'  => ['importar.php', 'Importar Planilha', '<svg class="icone" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>'],
    'relatorio' => ['relatorio.php', 'Relatório', '<svg class="icone" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>'],
    'tutorial'  => ['tutorial.php', 'Como usar', '<svg class="icone" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>'],
];
?>
<aside class="sidebar">
    <div class="logo">
        <div class="logo-icon">SA</div>
        <div>
            <div class="logo-titulo">SmartMarket Audit</div>
            <div class="logo-sub">Sistema real</div>
        </div>
    </div>
    <nav class="nav">
        <?php foreach ($itens_menu as $chave => [$url, $rotulo, $icone]) : ?>
            <a class="nav-link<?= ($pagina_atual ?? '') === $chave ? ' ativo' : '' ?>" href="<?= $url ?>">
                <?= $icone ?>
                <?= h($rotulo) ?>
            </a>
        <?php endforeach; ?>
    </nav>
    <div class="sidebar-roda">
        Banco: <?= h(DB_NAME) ?><br>
        <a href="https://github.com/Marcelo-Slv/smartmarket-audit" target="_blank" rel="noopener">Ver no GitHub</a>
    </div>
</aside>
