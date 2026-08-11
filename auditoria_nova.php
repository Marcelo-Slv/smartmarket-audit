<?php
require_once 'conexao.php';
$pagina_atual = 'auditoria';

// Lista de unidades e de produtos para os selects
$unidades = $conn->query('SELECT id, nome_condominio FROM unidades ORDER BY nome_condominio');
$produtos = $conn->query('SELECT id, nome FROM produtos ORDER BY nome ASC');
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Auditoria — SmartMarket Audit</title>
    <link rel="stylesheet" href="assets/css/estilo.css">
</head>
<body>
<div class="layout">

    <?php include '_nav.php'; ?>

    <main class="conteudo">
        <div class="topo">
            <div class="topo-titulo">
                <h1>Lançar Nova Auditoria</h1>
                <span class="badge badge-ghost">Sistema real</span>
            </div>
            <p>Registre o que as câmeras viram e o sistema compara com as vendas importadas da planilha.</p>
        </div>

        <div class="card" style="max-width: 760px;">
            <form action="salvar_auditoria.php" method="POST">
                <label>Unidade / Condomínio</label>
                <select name="unidade_id" required>
                    <option value="">Selecione...</option>
                    <?php while ($u = $unidades->fetch_assoc()) : ?>
                        <option value="<?= (int) $u['id'] ?>"><?= h($u['nome_condominio']) ?></option>
                    <?php endwhile; ?>
                </select>

                <label>Data e hora do vídeo</label>
                <input type="datetime-local" name="data_hora_video" required>

                <label>Observação</label>
                <textarea name="observacao" rows="3"></textarea>

                <hr>

                <h3>Produtos vistos na câmera</h3>
                <div id="produtos-container">
                    <div class="produto-item">
                        <div class="linha-produto">
                            <div>
                                <label>Produto</label>
                                <select name="produto_id[]" required>
                                    <option value="">Selecione...</option>
                                    <?php
                                    $produtos->data_seek(0);
                                    while ($p = $produtos->fetch_assoc()) :
                                    ?>
                                        <option value="<?= (int) $p['id'] ?>"><?= h($p['nome']) ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div>
                                <label>Quantidade</label>
                                <input type="number" name="quantidade[]" min="1" value="1" required>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-secundario" onclick="adicionarProduto()">+ Adicionar Produto</button>
                <button type="submit" class="btn" style="margin-top:14px;">Analisar Auditoria</button>
            </form>
        </div>

        <div class="card">
            <h2>Como funciona a conferência</h2>
            <ol style="padding-left:20px; color:var(--muted); line-height:1.7; font-size:14px;">
                <li>Você assiste o vídeo da câmera e cadastra o que viu (produto + quantidade);</li>
                <li>O sistema procura o mesmo produto nas <strong>vendas importadas da planilha</strong> em um intervalo de ±10 minutos;</li>
                <li>Se a câmera viu <strong>mais do que foi pago</strong>, a auditoria fica <strong class="bad">Divergente</strong>;</li>
                <li>Se tudo bateu, fica <strong class="ok">OK</strong>.</li>
            </ol>
        </div>
    </main>

</div>

<script>
    function opcoesProdutos() {
        let opcoes = '<option value="">Selecione...</option>';
        <?php
        $produtos->data_seek(0);
        while ($p = $produtos->fetch_assoc()) {
            echo "opcoes += '<option value=\"" . (int) $p['id'] . "\">" . addslashes(h($p['nome'])) . "</option>';\n";
        }
        ?>
        return opcoes;
    }

    function adicionarProduto() {
        const container = document.getElementById('produtos-container');
        const bloco = document.createElement('div');
        bloco.className = 'produto-item';
        bloco.innerHTML =
            '<div class="linha-produto">' +
            '<div><label>Produto</label><select name="produto_id[]" required>' + opcoesProdutos() + '</select></div>' +
            '<div><label>Quantidade</label><input type="number" name="quantidade[]" min="1" value="1" required></div>' +
            '<button type="button" class="btn btn-bad btn-pequeno" onclick="this.parentElement.parentElement.remove()">Remover</button>' +
            '</div>';
        container.appendChild(bloco);
    }
</script>
</body>
</html>
