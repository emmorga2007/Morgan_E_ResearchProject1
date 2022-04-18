# Morgan_E_Research_1

## References

### Setting up the Database Class and using connect in child
https://www.youtube.com/watch?v=PHiu0JA9eqE

### Delete from phpmyadmin SQL preview:
DELETE FROM `users`
WHERE
`users`.`id` = 4

### Update sql from phpmyadmin:
UPDATE `users`
SET
`first_name` = 'Jack',
`last_name` = 'Jill',
`email` = 'jack@gmail.com',
`countries_id` = '2'
WHERE
`users`.`id` = 4
