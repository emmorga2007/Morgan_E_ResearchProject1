<?php

namespace XpBar;

use Exception;
use PDO;
use PDOException;

/**
 * A simple Database class that allows you to connect and query a MySQL database.
 * 
 * @package XpBar
 * @author Nicholas Ireland <n_ireland@fanshaweonline.ca>
 * 
 * Some related documentation / readings:
 * @see https://www.php.net/manual/en/book.pdo.php
 * @see https://www.php.net/manual/en/pdo.prepare.php
 * @see https://www.php.net/manual/en/pdostatement.execute.php
 * @see https://www.php.net/manual/en/pdostatement.fetchall.php
 */
class Database
{
    /**
     * The PDO instance for this class once initialized.
     * 
     * @var PDO
     */
    protected $pdo;

    /**
     * The username for the MySQL server we're trying to connect to.
     * 
     * @var string
     */
    private $username;

    /**
     * The password for the MySQL server we're trying to connect to.
     * 
     * @var string
     */
    private $password;

    /**
     * The host of the database we're trying to connect to.
     * 
     * @var string
     */
    private $host;

    /**
     * The name of the database we're trying to connect to.
     * 
     * @var string
     */
    private $database;

    /**
     * The options we're going to pass to PDO.
     * 
     * @var string
     */
    private $options;

    /**
     * Build up the class so we can connect to the database.
     * We'll pass in the username / password / host / database, with optional options, to set up our class.
     * We'll then instantiate PDO using our getPDO() method and set it on the class to use later.
     * 
     * @param string $username 
     * @param string $password 
     * @param string $host 
     * @param string $database 
     * @param array $options 
     * @return void 
     */
    public function __construct(
        string $username,
        string $password,
        string $host,
        string $database,
        array $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // set the error mode to throw exceptions, instead of not doing anything (the default)
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // always fetch the associate array (key => value) instead of the numeric one
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    ) {
        $this->username = $username;
        $this->password = $password;
        $this->host = $host;
        $this->database = $database;
        $this->options = $options;

        $this->pdo = $this->getPDO();
    }

    /**
     * A very basic implementation of a SELECT query.
     * This basic implentation still hides away the PDO stuff, but allows for a lot of flexibility with wheres, joins, etc.
     * 
     * @param string $query - the query to execute
     * @param array $bindings - the bindings
     * @return array - the results
     * @throws Exception - we're manually throwing one of these if the query is not a select query
     * @throws PDOException - this could be thrown from ->execute()
     */
    public function select(string $query, array $bindings = []): array
    {
        if (! str_contains($query, "SELECT")) {
            throw new \Exception("You're using the wrong method - this is for SELECT queries only!");
        }

        $prepared = $this->pdo->prepare($query);
        $success = $prepared->execute($bindings);

        return $prepared->fetchAll();
    }

    /**
     * A very basic implementation of a SELECT query, but one that only returns a single row (or is intended to).
     * This basic implentation still hides away the PDO stuff, but allows for a lot of flexibility with wheres, joins, etc.
     * 
     * @param string $query - the query to execute
     * @param array $bindings - the bindings
     * @return array - the results
     * @throws Exception - we're manually throwing one of these if the query is not a select query
     * @throws PDOException - this could be thrown from ->execute()
     */
    public function selectOne(string $query, array $bindings = []): array
    {
        if (! str_contains($query, "SELECT") || ! str_contains($query, "LIMIT 1")) {
            throw new \Exception("You're using the wrong method - this is for SELECT queries for a single record (LIMIT 1) only!");
        }

        $prepared = $this->pdo->prepare($query);
        $success = $prepared->execute($bindings);

        return $prepared->fetch();
    }

    /**
     * A very basic implementation of a DELETE query, with a quick check to confirm the passed query actually has DELETE FROM in it.
     * This basic implentation still hides away the PDO stuff, but allows for a lot of flexibility with wheres, etc.
     * 
     * @param string $query 
     * @param array $bindings 
     * @return bool 
     * @throws Exception 
     * @throws PDOException 
     */
    public function delete(string $query, array $bindings): bool
    {
        if (! str_contains($query, "DELETE FROM")) {
            throw new \Exception("You're using the wrong method - this is for DELETE queries only!");
        }

        $prepared = $this->pdo->prepare($query);
        $success = $prepared->execute($bindings);

        return $success;
    }

