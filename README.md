# Survey Feedback Api

## 📝 Description

A simple backend API built with Laravel 11 and PHP 8.4 for collecting feedback through surveys. Responders can register, log in, and submit answers to surveys using secure JWT authentication.

The API allows anyone to view active surveys and their questions, but only logged-in responders can submit responses.

The system follows RESTful design, includes proper data validation, rate limiting, caching with Redis, and logging.

## 🚀 Installation

### PHP and Composer

If you don't have PHP and Composer installed on your local machine, the following commands will install PHP, Composer, and the Laravel installer:

#### macOS

```sh
/bin/bash -c "$(curl -fsSL https://php.new/install/mac/8.4)"
```

#### Windows PowerShell

```powershell
# Run as administrator...
Set-ExecutionPolicy Bypass -Scope Process -Force; [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072; iex ((New-Object System.Net.WebClient).DownloadString('https://php.new/install/windows/8.4'))
```

#### Linux

```sh
/bin/bash -c "$(curl -fsSL https://php.new/install/linux/8.4)"
```

## Clone the project from the GitHub repository

```git
git clone https://github.com/mariamitr98/survey-feedback-api.git
```

## Setting Up Docker and Containers (MySql, Redis)

Install Docker by following the instructions on the official [site](https://docs.docker.com/desktop/). Then run the following commands in order to create the docker containers

### Redis

```docker
docker run -d --name redis-stack -p 6379:6379 -p 8001:8001 redis/redis-stack:latest
```

### MySQL

```docker
docker run --detach --name mysql-container --env MYSQL_ROOT_PASSWORD=rootpassword --publish 3306:3306 --volume mysql-data:/var/lib/mysql mysql:latest
```

## Run the application

### Install dependecies

```sh
composer install
```

### Decrypt the .env.encrypted file

```sh
php artisan env:decrypt --key=
```

### Run migrations and seeders

```sh
php artisan migrate --seed
```

### Run

```sh
php artisan serve
```

## 🌐 API EndPoints

File <b> routes/api.php </b>

- `/register`: Register responder to app.

- `/login`: Login responder to app.

- `/logout`: Logout responder form the app.

- `/surveys`: Return all active surveys.

- `/surveys/{id}`: Return a given survey.

- `/surveys/{id}/submit`: Submit answer(s) for a given survey.

- `/me`: Returns the responder information.

## 🔧 Testing API Endpoints with REST Client

For testing and verifying the correct functionality of the API endpoints, a file named endpoint-examples.http is included in the project.

This file contains example HTTP requests that can be executed directly within Visual Studio code using the REST Client extension. This extension allows you to send HTTP requests and view responses directly in the editor, making endpoint testing simple and efficient.

### How to use:

1. Install the REST Client extension in Visual Studio Code.

2. Open the endpoint-examples.http file.

3. Select the HTTP request you want to test.