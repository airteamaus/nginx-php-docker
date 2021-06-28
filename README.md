# NGINX PHP Docker Images

The docker images in this project will run a PHP app in different deployment environments.

Currently the environments supported are:

1. Generic environment that has NGINX conf.d and www directories externally mounted. Configuration is loaded from environmental variable or an optional `.env` file located in the `/www` directory.

2. AWS environment that has NGINX conf.d and www directories externally mounted. Configuration is loaded from environmental variable or an optional `.env` file located in the `/www` directory first, then from AWS SecretsManager if `AWS_SECRET_ID` environmental variable is present.

## NGINX PHP Dockerfile

Creates a NGINX docker image to run a PHP site.

To build:

    docker build . --file nginx-php.dockerfile --tag nginx-php:latest

To run:

    docker run -i -p 8080:80 -t --rm -v $PWD/www:/www -v $PWD/nginx/conf.d:/etc/nginx/conf.d nginx-php:latest

## NGINX PHP AWS Dockerfile

Creates a NGINX docker image to run a PHP site in AWS ECS.

To build:

    docker build . --file nginx-php-aws.dockerfile --tag nginx-php-aws:latest

To run:

    docker run -i -p 8080:80 -t --rm -v $PWD/www:/www -v $PWD/nginx/conf.d:/etc/nginx/conf.d nginx-php-aws:latest
