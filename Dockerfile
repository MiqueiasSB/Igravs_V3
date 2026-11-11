# ============================================================
# ESTÁGIO 1 — "vendor"
# Instala as dependências PHP com Composer.
# ============================================================
FROM composer:2 AS vendor

WORKDIR /app

# Copia os manifests primeiro para aproveitar o cache do Docker.
COPY composer.json composer.lock ./

# Instala dependências de produção sem rodar scripts (não há .env ainda).
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --no-interaction

# Copia o código e gera o autoload otimizado.
COPY . .
RUN composer dump-autoload --optimize --no-dev


# ============================================================
# ESTÁGIO 2 — "assets"
# Builda o Vue com Vite Plus.
# Usa serversideup/php (PHP 8.3 + glibc) e instala Node 20 por cima.
# ============================================================
FROM serversideup/php:8.3-cli AS assets

# A imagem roda como www-data por padrão.
# Precisamos de root para instalar pacotes com apt-get.
USER root

# Instala o Node 20 via NodeSource.
# ca-certificates, curl e gnupg são pré-requisitos do repositório.
RUN apt-get update && apt-get install -y --no-install-recommends \
        ca-certificates \
        curl \
        gnupg \
    && mkdir -p /etc/apt/keyrings \
    && curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key \
        | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg \
    && echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_20.x nodistro main" \
        > /etc/apt/sources.list.d/nodesource.list \
    && apt-get update && apt-get install -y --no-install-recommends nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /app

# Copia os manifests do Node e instala as dependências.
COPY package.json package-lock.json ./
RUN npm ci

# Copia o código do projeto.
COPY . .

# Copia o vendor/ do estágio 1 — o Wayfinder precisa dele.
COPY --from=vendor /app/vendor ./vendor

# Cria um .env mínimo para o artisan conseguir bootar.
# O Wayfinder só precisa que o app inicialize, não que o banco exista.
RUN cp .env.example .env 2>/dev/null || touch .env
RUN php artisan key:generate --force 2>/dev/null || true

# Builda o Vite Plus. Resultado em public/build/.
RUN npm run build

# ============================================================
# ESTÁGIO 3 — "app"
# Imagem final: PHP 8.3-FPM + Nginx + Supervisor.
# Não tem Node nem Composer — só o resultado dos estágios anteriores.
# ============================================================
FROM php:8.3-fpm-alpine AS app

# Instala Nginx, Supervisor e as extensões PHP do Laravel 13.
RUN apk add --no-cache \
        nginx \
        supervisor \
        curl \
        libpng-dev \
        libzip-dev \
        oniguruma-dev \
        icu-dev \
        $PHPIZE_DEPS \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
        opcache \
    && apk del $PHPIZE_DEPS

WORKDIR /var/www/html

# Copia o código com o vendor já instalado.
COPY --from=vendor /app /var/www/html

# Copia o build do Vite do estágio 2 (sobrescreve o public/build vazio).
COPY --from=assets /app/public/build /var/www/html/public/build

# Copia as configurações de Nginx, Supervisor e o entrypoint.
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh

# Ajusta permissões das pastas que o Laravel escreve.
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]