<?php
require_once 'conexao.php';
$pagina_atual = 'tutorial';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Como usar — SmartMarket Audit</title>
    <link rel="stylesheet" href="assets/css/estilo.css">
</head>
<body>
<div class="layout">

    <?php include '_nav.php'; ?>

    <main class="conteudo">
        <div class="topo">
            <div class="topo-titulo">
                <h1>Como usar o SmartMarket Audit</h1>
                <span class="badge badge-ghost">Sistema real</span>
            </div>
            <p>Guia passo a passo para quem vai operar o sistema no dia a dia.</p>
        </div>

        <div class="card">
            <h2>Fluxo de trabalho</h2>
            <div class="passo">
                <div class="passo-num">1</div>
                <div class="passo-conteudo">
                    <h3>Importe a planilha do fiscal</h3>
                    <p>
                        O mercado exporta as vendas da compra em um arquivo CSV. Na tela
                        <strong>Importar Planilha</strong>, escolha o arquivo e clique em importar.
                    </p>
                    <ul>
                        <li>Colunas esperadas: <code>data</code>, <code>hora</code>, <code>produto</code> e (opcional) <code>quantidade</code>;</li>
                        <li>O separador pode ser <code>;</code> (Excel brasileiro) ou <code>,</code>;</li>
                        <li>Use o <a href="modelo.csv" download>modelo.csv</a> como base.</li>
                    </ul>
                </div>
            </div>

            <div class="passo">
                <div class="passo-num">2</div>
                <div class="passo-conteudo">
                    <h3>Confira os produtos fora do catálogo</h3>
                    <p>
                        Depois da importação, a tela mostra quais produtos da planilha
                        <strong>não existem no catálogo</strong>. Cadastre esses nomes na tabela
                        <code>produtos</code> (exatamente como aparecem) para a conferência funcionar.
                    </p>
                </div>
            </div>

            <div class="passo">
                <div class="passo-num">3</div>
                <div class="passo-conteudo">
                    <h3>Lance a auditoria de câmera</h3>
                    <p>
                        Assista ao vídeo da câmera e, na tela <strong>Nova Auditoria</strong>, selecione a unidade,
                        a data/hora do vídeo e os <strong>produtos vistos</strong> com as quantidades.
                    </p>
                </div>
            </div>

            <div class="passo">
                <div class="passo-num">4</div>
                <div class="passo-conteudo">
                    <h3>Entenda o resultado</h3>
                    <p>
                        Para cada item, o sistema procura o mesmo produto nas vendas importadas em um
                        intervalo de <strong>±10 minutos</strong> da hora do vídeo:
                    </p>
                    <ul>
                        <li><strong class="ok">OK</strong> — a quantidade paga é igual ou maior que a vista na câmera;</li>
                        <li><strong class="bad">Divergente</strong> — a câmera viu mais do que foi pago (possível perda/fraude).</li>
                    </ul>
                </div>
            </div>

            <div class="passo">
                <div class="passo-num">5</div>
                <div class="passo-conteudo">
                    <h3>Gere o relatório</h3>
                    <p>
                        Na tela <strong>Relatório</strong>, clique em "Baixar / Imprimir PDF" para gerar um
                        documento consolidado com todas as auditorias e divergências do período.
                    </p>
                </div>
            </div>
        </div>

        <div class="card">
            <h2>Perguntas frequentes</h2>

            <details>
                <summary>O que é uma "divergência"?</summary>
                <p>É quando a câmera registrou um produto sendo pego, mas não existe o pagamento correspondente na planilha (naquele horário). Isso indica possível perda no estoque.</p>
            </details>

            <details>
                <summary>A planilha precisa de um formato específico?</summary>
                <p>Sim, é só seguir as colunas <code>data;hora;produto;quantidade</code>. Baixe o <a href="modelo.csv" download>modelo.csv</a>. Se a sua planilha tem outras colunas, reorganize as colunas no Excel antes de salvar como CSV.</p>
            </details>

            <details>
                <summary>O intervalo de ±10 minutos pode ser alterado?</summary>
                <p>Por enquanto é fixo no código. É possível ajustar em <code>salvar_auditoria.php</code> no campo <code>INTERVAL 10 MINUTE</code>.</p>
            </details>

            <details>
                <summary>Quero ver a demonstração online</summary>
                <p>Acesse <a href="https://marcelo-slv.github.io/smartmarket-audit/" target="_blank" rel="noopener">marcelo-slv.github.io/smartmarket-audit</a> para ver o sistema em ação com dados fictícios.</p>
            </details>
        </div>
    </main>

</div>
</body>
</html>
