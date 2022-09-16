## PHP Slim 4 Framework

<h1 align="center">
    <br/>
 <a href="https://www.php.net/downloads" target="_blank" rel="noopener">PHP</a> | <a href="https://www.slimframework.com/docs/v4/" target="_blank" rel="noopener">Slim</a>
</h1>

<p align="center">
 <img alt="Language used" src="https://img.shields.io/badge/Language%20-%20^8.0-green?style=flat&logo=PHP">
 <img alt="Made with Slim" src="https://img.shields.io/badge/Made%20with%20-Slim-orange?style=flat&logo=PHP">
<p>

<h3 align="center">
  <a href="./README.md">About</a>
  <span> · </span>
  <a href="#-stack-used">Stack used</a>
  <span> · </span>
  <a href="#-how-to-use">How to use</a>
  <span> · </span>
  <a href="#-useful-links">Useful links</a>
</h3>

## 💭 About

This is a repository to test a small application using the Slim 4, rabbitMQ, MySQL, Docker and Nginx 

## 👨‍💻 Stack used

- <a href="https://www.php.net/downloads" target="_blank" rel="noopener">PHP ^8.x</a>
- <a href="https://www.slimframework.com/docs/v4/" target="_blank" rel="noopener">Slim 4</a>
- <a href="https://docs.docker.com/" target="_blank" rel="noopener">Docker</a>

## ⁉ How to use

### 🤔 Requirements

To be able to run this project, first you will need to have in your machine:

- **<a href="https://getcomposer.org" target="_blank" rel="noopener">Git</a>** to be able to clone this repository
- **<a href="https://docs.docker.com/" target="_blank" rel="noopener">Docker</a>** to be able to run the application

### 📝 Step to Step

First the repository in your computer

1. Cloning the repository

```sh
  # Clone the repository
  $ git clone git@git.jobsity.com:gabrielpatrola/php-challenge.git
  # Go to the project folder
  $ cd php-challenge
```

2. Create a .env file

```sh
  # Create a .env file
  $ cp .env.example .env # Or copy .env.example .env (windows)
```

3. Make changes in .env file

Open the `.env` file and change the values in database variables and/or other variables if necessary.

4. Starting the application

The application have everything in a docker container with Nginx, Mysql, PHP and Rabbitmq, so you will need to build the
container with the command above:

```sh
$ docker-compose up -d --build 
```

5. Installing the project dependencies

```sh
  $ docker exec -it slim_php composer install  
```

6. Running the database migrations

```sh
  $  docker exec -it slim_php php vendor/bin/doctrine orm:schema-tool:update --force
```

If everything went good, you will be able to access the application in http://localhost:8080

The app have 4 endpoints:

### `POST /users`

This endpoint is used to create a new user with the payload above:

```json
{
  "name": "Test User",
  "email": "test@test.com",
  "password": "12345678"
}
```

### `POST /login`

This endpoint is used to create a new user with the payload above:

```json
{
  "email": "test@test.com",
  "password": "12345678"
}
```

### `GET /stock`

This endpoint expected a query param `q` that recieves the stock symbol and also a header param called `Authorization` 
with a token that can be got using the login route.

This endpoint returns all information for the requested stock using the `Stooq API`

### `GET /history`

This endpoint shows all stock requests that got succefull results. This endpoint also needs the `Authorization` header

More information about the endpoints can be found in the postman collection in this **<a href="./assets/slim-request.postman_collection.json" target="_blank" rel="noopener">link</a>**

7. Sending emails

This application uses rabbitmq to send asynchronous emails, the command to execute the script is:

```sh
$ docker exec -it slim_php php consumer.php  
```

8. Testing the application

```sh
  $ docker exec -it slim_php composer test  
```

### 📚 Postman and Useful Links

- **<a href="./assets/slim-request.postman_collection.json" target="_blank" rel="noopener">Postman</a>** with all
  endpoints
- **<a href="https://www.slimframework.com/docs/v4/" target="_blank" rel="noopener">Slim docs</a>**

---
