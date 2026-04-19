# PCT Mail Transport System (Laravel + Vite)

Implementação base de um **Mail Transport System próprio** para VPS, sem dependência de provedores pagos.

## Stack
- Laravel 11 / PHP 8.2
- Blade + Alpine.js + Vite
- Filas com Redis/Database
- SMTP local (Postfix) ou sendmail

## Módulos implementados
- API de envio individual e logs (`MailController`)
- CRUD de templates (`TemplateController`)
- CRUD + disparo de campanhas (`CampaignController`)
- Gestão de caixas por diretório (`MailboxController`)
- Recebimento de e-mails inbound para caixa interna (`InboundMailController`)
- Serviço de transporte (`PCTMailer`) + renderização (`TemplateRenderer`)
- Job assíncrono (`SendMailJob`)
- Notificações de eventos do SaaS (`FiliacaoAprovada`, `DiretorioAtivado`, `BoasVindas`)
- Dashboard básico (`mail-dashboard.blade.php`)

## Estrutura de dados
- `mail_logs`: trilha de auditoria de mensagens enviadas
- `mail_templates`: templates reutilizáveis
- `mail_campaigns`: campanhas e segmentação
- `mail_queues`: fila de envio funcional
- `directory_mailboxes`: e-mail de cada diretório + caixas institucionais PCT
- `inbound_messages`: mensagens recebidas por cada caixa

## Caixas de e-mail por diretório
Quando um diretório é cadastrado no SaaS, o sistema pode provisionar automaticamente uma caixa, por exemplo:
- `diretorio-sp@pct.social.br`
- `diretorio-rj@pct.social.br`

Também são criadas caixas institucionais:
- `contato@pct.social.br`
- `suporte@pct.social.br`
- `financeiro@pct.social.br`

## Fluxo de recebimento de e-mail
1. O Postfix recebe o e-mail do domínio `pct.social.br`.
2. Postfix encaminha payload para `POST /mail/inbound` (pipe/webhook).
3. `InboundMailController` valida destinatário e salva em `inbound_messages`.
4. A caixa do diretório consulta mensagens em `GET /mail/mailboxes/{mailbox}/inbox`.

## Configuração para VPS AlmaLinux 9 (recomendado para seu ambiente)
Como sua VPS é AlmaLinux 9, use `dnf` (não `apt`).

### 1) Setup rápido
```bash
sudo bash scripts/almalinux9/setup-postfix.sh pct.social.br
```

Esse script:
- instala e habilita Postfix
- aplica baseline de SMTP local
- libera `smtp` no firewalld
- aplica ajuste comum de SELinux para tráfego da aplicação

### 2) Ajustes manuais importantes
- Edite `/etc/postfix/main.cf` para sua política final de relay.
- Se usar TLS com certificado público, configure caminhos corretos de cert/key.
- Garanta DNS com **A + MX + SPF + DKIM + DMARC**.

### 3) .env do Laravel
```env
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=25
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=nao-responder@pct.social.br
MAIL_FROM_NAME="PCT Mail"

PCT_MAIL_TRANSPORT=smtp
PCT_MAIL_HOST=127.0.0.1
PCT_MAIL_PORT=25
PCT_MAIL_QUEUE_CONNECTION=redis
PCT_MAIL_QUEUE_NAME=pct-mail
PCT_MAILBOX_DOMAIN=pct.social.br
```

### 4) Worker de fila
```bash
php artisan queue:work --queue=pct-mail
```

### 5) Inbound no Postfix
Para receber e-mails em `contato@pct.social.br` e caixas de diretório, configure alias/pipe no Postfix para enviar os dados ao endpoint:
- `POST /mail/inbound`

## Rotas
As rotas estão em `routes/mail.php` com middleware `auth:sanctum` + policies.
