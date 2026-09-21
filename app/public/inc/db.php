<?php
/**
 * Соединение с базой данных.
 * Параметры подключения задаются переменными окружения:
 * DB_HOST, DB_NAME, DB_USER, DB_PASSWORD.
 *
 * @return PDO
 */
function db_conn()
{
    static $pdo = null;

    if ($pdo === null) {
        $host = getenv('DB_HOST') !== false ? getenv('DB_HOST') : '127.0.0.1';
        $name = getenv('DB_NAME') !== false ? getenv('DB_NAME') : 'catalog_demo';
        $user = getenv('DB_USER') !== false ? getenv('DB_USER') : 'catalog_user';
        $pass = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : 'catalog_password';

        $pdo = new PDO(
            'mysql:host=' . $host . ';dbname=' . $name,
            $user,
            $pass,
            array(
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_INIT_COMMAND  => 'SET NAMES utf8',
            )
        );
    }

    return $pdo;
}