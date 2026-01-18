#!/bin/bash
set -e

DOMAIN="perfas.com.tr"
EMAIL="admin@perfas.com.tr"
DOCKER_PORT=8080
NGINX_SITE="/etc/nginx/sites-available/$DOMAIN"

echo "[1/6] Certbot kontrol ediliyor..."
sudo apt update
sudo apt install -y certbot

echo "[2/6] 443 uzerinden SSL aliniyor (tls-alpn-01)..."
sudo certbot certonly \
  --standalone \
  --tls-alpn-01 \
  --agree-tos \
  --no-eff-email \
  -m "$EMAIL" \
  -d "$DOMAIN" \
  -d "www.$DOMAIN"

echo "[3/6] Nginx 443 reverse proxy yaziliyor..."
sudo tee $NGINX_SITE > /dev/null <<EOF
server {
    listen 443 ssl http2;
    server_name $DOMAIN www.$DOMAIN;

    ssl_certificate /etc/letsencrypt/live/$DOMAIN/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/$DOMAIN/privkey.pem;

    location / {
        proxy_pass http://127.0.0.1:$DOCKER_PORT;
        proxy_set_header Host \$host;
        proxy_set_header X-Real-IP \$remote_addr;
        proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto https;
    }
}
EOF

echo "[4/6] Nginx config aktif ediliyor..."
sudo ln -sf $NGINX_SITE /etc/nginx/sites-enabled/

echo "[5/6] Nginx test ve reload..."
sudo nginx -t
sudo systemctl reload nginx

echo "[6/6] SSL yenileme cron ekleniyor..."
sudo crontab -l 2>/dev/null | grep -v certbot > /tmp/cron.tmp || true
echo "0 3 * * * certbot renew --standalone --tls-alpn-01 --quiet" >> /tmp/cron.tmp
sudo crontab /tmp/cron.tmp
rm /tmp/cron.tmp

echo "======================================"
echo "KURULUM BASARIYLA TAMAMLANDI"
echo "Site: https://$DOMAIN"
echo "80.port KULLANILMADI"
echo "Docker: $DOCKER_PORT"
echo "======================================"

