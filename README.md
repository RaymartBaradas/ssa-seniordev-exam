# Prerequisites

-   Composer ^2.5.4
-   PHP ^8.1.12
-   Laravel Framework 10.4.1
-   NodeJS ^18.12.1

---

## Instructions:

#### Install Dependencies

`composer install`
`npm install`

#### Setup Environment and Migrations

copy .env.example -> .env and setup your database connection and run this command

`php artisan migrate`
`php artisan key:generate`

#### Run Server

`php artisan serve`
`npm run dev`

---

#### Run Unit Testing

`vendor/bin/phpunit --testdox`

## Note:

If you encounter an error during unit testing, you can turn off your antivirus or run this command

`composer du`
