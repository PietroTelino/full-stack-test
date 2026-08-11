# Pull Request – Fork vs Upstream

## Repositórios comparados
- **Upstream (original):** https://github.com/leettech/full-stack-test
- **Branch upstream:** master
- **Fork (origin):** https://github.com/PietroTelino/full-stack-test
- **Branch do fork:** master

---

## Objetivo
Este documento descreve as alterações realizadas neste fork em relação ao repositório original, considerando apenas os commits introduzidos após o fork, utilizando o *merge-base* entre os repositórios.

---

## Principais mudanças no backend

- **Faturas (Invoice)**
  - Refatoração do `InvoiceController` para usar `DB::transaction()` na criação e edição de faturas, evitando estados inconsistentes entre fatura e itens.
  - Extração de lógica de negócio para o modelo `Invoice` com métodos como `calculateItemsTotal()` e `syncItems()`, centralizando regras de cálculo e manipulação de itens.
- **Serviço de banco (BankService)**
  - Tipagem mais forte com uso de `CarbonInterface` para datas de vencimento.
  - Tratamento explícito de erros da API fake do banco com logs estruturados e exceções com mensagem mais clara.
- **Validações e segurança**
  - `PasswordController` passou a usar `Hash::make()` ao atualizar senhas, corrigindo gravação em texto puro.
  - Regras `ValidCpfOrCnpj` e `ValidPhoneNumber` tiveram comportamento documentado via testes e usados de forma consistente com as mesmas regras de negócio do backend.
- **Código de fatura e consultas**
  - `InvoiceCode::generate()` recebeu tipagem explícita e pequena limpeza de implementação.
  - `SendOverdueInvoiceNotifications` passou a usar o escopo `Invoice::overdue()` em vez de repetir a lógica de filtro no comando, reduzindo duplicação.

## Principais mudanças no frontend

- **Utilitários compartilhados**
  - Criação de `resources/js/lib/format.ts` para padronizar formatação de moeda e datas no frontend.
  - Criação de `resources/js/lib/invoice.ts` para mapear `InvoiceStatus` → variante visual (cores de badge), evitando lógica duplicada em várias telas.
- **Modelagem de tipos**
  - Definição de tipos compartilhados em `resources/js/types/invoice.ts` e `resources/js/types/customer.ts` e reexport em `types/index.ts`.
  - Ajuste das páginas de invoices e customers para consumir esses tipos em vez de interfaces locais duplicadas.
- **Componentes compartilhados**
  - `Breadcrumbs.vue` passou a usar o tipo global de breadcrumb, deixando a API mais consistente com o resto da aplicação.
  - `Pagination.vue` ganhou tipagem forte para os links de paginação e uma lógica de filtro de páginas mais explícita.

## Testes automatizados adicionados

- **Backend (PHP / Pest)**
  - Testes unitários para `ValidCpfOrCnpj` cobrindo documentos válidos/ inválidos e campos opcionais.
  - Testes unitários para `ValidPhoneNumber` cobrindo limites de dígitos e comportamento para valores vazios.
  - Testes unitários para `BankService` usando `Http::fake()` e `Log::spy()` para validar:
    - requisições geradas (payload e URL),
    - tratamento de respostas de erro,
    - logging de falhas e exceções,
    - comportamento de `getBillet()` em cenários de sucesso, erro HTTP e exceção.

- **Frontend (Vitest + Vue Test Utils)**
  - Configuração do Vitest com ambiente `jsdom`, alias `@` e `setup` global para limpar o DOM entre testes.
  - Testes de unidade para:
    - `lib/format.ts` (formatação de moeda e datas),
    - `lib/invoice.ts` (mapeamento de status de fatura para variantes visuais),
    - `validations/customer.ts` (validação client-side de e-mail, telefone e CPF/CNPJ).
  - Testes de componente para:
    - `components/Breadcrumbs.vue`, garantindo renderização correta da trilha com o último item como página atual.
  - Testes de página para:
    - `pages/invoices/Create.vue`, cobrindo lógica interna de:
      - inicialização do formulário,
      - adicionar/remover itens de fatura,
      - cálculo do total a partir de quantidade e preço unitário,
      - submissão chamando `form.post('/invoices')`.

## Motivação geral das alterações

- **Confiabilidade e consistência de dados:** uso maior de transações, extração de regras de negócio para modelos e eliminação de duplicação de lógica reduzem risco de estados inconsistentes.
- **Segurança:** correção do fluxo de atualização de senha para sempre armazenar hashes, alinhado com boas práticas de autenticação.
- **Reutilização e manutenção:** centralização de tipos, helpers e regras (tanto no backend quanto no frontend) facilita evolução futura sem quebrar contratos ou repetir código.
- **Testabilidade:** introdução de testes automatizados de backend e frontend fornece uma base mínima de regressão para as áreas mais sensíveis (validações, integração com o banco fake e formulários principais da aplicação).
