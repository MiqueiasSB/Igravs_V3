#!/bin/sh
# Entrypoint do container.
# Detecta o driver do banco antes de esperar por ele.
# SQLite não precisa de espera — o arquivo é local.

set -e

# Lê o driver configurado. Se vazio, assume mysql.
DB_DRIVER="${DB_CONNECTION:-mysql}"

echo "==> Driver do banco: $DB_DRIVER"

# Só espera o banco se for um servidor externo (mysql, pgsql).
# SQLite é um arquivo local — não há o que esperar.
if [ "$DB_DRIVER" = "mysql" ] || [ "$DB_DRIVER" = "pgsql" ]; then
    echo "==> Aguardando o banco de dados..."
    i=0
    until php -r "
        \$driver = getenv('DB_CONNECTION');
        \$host   = getenv('DB_HOST');
        \$port   = getenv('DB_PORT') ?: (\$driver === 'pgsql' ? '5432' : '3306');
        \$db     = getenv('DB_DATABASE');
        \$user   = getenv('DB_USERNAME');
        \$pass   = getenv('DB_PASSWORD');
        new PDO(\"\$driver:host=\$host;port=\$port;dbname=\$db\", \$user, \$pass);
    " 2>/dev/null; do
        i=$((i+1))
        if [ $i -ge 30 ]; then
            echo "==> Banco não respondeu em 30s. Abortando."
            exit 1
        fi
        echo "    tentativa $i/30..."
        sleep 1
    done
    echo "==> Banco disponível."
else
    echo "==> SQLite detectado — pulando espera do banco."

    # Garante que o arquivo do SQLite existe.
    DB_FILE="${DB_DATABASE:-/var/www/html/database/database.sqlite}"
    DB_DIR="$(dirname "$DB_FILE")"

    # Cria o arquivo se não existir.
    if [ ! -f "$DB_FILE" ]; then
        echo "==> Criando arquivo SQLite em $DB_FILE"
        touch "$DB_FILE"
    fi

    # O SQLite precisa escrever arquivos temporários (-journal, -wal)
    # na MESMA pasta do banco. Sem permissão na pasta, ele falha.
    chown -R www-data:www-data "$DB_DIR"
    chmod 775 "$DB_DIR"

    # Permissão de leitura e escrita no arquivo do banco.
    chown www-data:www-data "$DB_FILE"
    chmod 664 "$DB_FILE"
fi

echo "==> Rodando migrations..."
# Não aborta se falhar — deixa o container subir para você debugar.
php artisan migrate --force || echo "AVISO: migrate falhou, continuando..."

echo "==> Limpando caches antigos..."
php artisan optimize:clear

echo "==> Gerando caches de produção..."
php artisan optimize

echo "==> Ajustando permissões..."
chown -R www-data:www-data storage bootstrap/cache

echo "==> Iniciando serviços..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf