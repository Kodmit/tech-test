# README

## Project Setup

This project uses Docker for setting up the development environment. The services are defined in the `docker-compose.yaml` file and include Apache, PHP, Node, and PostgreSQL.

### Docker Commands

- **Start the environment**: `make up`
- **Stop the environment**: `make down`
- **Initialize the environment**: `make init`

### Service URLs

- **API**: http://localhost
- **Frontend**: http://localhost:3000

## PHP Code Overview

The PHP code is structured following the Symfony framework conventions. The main components are:

- **Entity**: The `Article` entity represents a table in the database. It has properties like `id`, `name`, `content`, `isPublished`, and `createdAt`. Each property is annotated with ORM and validation annotations.

- **Controller**: The `AbstractController` provides methods to handle incoming requests and extract data from them. The `ArticleController` extends this abstract class and provides endpoints to manage articles (get, create, delete).

- **Repository**: The `ArticleRepository` is used to interact with the `Article` table in the database.

Remember to run `composer install` to install the PHP dependencies and `make db-migrate` to apply the database migrations.

## Makefile Commands

- **Open a bash terminal for Symfony**: `make bash`
- **Clear the Symfony cache**: `make ca-cl`
- **Install npm dependencies**: `make npm`
- **Install Symfony vendors**: `make composer-install`
- **Create the database**: `make db-create`
- **Migrate the database**: `make db-migrate`

Please refer to the `Makefile` for more commands and details.

## Docker Operations (ops)

The `ops` directory contains Dockerfiles and configuration files for the Docker services used in this project. Here's a brief overview of each service:

- **Apache**: The Apache service is built from the Dockerfile in `ops/apache`. It serves the Symfony application on ports 80 and 8080. The Apache virtual host configuration files are stored in `ops/apache/config/vhosts`.

- **PHP**: The PHP service is built from the Dockerfile in `ops/php`. It runs the PHP-FPM process to handle PHP requests from Apache. The PHP service has access to the Symfony application code through a Docker volume.

- **Node**: The Node service is built from the Dockerfile in `ops/node`. It runs a Node.js server for the Vue.js frontend application. The Node service has access to the frontend code through a Docker volume.

- **PostgreSQL**: The PostgreSQL service uses the `postgres:15-alpine` image. It runs a PostgreSQL database server. The database data is stored in a Docker volume to persist data across container restarts.

Each service is defined in the `docker-compose.yaml` file. The services are interconnected, meaning they can communicate with each other. For example, the PHP service can connect to the PostgreSQL service to query the database.

Remember to run `docker-compose up -d` to start the Docker services and `docker-compose down` to stop them.