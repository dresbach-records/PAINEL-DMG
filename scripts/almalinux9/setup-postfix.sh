#!/usr/bin/env bash
set -euo pipefail

# AlmaLinux 9 - setup mínimo para SMTP local (Postfix) + entrega para Laravel
# Execute como root: sudo bash scripts/almalinux9/setup-postfix.sh pct.social.br

DOMAIN="${1:-pct.social.br}"
HOSTNAME_FQDN="mail.${DOMAIN}"

sudo dnf -y install postfix cyrus-sasl cyrus-sasl-plain mailx
sudo systemctl enable --now postfix

sudo postconf -e "myhostname = ${HOSTNAME_FQDN}"
sudo postconf -e "mydomain = ${DOMAIN}"
sudo postconf -e "myorigin = \$mydomain"
sudo postconf -e "inet_interfaces = all"
sudo postconf -e "inet_protocols = ipv4"
sudo postconf -e "mydestination = localhost"
sudo postconf -e "mynetworks = 127.0.0.0/8 [::1]/128"
sudo postconf -e "smtpd_recipient_restrictions = permit_mynetworks,reject_unauth_destination"

# TLS baseline (cert path deve ser ajustado para o certificado real da VPS)
sudo postconf -e "smtpd_use_tls = yes"
sudo postconf -e "smtp_use_tls = yes"

# Firewall (porta SMTP)
sudo firewall-cmd --add-service=smtp --permanent || true
sudo firewall-cmd --reload || true

# SELinux: permite Postfix conectando localmente à app/webhook quando necessário
sudo setsebool -P httpd_can_network_connect 1 || true

sudo systemctl restart postfix

cat <<EOF
[OK] Postfix configurado para ${DOMAIN}
- Ajuste DNS: A, MX, SPF, DKIM, DMARC
- Configure .env no Laravel para MAIL_HOST=127.0.0.1 e MAIL_PORT=25
- Para inbound HTTP (POST /mail/inbound), configure alias/pipe no Postfix
EOF
