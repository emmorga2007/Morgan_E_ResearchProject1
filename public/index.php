<?php

// get the class to run its functions
spl_autoload_register(function ($class){
    require_once '../includes/classes/' . $class .  '.php';
});

// get the class to run its functions
$userFunctions = new UserFunctions();

// get all users
$allUsers = $userFunctions->allUsers();
var_dump($allUsers);

// get one user by first name and email
$user = $userFunctions->getUser('Emily', 'emily@gmail.com');
var_dump($user);

// create new user, needs all table items except id
$createUser = $userFunctions->createUser('Emily', 'Morgan', 'emily@gmail.com', '1');
var_dump($createUser);

// delete user with id
$deleteUser = $userFunctions->deleteUser(2);
var_dump($deleteUser);

// update user, needs all table items
$updateUser = $userFunctions->updateUser(4, 'Nick', 'Ireland', 'nick@gmail.com', '1');
var_dump($updateUser);
