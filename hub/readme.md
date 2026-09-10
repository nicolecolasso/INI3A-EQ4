# Brecharme

> **Conectando solidariedade e sustentabilidade através da gestão digital de doações e reservas para o brechó da Cáritas Bauru.**

---

https://github.com/user-attachments/assets/20d95fb6-d09f-4bde-8190-fccb45ac30a7

## 📌 Sobre o Projeto
O Brecharme é uma plataforma web desenvolvida para modernizar e otimizar os processos de doação, triagem e reserva de peças do brechó solidário mantido pela instituição Cáritas Bauru.
- **Problema identificado:** A gestão manual de doações e o controle de estoque em papel ou planilhas dificultavam a triagem de peças, geravam falta de visibilidade dos itens disponíveis para a comunidade e limitavam o alcance das arrecadações promovidas pela instituição.
- **Solução proposta:** Uma aplicação web integrada que automatiza o fluxo de cadastro e aprovação de doações, organiza o catálogo digital de produtos com fotos e preços acessíveis, permite o agendamento/reserva de peças online e notifica usuários e administradores via e-mail e mensagens automatizadas de WhatsApp.
- **Público-alvo:** Doador de peças, comunidade local (compradores/beneficiários dos itens do brechó) e a equipe administrativa e voluntária da Cáritas Bauru.

---

## 🚀 Tecnologias e Arquitetura
- **Linguagem / Framework Backend:** PHP / Laravel
- **Banco de Dados:** PostgreSQL
- **Frontend / Estilização:** Blade / TailwindCSS / JavaScript
- **Hospedagem / Infra:** Docker / Nginx

---

## 👥 Equipe e Papéis
| Integrante | Papel no Projeto | E-mail |
| :--- | :--- | :--- |
| **Guilherme Albaricci Alves** | Product Owner (PO) | guilherme.a.alves@unesp.br |
| **Maria Eduarda Batista Ribeiro** | Scrum Master | maria.eb.ribeiro@unesp.br |
| **Nicole Corrêa Colasso** | Tech Leader | nicole.colasso@unesp.br |
| **Ana Carolina Tiritan** | Desenvolvedor | ana.c.tirintan@unesp.br |
| **Lucas Dalben Borges** | Desenvolvedor | lucas.dalben@unesp.br |
| **Matheus Batista de Souza** | Desenvolvedor | matheus.b.souza@unesp.br |

**Orientador(a):** Prof. Marcelo Peres Cabello

---

## ⚙️ Como Executar Localmente

```bash
# 1. Clone o repositório principal
git clone https://github.com/seu-usuario/INI3A-EQ4.git

# 2. Acesse a pasta onde o projeto Laravel está localizado
cd INI3A-EQ4/src/Brecharme

# 3. Instale as dependências do PHP e do Node.js
composer install
npm install

# 4. Configure o arquivo de ambiente
cp .env.example .env
php artisan key:generate

# 5. Configure suas credenciais no arquivo .env:
# - Ajuste as credenciais do PostgreSQL (DB_DATABASE, DB_USERNAME, DB_PASSWORD)
# - Insira a chave/senha de e-mail SMTP (Gmail)
# - Configure as chaves do Google OAuth (GOOGLE_CLIENT_ID e GOOGLE_CLIENT_SECRET)
# - Insira a URL e o número da API do WhatsApp (WHATSAPP_API_URL, WHATSAPP_NUMERO)

# 6. Crie e popule o banco de dados com as tabelas e dados de teste (Seeders)
php artisan migrate --seed

# 7. Crie o link simbólico para as imagens do projeto funcionarem
php artisan storage:link

# 8. Compile os arquivos estáticos e inicie o servidor local
npm run dev
php artisan serve
```
