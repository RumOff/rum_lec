FROM php:8.1-fpm

# システムパッケージと依存関係のインストール
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# PHP拡張機能のインストール
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    zip \
    bcmath \
    pcntl \
    exif

# Composerのインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 作業ディレクトリを設定
WORKDIR /var/www/html

# パーミッションの設定
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# sailユーザーの作成
RUN groupadd -g 1000 sail && useradd -u 1000 -g sail -m sail

# Composerのホームディレクトリを作成し、権限を設定
RUN mkdir -p /home/sail/.composer && \
    chown -R sail:sail /home/sail/.composer
