# SmartMarket Audit

> Sistema web para **conferência de compras em mercados de autoatendimento** — o responsável envia a **planilha do fiscal** (CSV) e o sistema compara com o que foi registrado **pelas câmeras**, apontando divergências e gerando relatórios em PDF.

> 📌 Projeto em destaque no [meu portfólio](https://marcelo-slv.github.io/portf-lio/).

## 🎯 Como funciona

1. **Importe a planilha do fiscal** — o mercado exporta as vendas da compra em CSV e você envia pela tela "Importar Planilha";
2. **Lance a auditoria** — quem confere as câmeras registra o que viu (produtos + quantidade) na tela "Nova Auditoria";
3. **O sistema compara** — para cada item da câmera, ele procura o mesmo produto nas vendas importadas em um intervalo de ±10 minutos;
4. **Divergência** — se a câmera viu mais do que foi pago, a auditoria fica marcada como **Divergente**;
5. **Relatório** — o dashboard mostra KPIs e o gráfico, e a tela "Relatório" exporta em PDF.

## 🛠️ Tecnologias

- **PHP 8** — backend e front-end (com PDO-style prepared statements via mysqli)
- **MySQL** — banco de dados
- **HTML / CSS / JavaScript** — interface (com Chart.js para os gráficos)
- **CSV** — importação da planilha do fiscal

## 🚀 Rodando localmente (XAMPP)

Pré-requisitos: **XAMPP** (Apache + MySQL + PHP) e **git**.

1. **Subir o Apache e o MySQL** no painel do XAMPP;
2. **Criar o banco** importando o schema (só a estrutura, sem dados):
   - phpMyAdmin → *Importar* → escolher `database/schema.sql`;
   - ou via linha de comando: `mysql -u root < database/schema.sql`;
3. *(Opcional)* Inserir dados fictícios para teste:
   - importar `database/seed.sql`;
4. **Configurar o banco**:
   - copie `config.example.php` para `config.php` e ajuste usuário/senha, se necessário;
5. **Acessar**: `http://localhost/smartmarket-audit/`.

> 💡 No Windows, para clonar o projeto dentro do `htdocs` e ainda versionar em outra pasta, dá para criar um link (junction):
> `mklink /J "C:\xampp\htdocs\smartmarket-audit" "C:\caminho\do\projeto"`

## 🧾 Formato da planilha (CSV)

Colunas (a primeira linha é o cabeçalho):

```csv
data;hora;produto;quantidade
11/08/2026;14:23;Coca-Cola 2L;1
11/08/2026;14:24;Arroz 5kg;1
```

- `data` (obrigatória): aceita `dd/mm/aaaa` ou `aaaa-mm-dd`;
- `hora` (opcional): aceita `hh:mm` ou `hh:mm:ss`;
- `produto` (obrigatória): nome do produto **exatamente como está no catálogo** (tabela `produtos`);
- `quantidade` (opcional): se vier, a linha é duplicada para a conferência bater.

> O separador pode ser `;` (padrão do Excel BR) ou `,`. Um arquivo modelo está em [`modelo.csv`](modelo.csv). Produtos que não existem no catálogo são importados mesmo assim, mas ficam sinalizados na tela de importação.

## 📁 Estrutura

```
smartmarket-audit/
├── assets/css/estilo.css       # Estilos compartilhados
├── database/
│   ├── schema.sql              # Estrutura do banco (SEM dados reais)
│   └── seed.sql                # Dados fictícios para teste
├── config.example.php          # Modelo de configuração (versionado)
├── config.php                  # Suas credenciais (NÃO versionado)
├── conexao.php                 # Conexão com o banco
├── dashboard.php               # KPIs, gráfico e histórico
├── auditoria_nova.php          # Formulário de auditoria (câmeras)
├── salvar_auditoria.php        # Grava e compara com a planilha
├── importar.php                # Tela de importação do CSV
├── processar_importacao.php    # Processa o upload / limpa dados
├── relatorio.php               # Relatório em PDF (impressão)
├── remover.php                 # Exclui uma auditoria
├── modelo.csv                  # Exemplo de planilha
└── uploads/                    # Cópias dos CSV enviados (NÃO versionado)
```

## 🔒 Privacidade dos dados

O repositório contém **apenas a estrutura** do banco e dados **fictícios** para teste. Os dados reais do mercado (produtos, unidades, vendas) ficam somente no seu MySQL local:

- as credenciais ficam em `config.php`, que está no `.gitignore`;
- os arquivos CSV enviados ficam em `uploads/`, também ignorado;
- `database/schema.sql` nunca contém dados reais.

## 🧪 Testes rápidos

Com o MySQL rodando e o banco criado:

```bash
# importa as vendas fictícias
curl -F "arquivo=@modelo.csv" http://localhost/smartmarket-audit/processar_importacao.php
```

## 🗺️ Próximos passos

- [x] Estrutura do banco no repositório
- [x] Tela de importação do fiscal (CSV)
- [x] Regras de conferência e divergências
- [x] Relatórios e exportação em PDF
- [ ] Cadastro de produtos/unidades pela interface
- [ ] Login e permissões
- [ ] Deploy (hosting PHP + MySQL)

---

Feito por [Marcelo Expedito](https://github.com/Marcelo-Slv).
