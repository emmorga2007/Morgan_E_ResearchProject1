<?php

use XpBar\Database;

require dirname(__DIR__) . '/vendor/autoload.php';

// Windows
$host = '127.0.0.1';
$username = 'root';
$password = '';
$databaseName = 'my_first_database';

// OSX / Mac
// $host = 'localhost';
// $username = 'root';
// $password = 'root';
// $databaseName = 'my_first_database';

$database = new Database($username, $password, $host, $databaseName);

// Get a list of users
$users = $database->select("SELECT * FROM `users` ORDER BY `id` ASC");

// Update a particular user
$updatingUserWasSuccessful = $database->update('users', [
    'first_name' => 'Emily',
    'last_name' => 'Morgan',
    'email' => 'emily189morgan@hotmail.com',
    'countries_id' => 1
], [
    'id' => 8
]);

// insert a new user
$creatingUserWasSuccessful = $database->insert('users', [
    'first_name' => 'Emily',
    'last_name' => 'Morgan',
    'email' => 'emily189morgan@hotmail.com',
    'countries_id' => 1
]);

$deletingUserWasSuccessful = $database->delete("DELETE FROM `users` WHERE `id` = :id", ['id' => 3]);

$user8 = $database->selectOne("SELECT * FROM `users` WHERE `id` = :id LIMIT 1", ['id' => 8]);

// dd($users, $user8, $updatingUserWasSuccessful, $creatingUserWasSuccessful, $deletingUserWasSuccessful);


// echo "<pre>";
// var_dump($user8);
// echo "</pre>";

// echo "<pre>";
// var_dump($user8);
// echo "</pre>";

// echo "<pre>";
// var_dump($user8);
// echo "</pre>";

// ///

// echo "<pre>";
// var_dump($user8);
// var_dump($user8);
// var_dump($user8);
// var_dump($user8);
// var_dump($user8);
// echo "</pre>";


dump(true);
// dd(true);
// dump(true); die;

dump(true);



dump("hello!");