# Prequisites
1. PHP v.7.3 or greater
2. Installed [Composer](https://getcomposer.org/download/)

# Installation
## Setting Up The Configuration
1. Open command line / terminal, go to this root project location.
2. Install all the required component by executin this command in the terminal.
   ```shell
   composer install
   ```
3. Copy file `.env.example` and rename to `.env`.
4. Update `.env` value of `DB_CONNECTION`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` according to your database connection.

5. Generate JWT Key by executing this in the command line
   ```shell
   php artisan jwt:secret
   ```
   This will update your .env file with something like JWT_SECRET=foobar

## Migration Database
1. Open command line / terminal, go to this root project location.
2. Apply the migrations by executing this command in the command line.
   ```shell
   php artisan migrate
   ``` 
   or if you want to do fresh migration (clear all data), you can execute this command.
   ```shell
   php artisan migrate:fresh
   ```

   (WIP) To do data seeding (preloaded data), you can execute this command in the command line.
   ```shell
   php artisan db:seed --class=UserTableSeeder
   ```

## Start the server
### Start the program via hosting

### Start the program via terminal
1. Open terminal.
2. Go to the root folder of this project.
3. Make sure you have already installed all the required packages (see in the <b>Setting Up The Configuration</b> section).
4. run this command in the terminal.

    ```shell
    php -S localhost:8000 -t public
    ```

    you can access the server via browser at http://localhost:8000