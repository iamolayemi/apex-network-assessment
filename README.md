# ApexNetwork Backend Developer Assessment

This repository contains the solutions to apex network backend developer assessment role.

## Getting Started

> This guide assumed you have [PHP 8.2+](https://php.net/releases/) and [Composer](https://getcomposer.org/) installed on your pc.
>
### Clone and Navigate
Clone this repository and navigate into it:

```bash
git clone git@github.com:iamolayemi/apex-network-assessment.git

cd apex-network-assessment
```

### Install Dependencies
Install the application dependencies by running the following command:
```bash 
composer install
```

### Create a `.env` file
Copy the contents of the `.env.example` file into a new `.env` file by running the following command:
```bash
cp .env.example .env
```

### Setup Application Key
Generate a new application key by running the following command:
```bash
php artisan key:generate
```

### Setup Database
You can use SQLite for the database by default. If you want to use another database, update the database configuration in the `.env` file.

Create a new SQLite database by running the following command:
```bash
touch database/database.sqlite
```

### Run Migrations
Run the database migrations to create the necessary tables by running the following command:
```bash
php artisan migrate
```

### Seed Database
Seed the database with dummy data by running the following command:
```bash
php artisan db:seed
```

### Setup Laravel Passport
1. To set up passport oauth keys run the following command:
```bash
php artisan passport:keys
```

2. Next, you need to setup the Laravel Passport personal access token clients by running the following command:
```bash
php artisan passport:client --personal
```

After running the command, you will be prompted to enter the client name. You can enter any name you want.

Then copy the client id and client secret generated and update the `PASSPORT_PERSONAL_ACCESS_CLIENT_ID` and `PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET` in the `.env` file.

### Start the Application
Start the application by running the following command:
```bash
php artisan serve
```

The application should now be running and accessible at http://localhost:8000 in your web browser. You can access the API documentation at http://localhost:8000/docs.

## Demo Credentials
You can use the following credentials to test the application:

### Admin
- Email: admin@example.com
- Password: Password123!

### User
- Email: user@example.com
- Password: Password123!

## Running Tests
To run tests for the application you can use this command:
```bash
php artisan test

# To run tests with coverage
php artisan test --coverage
```


