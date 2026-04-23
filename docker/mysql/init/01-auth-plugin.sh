#!/bin/sh
set -e

mysql -uroot -p"${MYSQL_ROOT_PASSWORD}" <<SQL
ALTER USER '${MYSQL_USER}'@'%' IDENTIFIED WITH mysql_native_password BY '${MYSQL_PASSWORD}';
FLUSH PRIVILEGES;
SQL
