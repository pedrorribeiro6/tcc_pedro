
<?php

if (
    isset($_SERVER['HTTP_HOST']) &&
    (
        $_SERVER['HTTP_HOST'] === 'localhost' ||
        str_starts_with($_SERVER['HTTP_HOST'], 'localhost:')
    )
) {
    // CONFIGURAÇÃO LOCAL — XAMPP
    $host = 'localhost';
    $port = '3307';
    $dbname = 'banco_tcc';
    $username = 'root';
    $password = '';

} else {
    // CONFIGURAÇÃO ONLINE — INFINITYFREE
    $host = 'sql206.infinityfree.com';
    $port = '3306';
    $dbname = 'if0_43133622_banco_tcc';

    $username = 'if0_43133622';
    $password = 'D561s5uTdC7zE';
}

$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $username, $password);

    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    $pdo->setAttribute(
        PDO::ATTR_DEFAULT_FETCH_MODE,
        PDO::FETCH_ASSOC
    );

} catch (PDOException $e) {
    error_log($e->getMessage());
    exit('Não foi possível conectar ao banco de dados.');
}
