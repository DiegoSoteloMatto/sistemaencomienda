##############################################################
# README de Despliegue en Producción – SistemaEncomienda
##############################################################

## Requisitos del Servidor

| Componente       | Versión mínima |
|-----------------|----------------|
| Ubuntu / Debian  | 22.04 LTS      |
| PHP              | 8.2+           |
| PHP Extensions   | pgsql, mbstring, xml, curl, zip, gd |
| Nginx            | 1.24+          |
| PostgreSQL       | 15+            |
| Node.js          | 20 LTS         |
| Composer         | 2.x            |
| Certbot (SSL)    | Recomendado    |

---

## Paso a Paso

### 1. Preparar el servidor

```bash
# Actualizar paquetes
sudo apt update && sudo apt upgrade -y

# Instalar dependencias
sudo apt install -y nginx postgresql php8.3 php8.3-fpm \
  php8.3-pgsql php8.3-mbstring php8.3-xml php8.3-curl \
  php8.3-zip php8.3-gd php8.3-bcmath php8.3-intl \
  supervisor git unzip curl

# Instalar Node.js 20
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo bash -
sudo apt install -y nodejs

# Instalar Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### 2. Configurar PostgreSQL

```bash
# Ejecutar script de configuración
sudo -u postgres psql -f deploy/postgres/setup_production.sql
```

> ⚠️ **Cambiar la contraseña** en `setup_production.sql` antes de ejecutarlo.

### 3. Clonar el repositorio

```bash
sudo mkdir -p /var/www/sistemaencomienda
sudo chown www-data:www-data /var/www/sistemaencomienda
cd /var/www/sistemaencomienda
sudo -u www-data git clone https://github.com/DiegoSoteloMatto/sistemaencomienda.git .
```

### 4. Configurar variables de entorno

```bash
cp deploy/env.production.example src/.env
nano src/.env   # Completar APP_KEY, DB_PASSWORD, etc.

# Generar la clave de aplicación
php src/artisan key:generate
```

### 5. Instalar dependencias y compilar

```bash
cd src
composer install --no-dev --optimize-autoloader
npm ci --omit=dev && npm run build
```

### 6. Ejecutar migraciones y seeders

```bash
php artisan migrate --force
php artisan db:seed --class=RoleSeeder --force
```

### 7. Ajustar permisos

```bash
chown -R www-data:www-data /var/www/sistemaencomienda/src/storage
chown -R www-data:www-data /var/www/sistemaencomienda/src/bootstrap/cache
chmod -R 775 /var/www/sistemaencomienda/src/storage
chmod -R 775 /var/www/sistemaencomienda/src/bootstrap/cache
```

### 8. Configurar Nginx

```bash
sudo cp deploy/nginx/encomiendas.conf /etc/nginx/sites-available/encomiendas
sudo ln -s /etc/nginx/sites-available/encomiendas /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
```

### 9. Configurar SSL con Let's Encrypt

```bash
sudo apt install certbot python3-certbot-nginx -y
sudo certbot --nginx -d tudominio.com -d www.tudominio.com
```

### 10. Configurar Supervisor (Queue Worker)

```bash
sudo cp deploy/supervisor/encomiendas-worker.conf /etc/supervisor/conf.d/
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start encomiendas-worker:*
```

### 11. Configurar respaldos automáticos

```bash
chmod +x deploy/scripts/backup_db.sh

# Agregar al crontab (como root o www-data)
crontab -e
# Añadir la línea:
# 0 2 * * * /var/www/sistemaencomienda/deploy/scripts/backup_db.sh >> /var/log/encomiendas_backup.log 2>&1
```

### 12. Optimizar Laravel para producción

```bash
cd /var/www/sistemaencomienda/src
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

---

## Despliegues futuros (actualización)

```bash
bash /var/www/sistemaencomienda/deploy/scripts/deploy.sh
```

---

## Checklist de Verificación Post-Despliegue

- [ ] La URL `https://tudominio.com` carga el portal de bienvenida
- [ ] El login funciona con `admin@encomiendas.com`
- [ ] Se puede registrar una nueva encomienda y verificar el Tracking ID
- [ ] El portal de trazabilidad pública `/tracking` funciona sin login
- [ ] Los reportes se generan y exportan correctamente
- [ ] El certificado SSL es válido (candado verde en el navegador)
- [ ] Los respaldos se ejecutan correctamente (`bash deploy/scripts/backup_db.sh`)
