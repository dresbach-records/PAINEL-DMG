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
- Serviço de transporte (`PCTMailer`) + renderização (`TemplateRenderer`)
- Job assíncrono (`SendMailJob`)
- Notificações de eventos do SaaS (`FiliacaoAprovada`, `DiretorioAtivado`, `BoasVindas`)
- Dashboard básico (`mail-dashboard.blade.php`)

## Estrutura de dados
- `mail_logs`: trilha de auditoria de mensagens
- `mail_templates`: templates reutilizáveis
- `mail_campaigns`: campanhas e segmentação
- `mail_queues`: fila de envio funcional

## Configuração SMTP local (VPS)
1. Instale Postfix na VPS (Ubuntu):
   ```bash
   sudo apt update
   sudo apt install postfix -y
   ```
2. Configure `/etc/postfix/main.cf` (hostname, mydomain, relay restrictions).
3. Garanta DNS com **A + MX + SPF + DKIM + DMARC** para entregabilidade.
4. No `.env` do Laravel:
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=127.0.0.1
   MAIL_PORT=25
   MAIL_ENCRYPTION=null
   MAIL_FROM_ADDRESS=nao-responder@seu-dominio.com
   MAIL_FROM_NAME="PCT Mail"

   PCT_MAIL_TRANSPORT=smtp
   PCT_MAIL_HOST=127.0.0.1
   PCT_MAIL_PORT=25
   PCT_MAIL_QUEUE_CONNECTION=redis
   PCT_MAIL_QUEUE_NAME=pct-mail
   ```
5. Execute worker:
   ```bash
   php artisan queue:work --queue=pct-mail
   ```

## Integração SaaS
- Dispare `SendMailJob` em aprovações de filiação, ativação de diretórios e boas-vindas.
- Use as notificações já criadas em listeners/observers dos eventos de negócio.

## Rotas
As rotas estão em `routes/mail.php` com middleware `auth:sanctum` + policies.
