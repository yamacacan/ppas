#!/bin/bash
# ============================================================
# PPAS MySQL Otomatik Yedekleme Scripti
# Docker container'daki MySQL veritabanını yedekler
# ============================================================
#Ayarlara dokunmayın sadece kendi veritabanı bilgilerinizi giriniz
# --- Ayarlar ---
CONTAINER_NAME="ppas_mysql"
DB_NAME="ppas_db"
DB_USER="root"
DB_PASSWORD="Ppas_Secure_Root_2026_!#"
BACKUP_DIR="/mnt/HC_Volume_104488791/backups/ppas_mysql"
RETENTION_DAYS=3

# --- Tarih/saat damgası ---
TIMESTAMP=$(date +"%Y-%m-%d_%H-%M-%S")
BACKUP_FILE="${BACKUP_DIR}/${DB_NAME}_${TIMESTAMP}.sql.gz"

# --- Log Fonksiyonu ---
log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1"
}

# --- Yedek dizinini oluştur ---
mkdir -p "${BACKUP_DIR}"
if [ $? -ne 0 ]; then
    log "HATA: Yedek dizini oluşturulamadı: ${BACKUP_DIR}"
    exit 1
fi

# --- Container çalışıyor mu kontrol et ---
if ! docker ps --format '{{.Names}}' | grep -q "^${CONTAINER_NAME}$"; then
    log "HATA: '${CONTAINER_NAME}' container'ı çalışmıyor!"
    exit 1
fi

# --- Yedekleme ---
log "Yedekleme başlıyor: ${DB_NAME} -> ${BACKUP_FILE}"

docker exec "${CONTAINER_NAME}" mysqldump \
    -u"${DB_USER}" \
    -p"${DB_PASSWORD}" \
    --single-transaction \
    --routines \
    --triggers \
    --events \
    --quick \
    "${DB_NAME}" 2>/dev/null | gzip > "${BACKUP_FILE}"

# --- Sonuç kontrolü ---
if [ $? -eq 0 ] && [ -s "${BACKUP_FILE}" ]; then
    FILESIZE=$(du -h "${BACKUP_FILE}" | cut -f1)
    log "BAŞARILI: Yedek oluşturuldu (${FILESIZE}): ${BACKUP_FILE}"
else
    log "HATA: Yedekleme başarısız oldu!"
    rm -f "${BACKUP_FILE}"
    exit 1
fi

# --- Eski yedekleri temizle ---
DELETED_COUNT=$(find "${BACKUP_DIR}" -name "${DB_NAME}_*.sql.gz" -type f -mtime +${RETENTION_DAYS} | wc -l)
if [ "${DELETED_COUNT}" -gt 0 ]; then
    find "${BACKUP_DIR}" -name "${DB_NAME}_*.sql.gz" -type f -mtime +${RETENTION_DAYS} -delete
    log "TEMİZLİK: ${DELETED_COUNT} adet eski yedek silindi (>${RETENTION_DAYS} gün)"
else
    log "TEMİZLİK: Silinecek eski yedek yok"
fi

log "İşlem tamamlandı."
