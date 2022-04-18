<?php

class Database
{
    private $servername; // localhost address
    private $username; // phpmyadmin username
    private $password; // phpmyadmin password
    private $dbname; // phpmyadmin database name

    protected function connect(){
        // put values in the variables
        $this->servername  = 'localhost';
        $this->username  = 'root';
        $this->password = '' ;
        $this->dbname = 'my_database' ;

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        // connect to database - same as in class but with the private variables
        $dsn = 'mysql:host=' . $this->servername . ';dbname=' . $this->dbname;
        $pdo = new PDO($dsn, $this->username, $this->password, $options);

        // send back PDO so the child classes can use it
        return $pdo;
    }
}

