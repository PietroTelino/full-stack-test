# Desafio Técnico Full Stack - Leet

## 🎯 Objetivo
Este é um teste prático para avaliar suas habilidades como desenvolvedor full stack. Você receberá um projeto **completamente abandonado** com diversos problemas técnicos, desde pequenos bugs até questões críticas de segurança e arquitetura.
Sua missão é **entender, consertar e refatorar** este projeto, transformando-o em uma aplicação **pronta para produção**.

## 🧰 Rodando o projeto localmente (Windows)
Pré-requisitos:
- PHP 8.2+ (com extensões comuns do Laravel: openssl, pdo_sqlite, mbstring, tokenizer, xml)
- Composer
- Node.js (recomendado 18+ / 20+) + npm

Setup inicial (PowerShell) - na raiz do projeto:
```bash
composer install
Copy-Item .env.example .env
php artisan key:generate
New-Item -ItemType File -Path database/database.sqlite -Force | Out-Null
php artisan migrate
npm ci
```

Rodar em modo desenvolvimento (2 terminais):
Terminal 1 (backend):
```bash
php artisan serve
```
Terminal 2 (frontend - Vite):
```bash
npm run dev
```

Alternativa (1 comando) - roda backend, Vite, queue worker e logs juntos:
```bash
composer run dev
```

Acesso:
- App: http://127.0.0.1:8000
- Vite: http://localhost:5173

## 📋 Sobre o Projeto
Este é um sistema básico de **faturamento e cobrança** (billing) que permite:
- ✅ Gerenciar clientes (CRUD completo)
- ✅ Criar e gerenciar faturas (invoices) com múltiplos itens
- ✅ Emitir boletos bancários automaticamente
- ✅ Acompanhar status de pagamento
- ✅ Notificações de faturas vencidas
- ✅ Sistema de multi-tenancy (teams)

## 🎓 Critérios de Avaliação
Você será avaliado em:
1. **Capacidade de Análise**
2. **Qualidade Técnica**
3. **Segurança**
4. **Testes**
5. **Observabilidade**

## 📦 Entrega
1. Documente suas decisões em um arquivo `PR.md`
2. Gere um diff completo das suas alterações:
   ```bash
   git diff master > solution.diff
   ```
3. Envie o arquivo `.diff` por email para `vagas@leet.tech` com o título `Desafio Laravel - <seu nome>`

## ⏱️ Prazo
Você tem 15 dias para completar o desafio a partir da data de fork do repositório. Não esperamos que você corrija 100% dos problemas, mas queremos ver sua capacidade de priorização e execução. **Foque em entregar valor**.
