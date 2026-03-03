#!/bin/bash
# ============================================================
# PPAS MySQL Yedekten Geri Yükleme Scripti
# Mevcut yedekleri listeler ve seçilen yedeği geri yükler
# ============================================================

# --- Ayarlar ---
CONTAINER_NAME="ppas_mysql"
DB_NAME="ppas_db"
DB_USER="root"
DB_PASSWORD="Ppas_Secure_Root_2026_!#"
BACKUP_DIR="/mnt/HC_Volume_104488791/backups/ppas_mysql"

# --- Renkler ---
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
BOLD='\033[1m'
NC='\033[0m' # No Color

# --- Fonksiyonlar ---
log() {
    echo -e "[$(date '+%Y-%m-%d %H:%M:%S')] $1"
}

# --- Yedek dizini kontrol ---
if [ ! -d "${BACKUP_DIR}" ]; then
    echo -e "${RED}HATA: Yedek dizini bulunamadı: ${BACKUP_DIR}${NC}"
    exit 1
fi

# --- Yedekleri listele ---
list_backups() {
    echo ""
    echo -e "${BOLD}╔══════════════════════════════════════════════════════════╗${NC}"
    echo -e "${BOLD}║           PPAS MySQL Yedek Geri Yükleme                 ║${NC}"
    echo -e "${BOLD}╚══════════════════════════════════════════════════════════╝${NC}"
    echo ""
    echo -e "${CYAN}Mevcut yedekler:${NC}"
    echo -e "${CYAN}────────────────────────────────────────────────────────${NC}"

    BACKUPS=($(ls -t "${BACKUP_DIR}"/${DB_NAME}_*.sql.gz 2>/dev/null))

    if [ ${#BACKUPS[@]} -eq 0 ]; then
        echo -e "${RED}Hiç yedek bulunamadı!${NC}"
        exit 1
    fi

    for i in "${!BACKUPS[@]}"; do
        FILE="${BACKUPS[$i]}"
        FILENAME=$(basename "$FILE")
        FILESIZE=$(du -h "$FILE" | cut -f1)
        FILEDATE=$(stat -c '%y' "$FILE" | cut -d'.' -f1)
        NUM=$((i + 1))
        echo -e "  ${GREEN}${NUM})${NC} ${FILENAME}  ${YELLOW}(${FILESIZE})${NC}  ${FILEDATE}"
    done

    echo -e "${CYAN}────────────────────────────────────────────────────────${NC}"
    echo ""
}

# --- Parametre ile doğrudan restore ---
if [ -n "$1" ]; then
    SELECTED_FILE="$1"
    # Eğer tam yol verilmediyse backup dizininde ara
    if [ ! -f "$SELECTED_FILE" ]; then
        SELECTED_FILE="${BACKUP_DIR}/$1"
    fi
    if [ ! -f "$SELECTED_FILE" ]; then
        echo -e "${RED}HATA: Belirtilen yedek dosyası bulunamadı: $1${NC}"
        exit 1
    fi
else
    # --- İnteraktif mod ---
    list_backups

    read -p "Geri yüklenecek yedeğin numarasını girin (çıkış: q): " CHOICE

    if [ "$CHOICE" = "q" ] || [ "$CHOICE" = "Q" ]; then
        echo "İptal edildi."
        exit 0
    fi

    # Sayı kontrolü
    if ! [[ "$CHOICE" =~ ^[0-9]+$ ]] || [ "$CHOICE" -lt 1 ] || [ "$CHOICE" -gt ${#BACKUPS[@]} ]; then
        echo -e "${RED}HATA: Geçersiz seçim!${NC}"
        exit 1
    fi

    SELECTED_FILE="${BACKUPS[$((CHOICE - 1))]}"
fi

SELECTED_NAME=$(basename "$SELECTED_FILE")

# --- Onay ---
echo ""
echo -e "${YELLOW}╔══════════════════════════════════════════════════════════╗${NC}"
echo -e "${YELLOW}║  ⚠  DİKKAT: Bu işlem mevcut veritabanını SİLECEK      ║${NC}"
echo -e "${YELLOW}║     ve seçilen yedekle DEĞİŞTİRECEKTİR!                ║${NC}"
echo -e "${YELLOW}╚══════════════════════════════════════════════════════════╝${NC}"
echo ""
echo -e "Seçilen yedek: ${GREEN}${SELECTED_NAME}${NC}"
echo ""
read -p "Devam etmek istediğinizden emin misiniz? (evet/hayir): " CONFIRM

if [ "$CONFIRM" != "evet" ]; then
    echo "İptal edildi."
    exit 0
fi

# --- Container kontrolü ---
if ! docker ps --format '{{.Names}}' | grep -q "^${CONTAINER_NAME}$"; then
    echo -e "${RED}HATA: '${CONTAINER_NAME}' container'ı çalışmıyor!${NC}"
    exit 1
fi

# --- Geri yükleme başlat ---
echo ""
log "${CYAN}Geri yükleme başlıyor: ${SELECTED_NAME} -> ${DB_NAME}${NC}"

# İlk olarak mevcut veritabanını drop & recreate et
log "Veritabanı sıfırlanıyor..."
docker exec -i "${CONTAINER_NAME}" mysql \
    -u"${DB_USER}" \
    -p"${DB_PASSWORD}" \
    -e "DROP DATABASE IF EXISTS ${DB_NAME}; CREATE DATABASE ${DB_NAME} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null

if [ $? -ne 0 ]; then
    log "${RED}HATA: Veritabanı sıfırlanamadı!${NC}"
    exit 1
fi

# Yedeği geri yükle
log "Yedek geri yükleniyor (bu işlem birkaç dakika sürebilir)..."
zcat "${SELECTED_FILE}" | docker exec -i "${CONTAINER_NAME}" mysql \
    -u"${DB_USER}" \
    -p"${DB_PASSWORD}" \
    "${DB_NAME}" 2>/dev/null

if [ $? -eq 0 ]; then
    # Tablo sayısını kontrol et
    TABLE_COUNT=$(docker exec "${CONTAINER_NAME}" mysql \
        -u"${DB_USER}" \
        -p"${DB_PASSWORD}" \
        -N -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='${DB_NAME}';" 2>/dev/null)

    echo ""
    echo -e "${GREEN}╔══════════════════════════════════════════════════════════╗${NC}"
    echo -e "${GREEN}║  ✓  GERİ YÜKLEME BAŞARILI!                              ║${NC}"
    echo -e "${GREEN}╚══════════════════════════════════════════════════════════╝${NC}"
    echo ""
    log "${GREEN}Yedek: ${SELECTED_NAME}${NC}"
    log "${GREEN}Veritabanı: ${DB_NAME}${NC}"
    log "${GREEN}Tablolar: ${TABLE_COUNT} adet${NC}"
    echo ""
    echo -e "${YELLOW}NOT: Gerekiyorsa Laravel cache'i temizleyin:${NC}"
    echo -e "  docker exec ppas_php php /var/www/artisan cache:clear"
    echo -e "  docker exec ppas_php php /var/www/artisan config:clear"
else
    echo ""
    echo -e "${RED}╔══════════════════════════════════════════════════════════╗${NC}"
    echo -e "${RED}║  ✗  GERİ YÜKLEME BAŞARISIZ!                            ║${NC}"
    echo -e "${RED}╚══════════════════════════════════════════════════════════╝${NC}"
    log "${RED}HATA: Yedek geri yüklenemedi!${NC}"
    exit 1
fi