    /**
     * Insert something into the provided table.
     * This is a more advanced implementation, where we reduce even more of the knowledge needed for the developer to insert something into a table.
     * 
     * Compared to select() above, this is much more abstracted; since in select(), we need to pass in an actual query, and here, we generate one
     * for the developer, so they don't have to.
     * 
     * They only need to pass in the name of the table ($table) and the array of key value pairs they want to insert into that table, ie.
     * [
     *    'first_name' => 'name',
     *    'last_name' => 'lastname',
     *    'email' => 'email@email.com',
     *    'countries_id' => 2,
     * ]
     * 
     * @param string $table - the name of the table we want to insert into
     * @param array $itemToInsert - the key / value pairs for insertion
     * @return bool - whether the insert was successful or not
     * @throws PDOException 
     * 
     * Some related documentation you may find helpful:
     * @see https://www.php.net/manual/en/function.array-keys.php
     * @see https://www.php.net/manual/en/function.array-map.php
     * @see https://www.php.net/manual/en/function.implode.php
     */
    public function insert(string $table, array $itemToInsert): bool
    {
        $tableName = '`' . $table . '`'; // backtick the tablename
        $columns = array_keys($itemToInsert); // get the keys of the object we're passing in.

        // map over each of the columns, and add a : to the front, ie. :first_name, so we can use them as bindings.
        $mappedBindings = array_map(function ($column) {
            return ':' . $column;
        }, $columns);

        // join all the values of these arrays into one string, separated by "," commas.
        // https://www.php.net/manual/en/function.implode.php
        $columnsString = implode(", ",  $columns);
        $bindingsString = implode(", ", $mappedBindings);

        // finally, we create the query as a string here.
        $query = "INSERT INTO $tableName ($columnsString) VALUES ($bindingsString)";

        // prepare the query
        $prepared = $this->pdo->prepare($query);
        // execute the query, and get whether it was successful
        $success = $prepared->execute($itemToInsert);

        return $success;
    }

    /**
     * Another more complex solution, where the user passes in the table, array of columns to update, and the conditions (wheres) they want to constrain their query
     * @param string $table 
     * @param array $thingsToUpdate 
     * @param array $wheres 
     * @return bool 
     * @throws PDOException 
     */
    public function update(string $table, array $thingsToUpdate, array $wheres): bool
    {
        $tableName = '`' . $table . '`'; // backtick the tablename
        $columns = array_keys($thingsToUpdate); // get the keys of the object we're passing in.

        // map over each of the provided key / value pairs from the $thingsToUpdate, and set the up like
        // key = :key
        // for binding later
        $mappedBindings = array_map(function ($column) {
            return "`" . $column . "` = :" . $column;
        }, $columns);

        // join all the values of these arrays into one string, separated by "," commas.
        // https://www.php.net/manual/en/function.implode.php
        $bindingsString = implode(", ", $mappedBindings);

        // same process as above - get the keys from the wheres array:
        $wheresKeys = array_keys($wheres);

        // map the keys into key = :key so we can use them in the wheres
        $mappedWheres = array_map(function ($column) {
            return "`" . $column . "` = :" . $column;
        }, $wheresKeys);

        /**
         * turn the array into a string, but this time, concatenate them with " AND ", so something like
         * [
         *    'id' => 2,
         *    'email' => 'email@email.com'
         * ]
         * becomes:
         * id = :id AND email = :email
         * 
         * so we can bind into those later.
         */
        $wheresString = implode(" AND ", $mappedWheres);

        // finally, we create the query as a string here.
        $query = "UPDATE $tableName SET $bindingsString WHERE $wheresString";

        // prepare the query
        $prepared = $this->pdo->prepare($query);

        // add the ID to the bindings so we can do the where
        $bindings = array_merge($thingsToUpdate, $wheres);

        // execute the query, and get whether it was successful
        $success = $prepared->execute($bindings);

        return $success;
    }

    /**
     * Get a the PDO instance we will set on the class by instantiating a new instance of PDO.
     * We use getDSN() to get the DSN string, and then pass in the passed username, password and options.
     * 
     * @return PDO 
     */
    private function getPDO(): PDO
    {
        return new PDO($this->getDSN(), $this->username, $this->password, $this->options);
    }

    /**
     * Get the DSN string from the class attributes.
     * 
     * @return string 
     */
    private function getDSN(): string
    {
        return 'mysql:host=' . $this->host .';dbname=' . $this->database;
    }
}