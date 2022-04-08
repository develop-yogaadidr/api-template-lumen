# Start the server
1. Open terminal
2. Go to the root folder of this project
3. Install the required packages

    ```
    composer install
    ```

4. run this command in the terminal

    ```shell
    php -S localhost:8000 -t public
    ```
# Migrations
## Apply migrations
```
php artisan migrate
```

## Drop All Tables and Migrate
```
php artisan migrate:fresh
```
# Generate secret JWT key 
```shell
php artisan jwt:secret
```
This will update your .env file with something like JWT_SECRET=foobar