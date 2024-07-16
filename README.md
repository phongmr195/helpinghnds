# HelpingHnds for webapp

- API Backend

## Laravel Framework version 8.4
- Document: https://laravel.com/docs/8.x/installation
## Require

- PHP version: "^7.3|^8.0"
- Composer
## Setup Config

1. App Env
```bash
    cp -f .env.example .env
```
2. App Local
```bash
    composer install
```
3. App Production
```bash
    composer install
```
4. App Key
```bash
    php artisan key:gen
```
## Config Database

- Kết nối với database trong file .env

- Ví dụ:

    DB_CONNECTION=mysql

    DB_HOST=127.0.0.1

    DB_PORT=3306

    DB_DATABASE=helpinghnds

    DB_USERNAME=root

    DB_PASSWORD=root
## CSS adjust
1. Install node_modules

```bash
    npm install
```
2. Local

```bash
    npm run dev
```

3. Production

```bash
    npm run prod
```
