#!/usr/bin/env bash
##############################################################
# Script de despliegue en producción – SistemaEncomienda
# Ejecutar desde el servidor como usuario www-data o con sudo.
# Uso:  bash deploy/scripts/deploy.sh
##############################################################

set -euo pipefail

APP_DIR="/var/www/sistemaencomienda"
SRC_DIR="${APP_DIR}/src"
BRANCH="main"

echo "========================================================"
echo " Sistema de Encomiendas – Despliegue a Producción"
echo " $(date '+%Y-%m-%d %H:%M:%S')"
echo "========================================================"

# 1. Poner en modo mantenimiento
echo "[1/9] Activando modo mantenimiento..."
php "$SRC_DIR/artisan" down --refresh=15 --secret="despliegue-$(date +%s)"

# 2. Actualizar el código fuente
echo "[2/9] Descargando última versión desde GitHub (rama ${BRANCH})..."
git -C "$APP_DIR" fetch origin
git -C "$APP_DIR" reset --hard origin/${BRANCH}

# 3. Instalar dependencias PHP (sin dev)
echo "[3/9] Instalando dependencias Composer..."
composer install --no-dev --optimize-autoloader --no-interaction -d "$SRC_DIR"

# 4. Instalar dependencias JS y compilar assets
echo "[4/9] Compilando assets con Vite..."
cd "$SRC_DIR" && npm ci --omit=dev && npm run build

# 5. Ejecutar migraciones
echo "[5/9] Ejecutando migraciones de base de datos..."
php "$SRC_DIR/artisan" migrate --force

# 6. Limpiar y reconstruir caché
echo "[6/9] Optimizando configuración y rutas..."
php "$SRC_DIR/artisan" config:cache
php "$SRC_DIR/artisan" route:cache
php "$SRC_DIR/artisan" view:cache
php "$SRC_DIR/artisan" event:cache

# 7. Reiniciar queue workers
echo "[7/9] Reiniciando workers de cola..."
php "$SRC_DIR/artisan" queue:restart

# 8. Ajustar permisos
echo "[8/9] Ajustando permisos..."
chown -R www-data:www-data "$SRC_DIR/storage" "$SRC_DIR/bootstrap/cache"
chmod -R 775 "$SRC_DIR/storage" "$SRC_DIR/bootstrap/cache"

# 9. Desactivar modo mantenimiento
echo "[9/9] Desactivando modo mantenimiento..."
php "$SRC_DIR/artisan" up

echo "========================================================"
echo " ✓ Despliegue completado exitosamente."
echo " URL: https://tudominio.com"
echo "========================================================"
