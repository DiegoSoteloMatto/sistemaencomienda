#!/usr/bin/env bash
##############################################################
# Script de respaldo automático de PostgreSQL
# Uso: añadir al crontab del servidor:
#   0 2 * * * /var/www/sistemaencomienda/deploy/scripts/backup_db.sh >> /var/log/encomiendas_backup.log 2>&1
##############################################################

set -euo pipefail

# ── Configuración ────────────────────────────────────────────
DB_NAME="encomiendabd"
DB_USER="encomiendas_user"          # Usuario de producción (no usar postgres)
BACKUP_DIR="/var/backups/encomiendas"
RETENTION_DAYS=30                   # Días a conservar los respaldos

# ── Crear directorio si no existe ───────────────────────────
mkdir -p "$BACKUP_DIR"

# ── Nombre del archivo con timestamp ────────────────────────
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
FILENAME="${BACKUP_DIR}/${DB_NAME}_${TIMESTAMP}.sql.gz"

# ── Ejecutar volcado y comprimir ────────────────────────────
echo "[$(date '+%Y-%m-%d %H:%M:%S')] Iniciando respaldo de ${DB_NAME}..."
PGPASSWORD="${PGPASSWORD:-}" pg_dump \
    --host=localhost \
    --port=5432 \
    --username="$DB_USER" \
    --format=plain \
    --no-password \
    "$DB_NAME" | gzip > "$FILENAME"

echo "[$(date '+%Y-%m-%d %H:%M:%S')] Respaldo creado: $FILENAME ($(du -sh "$FILENAME" | cut -f1))"

# ── Eliminar respaldos más antiguos que RETENTION_DAYS ──────
find "$BACKUP_DIR" -name "${DB_NAME}_*.sql.gz" -mtime +${RETENTION_DAYS} -delete
echo "[$(date '+%Y-%m-%d %H:%M:%S')] Limpieza completada. Respaldos > ${RETENTION_DAYS} días eliminados."

echo "[$(date '+%Y-%m-%d %H:%M:%S')] ✓ Respaldo finalizado correctamente."
