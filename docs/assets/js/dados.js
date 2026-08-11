/* =============================================================
   DADOS FICTÍCIOS — apenas para simulação da demonstração.
   Estes dados são inventados e NÃO refletem nenhum mercado real.
   O sistema de verdade usa o banco MySQL (ver repositório).
   ============================================================= */

const DEMO = {
    unidadeAtual: 'Residencial Aurora',
    unidades: [
        'Residencial Aurora',
        'Edifício Solar',
        'Condomínio Horizonte'
    ],
    produtos: [
        'Coca-Cola 2L',
        'Arroz 5kg',
        'Feijão 1kg',
        'Leite Integral 1L',
        'Pão de Forma',
        'Detergente 500ml',
        'Sabonete',
        'Salgadinho 250g'
    ],
    kpis: {
        total: 128,
        ok: 112,
        divergentes: 16,
        taxa: 12.5,
        hoje: 9
    },
    vendasImportadas: 1420,
    semCatalogo: [
        { produto: 'Água Tônica x1', linhas: 7 },
        { produto: 'Pilha AA (pack)', linhas: 4 }
    ],
    auditorias: [
        { id: 128, data: '11/08/2026 14:23', unidade: 'Residencial Aurora', itens: 'Coca-Cola 2L x2', status: 'OK', obs: 'Conferência normal' },
        { id: 127, data: '11/08/2026 14:10', unidade: 'Edifício Solar', itens: 'Arroz 5kg x1, Feijão 1kg x1', status: 'Divergente', obs: 'Câmera viu 2, pagou 1' },
        { id: 126, data: '11/08/2026 13:58', unidade: 'Condomínio Horizonte', itens: 'Leite Integral 1L x1', status: 'OK', obs: '' },
        { id: 125, data: '10/08/2026 19:42', unidade: 'Residencial Aurora', itens: 'Pão de Forma x1, Salgadinho 250g x2', status: 'OK', obs: '' },
        { id: 124, data: '10/08/2026 18:15', unidade: 'Edifício Solar', itens: 'Detergente 500ml x1', status: 'Divergente', obs: 'Item sem pagamento' },
        { id: 123, data: '10/08/2026 17:03', unidade: 'Condomínio Horizonte', itens: 'Sabonete x3', status: 'OK', obs: '' },
        { id: 122, data: '09/08/2026 20:31', unidade: 'Residencial Aurora', itens: 'Coca-Cola 2L x1', status: 'OK', obs: '' },
        { id: 121, data: '09/08/2026 12:05', unidade: 'Edifício Solar', itens: 'Feijão 1kg x1', status: 'OK', obs: 'Revisão da câmera 02' }
    ],
    vendas: [
        { data: '11/08/2026', hora: '14:23', produto: 'Coca-Cola 2L', catalogado: true },
        { data: '11/08/2026', hora: '14:24', produto: 'Arroz 5kg', catalogado: true },
        { data: '11/08/2026', hora: '14:25', produto: 'Leite Integral 1L', catalogado: true },
        { data: '11/08/2026', hora: '14:26', produto: 'Leite Integral 1L', catalogado: true },
        { data: '11/08/2026', hora: '14:31', produto: 'Pão de Forma', catalogado: true },
        { data: '11/08/2026', hora: '14:40', produto: 'Água Tônica x1', catalogado: false },
        { data: '11/08/2026', hora: '14:44', produto: 'Detergente 500ml', catalogado: true },
        { data: '11/08/2026', hora: '14:52', produto: 'Pilha AA (pack)', catalogado: false },
        { data: '11/08/2026', hora: '15:02', produto: 'Sabonete', catalogado: true },
        { data: '11/08/2026', hora: '15:11', produto: 'Salgadinho 250g', catalogado: true }
    ]
};
