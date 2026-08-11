# SmartMarket Audit

> Sistema web para **conferência de compras em mercados de autoatendimento** — compara os produtos registrados no banco de dados com os itens do fiscal, aponta divergências e gera relatórios com exportação em PDF.

## 🎯 Contexto

Em mercados de autoatendimento, a conferência entre o que foi cadastrado no sistema e o que consta no fiscal da compra é manual e sujeita a erro. O SmartMarket Audit automatiza essa conferência:

- Compara **produtos do banco de dados** com **itens do fiscal**;
- Marca **divergências** (produto não encontrado, preço/quantidade diferente, etc.);
- Gera **relatórios** consolidados;
- **Exporta em PDF** para registro e análise.

## 🛠️ Tecnologias sugeridas

- **Angular** — front-end
- **MySQL** — banco de dados
- **Relatórios / PDF** — geração de documentos

## 🗺️ Próximos passos

- [ ] Definir o modelo do banco (produtos, compras, itens do fiscal)
- [ ] Tela de importação/leitura do fiscal
- [ ] Regras de conferência e divergências
- [ ] Relatórios e exportação em PDF
- [ ] Deploy

---

Feito por [Marcelo Expedito](https://github.com/Marcelo-Slv).
