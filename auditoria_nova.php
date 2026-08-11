<?php
require_once 'conexao.php';

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
<div class="container">

    <nav class="nav">
        <a href="dashboard.php">Dashboard</a>
        <a href="auditoria_nova.php" class="ativo">Nova Auditoria</a>
        <a href="importar.php">Importar Planilha</a>
        <a href="relatorio.php">Relatório</a>
    </nav>

    <div class="topo">
        <h1>Lançar Nova Auditoria</h1>
        <p>Registre o que as câmeras registraram e o sistema compara com as vendas da planilha.</p>
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

            <hr style="margin:20px 0; border:none; border-top:1px solid #eee;">

            <h3>Produtos vistos na câmera</h3>
            <div id="produtos-container">
                <div class="produto-item">
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
                    <label>Quantidade</label>
                    <input type="number" name="quantidade[]" min="1" value="1" required>
                    <hr style="margin:15px 0; border:none; border-top:1px solid #eee;">
                </div>
            </div>

            <button type="button" class="btn-verde" onclick="adicionarProduto()">+ Adicionar Produto</button>
            <button type="submit" style="margin-top:15px;">Analisar Auditoria</button>
        </form>
    </div>

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
            '<label>Produto</label>' +
            '<select name="produto_id[]" required>' + opcoesProdutos() + '</select>' +
            '<label>Quantidade</label>' +
            '<input type="number" name="quantidade[]" min="1" value="1" required>' +
            '<button type="button" class="btn-perigo" style="margin-top:15px;" onclick="this.parentElement.remove()">Remover</button>';
        container.appendChild(bloco);
    }
</script>
</body>
</html>
