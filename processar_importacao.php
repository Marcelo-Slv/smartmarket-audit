<?php
require_once 'conexao.php';

// ---------------------------------------------------------------------
// Ação: limpar todas as vendas importadas
// ---------------------------------------------------------------------
if (($_POST['acao'] ?? '') === 'limpar') {
    $conn->query('DELETE FROM vendas_importadas');
    header('Location: importar.php?msg=' . urlencode('Todas as vendas importadas foram removidas.'));
    exit;
}

// ---------------------------------------------------------------------
// Validação do upload
// ---------------------------------------------------------------------
if (empty($_FILES['arquivo']) || $_FILES['arquivo']['error'] !== UPLOAD_ERR_OK) {
    header('Location: importar.php?erro=' . urlencode('Selecione um arquivo CSV para importar.'));
    exit;
}

$tamanho_maximo = 5 * 1024 * 1024; // 5 MB
if ($_FILES['arquivo']['size'] > $tamanho_maximo) {
    header('Location: importar.php?erro=' . urlencode('Arquivo muito grande (máximo 5 MB).'));
    exit;
}

$nome_original = basename($_FILES['arquivo']['name']);
if (strtolower(pathinfo($nome_original, PATHINFO_EXTENSION)) !== 'csv') {
    header('Location: importar.php?erro=' . urlencode('Somente arquivos .csv são aceitos.'));
    exit;
}

// ---------------------------------------------------------------------
// Lê o CSV
// ---------------------------------------------------------------------
$handle = fopen($_FILES['arquivo']['tmp_name'], 'r');
if (!$handle) {
    header('Location: importar.php?erro=' . urlencode('Não foi possível ler o arquivo enviado.'));
    exit;
}

// Detecta o delimitador (; , ou TAB) pela primeira linha
$primeira_linha = (string) fgets($handle);
rewind($handle);
$delimitadores = [
    ';'   => substr_count($primeira_linha, ';'),
    ','   => substr_count($primeira_linha, ','),
    "\t"  => substr_count($primeira_linha, "\t"),
];
arsort($delimitadores);
$delim = array_key_first($delimitadores);
$delim = $delimitadores[$delim] > 0 ? $delim : ';';

$cabecalho = fgetcsv($handle, 0, $delim);
if (!$cabecalho) {
    fclose($handle);
    header('Location: importar.php?erro=' . urlencode('O arquivo está vazio ou inválido.'));
    exit;
}

// Remove BOM (caso o arquivo venha do Excel) e normaliza os nomes
$cabecalho = array_map(fn($c) => mb_strtolower(trim(str_replace("\xEF\xBB\xBF", '', $c))), $cabecalho);
$pos = array_flip($cabecalho);

function posicao_coluna(array $pos, array $nomes): ?int {
    foreach ($nomes as $n) {
        if (array_key_exists($n, $pos)) {
            return $pos[$n];
        }
    }
    return null;
}

$col_data    = posicao_coluna($pos, ['data', 'date', 'dia', 'dt']);
$col_hora    = posicao_coluna($pos, ['hora', 'time', 'hr']);
$col_produto = posicao_coluna($pos, ['produto', 'item', 'descricao', 'descrição', 'nome', 'desc']);
$col_qtd     = posicao_coluna($pos, ['quantidade', 'qtd', 'qtde', 'quant']);

if ($col_data === null || $col_produto === null) {
    fclose($handle);
    header('Location: importar.php?erro=' . urlencode('Colunas "data" e "produto" são obrigatórias no CSV. Veja o modelo.csv.'));
    exit;
}

// ---------------------------------------------------------------------
// Importa as linhas
// ---------------------------------------------------------------------
function parse_data_hora(string $cel_data, string $cel_hora = ''): ?DateTime {
    $data = trim($cel_data);
    $hora = trim($cel_hora);

    // "dd/mm/aaaa hh:mm" ou "aaaa-mm-dd hh:mm:ss" na mesma célula
    if (strpos($data, ' ') !== false) {
        [$data, $parte_hora] = explode(' ', $data, 2);
        if ($hora === '') {
            $hora = $parte_hora;
        }
    }

    $dt = (strpos($data, '/') !== false)
        ? DateTime::createFromFormat('d/m/Y', $data)
        : DateTime::createFromFormat('Y-m-d', $data);

    if (!$dt) {
        return null;
    }
    $dt->setTime(0, 0);

    if ($hora !== '') {
        foreach (['H:i:s', 'H:i'] as $formato) {
            $ht = DateTime::createFromFormat($formato, $hora);
            if ($ht) {
                $dt->setTime((int) $ht->format('H'), (int) $ht->format('i'), (int) $ht->format('s'));
                break;
            }
        }
    }
    return $dt;
}

$stmt_insert = $conn->prepare('INSERT INTO vendas_importadas (data, hora, produto, produto_id) VALUES (?, ?, ?, ?)');
$stmt_prod   = $conn->prepare('SELECT id FROM produtos WHERE nome = ? LIMIT 1');

$importados   = 0;
$ignorados    = 0;
$sem_catalogo = [];

while (($linha = fgetcsv($handle, 0, $delim)) !== false) {
    if (count($linha) < 2 || trim(implode('', $linha)) === '') {
        continue;
    }

    $valor_data    = trim($linha[$col_data] ?? '');
    $valor_hora    = trim($linha[$col_hora] ?? '');
    $valor_produto = trim($linha[$col_produto] ?? '');
    $qtd           = max(1, (int) trim($linha[$col_qtd] ?? '1'));

    $dt = parse_data_hora($valor_data, $valor_hora);
    if (!$dt || $valor_produto === '') {
        $ignorados++;
        continue;
    }

    // Descobre o id do produto no catálogo (se existir)
    $produto_id = null;
    $stmt_prod->bind_param('s', $valor_produto);
    $stmt_prod->execute();
    $res = $stmt_prod->get_result();
    if ($row = $res->fetch_row()) {
        $produto_id = (int) $row[0];
    } else {
        $sem_catalogo[$valor_produto] = true;
    }

    $data_sql = $dt->format('Y-m-d');
    $hora_sql = $dt->format('H:i:s');

    // Uma linha = um item vendido. Se houver quantidade > 1, duplica as linhas.
    for ($i = 0; $i < $qtd; $i++) {
        $stmt_insert->bind_param('sssi', $data_sql, $hora_sql, $valor_produto, $produto_id);
        $stmt_insert->execute();
        $importados++;
    }
}

fclose($handle);

// Guarda uma cópia do arquivo enviado (não versionado)
if (!is_dir(__DIR__ . '/uploads')) {
    mkdir(__DIR__ . '/uploads', 0777, true);
}
move_uploaded_file($_FILES['arquivo']['tmp_name'], __DIR__ . '/uploads/' . date('Ymd_His') . '_' . $nome_original);

// ---------------------------------------------------------------------
// Resumo
// ---------------------------------------------------------------------
$resumo = "Importação concluída: $importados linha(s) importada(s)";
if ($ignorados > 0) {
    $resumo .= ", $ignorados ignorada(s)";
}
if ($sem_catalogo) {
    $resumo .= '. Produtos fora do catálogo: ' . implode(', ', array_slice(array_keys($sem_catalogo), 0, 10));
}

header('Location: importar.php?msg=' . urlencode($resumo));
exit;
