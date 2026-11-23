-- ##############################################################
-- Setup de PostgreSQL para Producción – SistemaEncomienda
-- Ejecutar como superusuario (postgres):
--   psql -U postgres -f deploy/postgres/setup_production.sql
-- ##############################################################

-- 1. Crear usuario dedicado de producción (NO usar el superusuario "postgres")
CREATE USER encomiendas_user WITH
    PASSWORD 'CAMBIAR_POR_CONTRASEÑA_SEGURA'
    NOSUPERUSER
    NOCREATEDB
    NOCREATEROLE
    LOGIN;

-- 2. Crear la base de datos con el nuevo usuario como dueño
CREATE DATABASE encomiendabd
    WITH OWNER = encomiendas_user
    ENCODING = 'UTF8'
    LC_COLLATE = 'es_PY.UTF-8'
    LC_CTYPE = 'es_PY.UTF-8'
    TEMPLATE = template0;

-- Si el locale es_PY no está disponible, usar:
-- LC_COLLATE = 'en_US.UTF-8'
-- LC_CTYPE   = 'en_US.UTF-8'

-- 3. Conceder todos los privilegios sobre la BD
GRANT ALL PRIVILEGES ON DATABASE encomiendabd TO encomiendas_user;

-- 4. Conectar a la base de datos y establecer permisos de esquema
\c encomiendabd
GRANT ALL ON SCHEMA public TO encomiendas_user;

-- 5. Instrucciones adicionales de seguridad (pg_hba.conf)
-- Asegurarse que pg_hba.conf tenga:
-- host  encomiendabd  encomiendas_user  127.0.0.1/32  scram-sha-256
