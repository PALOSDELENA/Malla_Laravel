# MallaPalos - Laravel + Docker

Este proyecto Laravel está configurado para ejecutarse en un entorno de desarrollo Dockerizado con soporte para Nginx, PHP-FPM, MySQL, Redis y un contenedor de desarrollo interactivo ("workspace").

---

## 📦 Requisitos

- [Docker](https://www.docker.com/products/docker-desktop)
- [Docker Compose](https://docs.docker.com/compose/install/)
- [Composer](https://getcomposer.org/)
- [Node.js & npm](https://nodejs.org/)

---

## 🚀 Instalación y ejecución

1. Clonar el repositorio:

```bash
https://github.com/PALOSDELENA/Malla_Laravel.git
cd raiz

2. Crear el archivo .env:

```bash
cp .env.example .env

3. Instalar dependencias de Laravel y frontend:

```bash
composer install
npm install && npm run dev

4. Levantar los contenedores con Docker:

```bash
docker compose -f compose.dev.yml up --build -d

5. Ejecutar las migraciones:

```bash
docker exec -it mallapalos-workspace-1 bash
php artisan migrate

🌐 Acceso
Una vez levantado el entorno, accede a tu aplicación Laravel desde:
http://localhost

🧼 Comandos útiles
- Apagar los contenedores:
    docker compose -f compose.dev.yml down

- Ver logs:
    docker compose logs -f

- Acceder al contenedor de desarrollo:
    docker exec -it mallapalos-workspace-1 bash

⚠️ Notas
- Las credenciales de base de datos están definidas en tu .env, y se inyectan también en los contenedores.

- Corregir problemas con rutas:
    php artisan route:clear
    php artisan config:clear
    php artisan optimize:clear


      MYSQL_DATABASE: app
      MYSQL_USER: laravel
      MYSQL_PASSWORD: secret
      MYSQL_ROOT_PASSWORD: root
