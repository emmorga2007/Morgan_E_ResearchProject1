<?php

$host = 'localhost';
$user = 'root';
$password = '';
$database = 'my_database';

$dsn = 'mysql:host=' . $host . ';dbname=' . $database;

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

$pdo = new PDO($dsn, $user, $password, $options);

$query = $pdo->query('SELECT * FROM `users`');
//$firstResult = $query->fetch();

$query2 = $pdo->query('SELECT * FROM `users`');
//$results = $query2->fetchAll();

$users = $pdo->query('SELECT * FROM `users`')->fetchAll();
var_dump($users);


$prepared = $pdo->prepare('SELECT * FROM `users` WHERE `country_id` = :country_id');
$prepared->execute([
    'country_id' => 2
]);
$usersFromAmerica = $prepared->fetchAll();


// create a user

$preparedCreateUser = $this->connect()->prepare(
    'INSERT INTO `users` (
        `id`, `first_name`, `last_name`, `email`
    ) VALUES (
        :first_name, :last_name, :email
    )'
);

$success = $preparedCreateUser->execute([
    'first_name' => $first_name,
    'last_name' => $last_name,
    'email' => $email,
]);

var_dump($success);
