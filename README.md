# Example of Filament Pos App

## Installation

Clone the repo locally:

```sh
git clone https://github.com/michaelnabil230/pos-with-filament.git filament-demo && cd pos-with-filament
```

Install PHP dependencies:

```sh
composer install
```

Setup configuration:

```sh
cp .env.example .env
```

Generate application key:

```sh
php artisan key:generate
```

Create an MySQL database. You can also use another database MariaDB, simply update your configuration accordingly.

Run database migrations:

```sh
php artisan migrate
```

Run database seeder:

```sh
php artisan db:seed
```

Run the dev server (the output will give the address):

```sh
php artisan serve
```

You're ready to go! Visit the url in your browser, and login with:

- **Username:** admin@admin.com
- **Password:** password
