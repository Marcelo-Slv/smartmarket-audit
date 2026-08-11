# SmartMarket Audit

> Sistema web para **conferência de compras em mercados de autoatendimento**: compare o que as **câmeras** registraram com as vendas da **planilha do fiscal** e descubra divergências automaticamente.

<div align="center">

### 🧪 Teste a demonstração online

**[marcelo-slv.github.io/smartmarket-audit](https://marcelo-slv.github.io/smartmarket-audit/)**

_Demonstração com dados fictícios — para testar o visual e o fluxo, sem precisar instalar nada._

</div>

---

## 🎯 O problema

Em mercados de autoatendimento, conferir o que foi **registrado pela câmera** contra o que foi **pago** é feito manualmente e sujeito a erro. O SmartMarket Audit automatiza essa conferência:

1. **Importe a planilha do fiscal** — o mercado exporta as vendas da compra em CSV;
2. **Lance a auditoria** — quem confere as câmeras registra o que viu (produto + quantidade);
3. **O sistema compara** — para cada item da câmera, busca o mesmo produto nas vendas importadas (±10 min);
4. **Divergência** — se a câmera viu mais do que foi pago, a auditoria fica marcada como **Divergente**;
5. **Relatório** — dashboard com KPIs e gráfico + exportação do relatório em PDF.

## ✨ Funcionalidades

- 📊 **Dashboard** com KPIs (total, OK, divergências, taxa) e gráfico;
- 🎥 **Nova Auditoria** com adição dinâmica de produtos;
- 📄 **Importação de planilha (CSV)** — cada pessoa envia o próprio fiscal;
- ⚠️ **Sinalização de produtos fora do catálogo**;
- 🖨️ **Relatório em PDF** (impressão);
- 📚 **Tutoriais** passo a passo dentro do sistema;
- 🔒 **Prepared statements** (PHP) para evitar injeção de SQL;
- 🎨 Front-end responsivo (também funciona no celular).

## 🛠️ Tecnologias

- **PHP 8** — backend
- **MySQL** — banco de dados
- **HTML / CSS / JavaScript** — front-end com **Chart.js**
- **GitHub Pages** — demonstração estática com dados fictícios

## 🚀 Rodando localmente (XAMPP)

Pré-requisitos: **XAMPP** (Apache + MySQL + PHP) e **git**.

```bash
# 1. Clone o projeto para dentro do htdocs
cd C:\xampp\htdocs
git clone https://github.com/Marcelo-Slv/smartmarket-audit.git
cd smartmarket-audit
```

> 💡 Alternativa: versionar em outra pasta e criar um link no htdocs:
> `mklink /J "C:\xampp\htdocs\smartmarket-audit" "C:\caminho\do\projeto"`

1. **Subir o Apache e o MySQL** no painel do XAMPP;
2. **Criar o banco** (estrutura, sem dados) pelo phpMyAdmin → *Importar* → `database/schema.sql`;
3. *(Opcional)* Inserir dados fictícios para teste: `database/seed.sql`;
4. **Configurar** o banco: copie `config.example.php` para `config.php` e ajuste se necessário;
5. Acesse **`http://localhost/smartmarket-audit/`**.

## 🧾 Formato da planilha (CSV)

A primeira linha é o cabeçalho. Colunas: `data`, `hora`, `produto` e (opcional) `quantidade`.

```csv
data;hora;produto;quantidade
11/08/2026;14:23;Coca-Cola 2L;1
11/08/2026;14:24;Arroz 5kg;1
```

- `data` (obrigatória): `dd/mm/aaaa` ou `aaaa-mm-dd`;
- `hora` (opcional): `hh:mm` ou `hh:mm:ss`;
- `produto` (obrigatória): nome **exatamente como no catálogo** (tabela `produtos`);
- `quantidade` (opcional): se vier, a linha é duplicada para a conferência bater.

> O separador pode ser `;` (padrão do Excel BR) ou `,`. Arquivo modelo: [`modelo.csv`](modelo.csv).

## 📚 Como usar (resumo)

1. **Importar Planilha** → envie o CSV do fiscal;
2. Confira na mesma tela se há produtos **fora do catálogo** e cadastre-os (tabela `produtos`);
3. **Nova Auditoria** → registre o que a câmera viu (unidade, data/hora, produtos e quantidades);
4. **Dashboard** → acompanhe KPIs, gráfico e histórico;
5. **Relatório** → "Baixar / Imprimir PDF".

O tutorial completo fica dentro do sistema, na tela **"Como usar"**.

## 🔒 Privacidade dos dados

O repositório contém **apenas a estrutura** do banco e dados **fictícios** para teste. Os dados reais do mercado ficam somente no seu MySQL local:

- as credenciais ficam em `config.php`, que está no `.gitignore`;
- os CSVs enviados ficam em `uploads/`, também ignorado;
- `database/schema.sql` nunca contém dados reais.

## 📁 Estrutura

```
smartmarket-audit/
├── index.html                  # Redireciona para a demo (GitHub Pages)
├── assets/css/estilo.css       # Design compartilhado
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
├── tutorial.php                # Tutoriais dentro do sistema
├── _nav.php                    # Menu lateral compartilhado
├── modelo.csv                  # Exemplo de planilha
├── docs/                       # Demo estática (GitHub Pages)
└── uploads/                    # Cópias dos CSV enviados (NÃO versionado)
```

## 🗺️ Próximos passos

- [ ] Cadastro de produtos/unidades pela interface
- [ ] Login e permissões
- [ ] Parâmetro configurável do intervalo de conferência
- [ ] Deploy (hospedagem PHP + MySQL)
- [ ] Automatização do Sistema por meio de uma IA que deverá analisar as câmeras

---

Feito por [Marcelo Expedito](https://github.com/Marcelo-Slv) · Projeto em destaque no [meu portfólio](https://marcelo-slv.github.io/portf-lio/).
