FROM php:8.3-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim unzip git curl \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd

# Install Composer globally
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer


# Use this if using linux
ARG USER_ID
ARG GROUP_ID
ARG USER_NAME
ARG PW=docker

ENV USER_ID=$USER_ID
ENV GROUP_ID=$GROUP_ID
ENV USER_NAME=$USER_NAME


RUN useradd -m ${USER_NAME} --uid=${USER_ID} && echo "${USER_NAME}:${PW}" | \
      chpasswd

RUN usermod -aG sudo ${USER_NAME}

# End of comment

WORKDIR /var/www
COPY . .

RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www

EXPOSE 9000
CMD ["php-fpm"]
