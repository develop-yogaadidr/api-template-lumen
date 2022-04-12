# prerequisite

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
4. Update the `.env` value of the properties `DB_CONNECTION`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` according to your database connection.

5. Generate JWT Key by executing this in the command line
    ```shell
    php artisan jwt:secret
    ```
    This will update your .env file with something like JWT_SECRET=foobar

## Setting Up The Mail

Open your `.env` file and update the value of the properties according your mail credentials:

```
MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=hello@example.com
MAIL_FROM_NAME="Example app"
```

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

# Integration With Firebase

1. Download firebase new private key at
   `https://console.firebase.google.com/u/2/project/template-app-b51ac/settings/serviceaccounts/adminsdk`

    You can also using the existing key that you have already saved in your local disk.

2. Rename the key file to `firebase-app-key.json`
3. Placed the key into the `./app` folder.
4. Make sure you have already set the `.env` value of `FIREBASE_CREDENTIALS` with the correct key path.

## Sending Message Using Firebase Cloud Messaging

1. Inject class `Messaging` in the class constructor. In this case we use `UserController` class.

    ```php
    class UserController extends Controller
    {
        public function __construct(Messaging $messaging)
        {
            ...
            $this->messaging = $messaging;
            ...
        }
    }
    ```

    You must extends `Controller` or its derrived classes to set variable `$messaging`.

2. Build and send message

    ```php
     $message = new MessageParameter;
     $message->setNotification("title here", "body here");
     $message->setTarget(MessageTarget::TOPIC, "topic-A");

     $this->sendMessage($message);
    ```

    in the `setTarget()` method you can pass `MessageTarget::TOKEN` to send message to a specific client.

## Start the server

### Start the program via hosting

1. Copy this project to the hosting folder (e.g `public_html` or `htdocs` if you are using XAMPP)
2. To access this project, you can access `http://{host}/public`

### Start the program via terminal

1. Open terminal.
2. Go to the root folder of this project.
3. Make sure you have already installed all the required packages (see in the <b>Setting Up The Configuration</b> section).
4. run this command in the terminal.

    ```shell
    php -S localhost:8000 -t public
    ```

    you can access the server via browser at http://localhost:8000
