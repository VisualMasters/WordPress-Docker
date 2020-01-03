FROM php:7.3-apache
LABEL maintainer="justin@visualmasters.nl"

# install the PHP extensions we need (https://make.wordpress.org/hosting/handbook/handbook/server-environment/#php-extensions)
RUN set -ex; \
	\
	savedAptMark="$(apt-mark showmanual)"; \
	\
	apt-get update; \
	apt-get install -y --no-install-recommends \
	libjpeg-dev \
	libmagickwand-dev \
	libpng-dev \
	libzip-dev \
	; \
	\
	docker-php-ext-configure gd --with-png-dir=/usr --with-jpeg-dir=/usr; \
	docker-php-ext-install \
	bcmath \
	exif \
	gd \
	mysqli \
	opcache \
	zip \
	; \
	pecl install imagick-3.4.4; \
	docker-php-ext-enable imagick; \
	\
	# reset apt-mark's "manual" list so that "purge --auto-remove" will remove all build dependencies
	apt-mark auto '.*' > /dev/null; \
	apt-mark manual $savedAptMark; \
	ldd "$(php -r 'echo ini_get("extension_dir");')"/*.so \
	| awk '/=>/ { print $3 }' \
	| sort -u \
	| xargs -r dpkg-query -S \
	| cut -d: -f1 \
	| sort -u \
	| xargs -rt apt-mark manual; \
	\
	apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false; \
	rm -rf /var/lib/apt/lists/*

# install nano for testing and debugging easier
RUN apt-get update; \
	apt-get install -y nano;

# set recommended PHP.ini settings
# see https://secure.php.net/manual/en/opcache.installation.php
RUN { \
	echo 'opcache.memory_consumption=128'; \
	echo 'opcache.interned_strings_buffer=8'; \
	echo 'opcache.max_accelerated_files=4000'; \
	echo 'opcache.revalidate_freq=2'; \
	echo 'opcache.fast_shutdown=1'; \
	echo 'opcache.enable_cli=1'; \
	} > /usr/local/etc/php/conf.d/opcache-recommended.ini
# https://codex.wordpress.org/Editing_wp-config.php#Configure_Error_Logging
RUN { \
	echo 'error_reporting = 4339'; \
	echo 'display_errors = Off'; \
	echo 'display_startup_errors = Off'; \
	echo 'log_errors = On'; \
	echo 'error_log = /dev/stderr'; \
	echo 'log_errors_max_len = 1024'; \
	echo 'ignore_repeated_errors = On'; \
	echo 'ignore_repeated_source = Off'; \
	echo 'html_errors = Off'; \	
	echo 'post_max_size = 1024M'; \
	echo 'upload_max_filesize = 1024M'; \
	echo 'memory_limit = 1024M'; \
	} > /usr/local/etc/php/conf.d/error-logging.ini

RUN a2enmod rewrite expires

# move the installation files
COPY setup /usr/src

# get wordpress and push it to the correct location
RUN curl -o wordpress.tar.gz https://nl.wordpress.org/wordpress-5.3.2-nl_NL.tar.gz; \
	tar -xzf wordpress.tar.gz; \
	rm wordpress.tar.gz; \
	mv wordpress/* ./; \
	rm -rf wordpress; \
	mkdir wp; \
	chown -R www-data:www-data wp; \
	mv -t wp/ *.txt *.html *.php wp-admin wp-includes; \
	rm wp/index.php; \
	mv wp-content content; \
	rm -R -- content/themes/*/; \
	rm -rf content/plugins/hello.php content/plugins/akismet; \
	mv /usr/src/.env /var/www/; \
	mv /usr/src/config /var/www/; \
	mv /usr/src/vendor /var/www/; \
	mv /usr/src/index.php /var/www/html/; \
	mv /usr/src/wp-config.php /var/www/html/; \
	chown -R www-data:www-data /var/www; \
	find /var/www -type d -exec chmod 755 {} \; && find /var/www -type f -exec chmod 644 {} \;

# set the keys in the env file to be random
RUN curl http://api.visual-masters.nl/env/ -o /var/www/salts.txt; \
	sed -i -e '/WPSALTS/{r /var/www/salts.txt' -e 'd' -e ' }' /var/www/.env; \
	rm /var/www/salts.txt;

# mount the volume
VOLUME /var/www/html

CMD ["apache2-foreground"]