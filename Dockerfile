# Utiliser une image PHP officielle comme base
FROM php:8.2-fpm

# Arguments pour la personnalisation
ARG user=laravel
ARG uid=1000

# Mettre à jour les paquets du système
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev

# Nettoyer le cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Installer les extensions PHP nécessaires
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Configurer l'extension GD
RUN docker-php-ext-configure gd --with-freetype --with-jpeg

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Créer un utilisateur système
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier les fichiers du projet
COPY . /var/www/html

# Copier les permissions
COPY --chown=$user:$user . /var/www/html

# Installer les dépendances Composer
RUN composer install --no-interaction --optimize-autoloader

# Générer la clé d'application
RUN php artisan key:generate

# Définir les permissions
RUN chown -R $user:www-data /var/www/html/storage
RUN chmod -R 775 /var/www/html/storage

# Basculer vers l'utilisateur personnalisé
USER $user

# Exposer le port
EXPOSE 9000

# Commande de démarrage
CMD ["php-fpm"]
