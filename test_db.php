<?php
$host = '127.0.0.1';
$db = 'todo';
$user = 'todo';
$pass = 'helloworld';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int) $e->getCode());
}

//hae rivit
$stmt = $pdo->query('SELECT * FROM tehtava');
while ($row = $stmt->fetch()) {
    echo print_r($row, true) . "\n";
}

//lisää rivi
$data = [
    0 =>  "kuvaus pdo",
    1 =>  "tehtava pdo",
    2 =>  "2020-03-02 18:23:15",
    3 =>  10,
];
try {
    $pdo->prepare("INSERT INTO tehtava(kuvaus,otsikko,lisatty,prioriteetti) VALUES (?,?,?,?)")->execute($data);
} catch (PDOException $e) {
    throw $e;
}

