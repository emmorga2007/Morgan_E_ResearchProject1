<?php

class UserFunctions extends Database
{
    public function allUsers()
    {
        // use $this->connect() from Database class the same as $pdo
        // then just do SQL queries here
        $preparedAllUsers = $this->connect()->prepare(
            'SELECT * FROM `users` '
        );

        // prepares query (no placeholders for this one)
        $preparedAllUsers->execute();

        // run query
        $result = $preparedAllUsers->fetchAll();

        // send the result to the front end
        return $result;
    }

    public function getUser($first_name, $email)
    {
        // prepares query with placeholders
        $preparedGetUser = $this->connect()->prepare(
            'SELECT * FROM `users` WHERE `first_name` = :first_name AND `email` = :email '
        );

        // takes params and puts them into query
        $preparedGetUser->execute([
            'first_name' => $first_name,
            'email' => $email,
        ]);

        // run query
        $result = $preparedGetUser->fetchAll();

        // return result to display
        return $result;
    }


    public function createUser($first_name, $last_name, $email, $countries_id)
    {
        // sets up query with placeholders
        $preparedCreateUser = $this->connect()->prepare(
            'INSERT INTO `users` (
                `first_name`, `last_name`, `email`, `countries_id`
            ) VALUES (
                :first_name, :last_name, :email, :countries_id
            )'
        );

        // make it only return true or false as result
        $success = $preparedCreateUser->execute([
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'countries_id' => $countries_id,
        ]);

        // tell the user if it worked
        if ($success) {
            return 'New User Created';
        } else {
            return 'Failed to Create User';
        }
    }

    public function deleteUser($id)
    {
        // sets up query with placeholders
        $preparedDelete = $this->connect()->prepare(
            'DELETE FROM `users` WHERE `users`.`id` = :id'
        );

        // make it only return true or false as result
        $success = $preparedDelete->execute([
            'id' => $id,
        ]);

        // tell the user if it worked
        if ($success) {
            return 'Deleted User ' . $id;
        } else {
            return 'Failed to Delete User';
        }
    }

    public function updateUser($id, $first_name, $last_name, $email, $countries_id)
    {
        // sets up query with placeholders
        $preparedUpdate = $this->connect()->prepare(
            'UPDATE
                `users`
            SET
                `first_name`=:first_name,
                `last_name`=:last_name,
                `email`=:email,
                `countries_id`=:countries_id
            WHERE
                `users`.`id` = :id'
        );

        // make it only return true or false as result
        $success = $preparedUpdate->execute([
            'id' => $id,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'countries_id' => $countries_id,
        ]);

        // tell the user if it worked
        if ($success) {
            return 'User Updated';
        } else {
            return 'Failed to Update User';
        }
    }
}
