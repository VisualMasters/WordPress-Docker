FROM php:7.4-fpm
LABEL maintainer="justin@burovoordeboeg.nl"

# install the PHP extensions we need (https://make.wordpress.org/hosting/handbook/handbook/server-environment/#php-extensions)
RUN set -ex; \
	\
	savedAptMark="$(apt-mark showmanual)"; \
	\
	apt-get update; \
	apt-get install -y --no-install-recommends \
		libfreetype6-dev \
		libicu-dev \
		libjpeg-dev \
		libmagickwand-dev \
		libpng-dev \
		libwebp-dev \
		libzip-dev \
	; \
	\
	docker-php-ext-configure gd \
		--with-freetype \
		--with-jpeg \
		--with-webp \
	; \
	docker-php-ext-install -j "$(nproc)" \
		bcmath \
		exif \
		gd \
		intl \
		mysqli \
		zip \
	; \
# https://pecl.php.net/package/imagick
	pecl install imagick-3.6.0; \
	docker-php-ext-enable imagick; \
	rm -r /tmp/pear; \
	\
# some misbehaving extensions end up outputting to stdout 🙈 (https://github.com/docker-library/wordpress/issues/669#issuecomment-993945967)
	out="$(php -r 'exit(0);')"; \
	[ -z "$out" ]; \
	err="$(php -r 'exit(0);' 3>&1 1>&2 2>&3)"; \
	[ -z "$err" ]; \
	\
	extDir="$(php -r 'echo ini_get("extension_dir");')"; \
	[ -d "$extDir" ]; \
# reset apt-mark's "manual" list so that "purge --auto-remove" will remove all build dependencies
	apt-mark auto '.*' > /dev/null; \
	apt-mark manual $savedAptMark; \
	ldd "$extDir"/*.so \
		| awk '/=>/ { print $3 }' \
		| sort -u \
		| xargs -r dpkg-query -S \
		| cut -d: -f1 \
		| sort -u \
		| xargs -rt apt-mark manual; \
	\
	apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false; \
	rm -rf /var/lib/apt/lists/*; \
	\
	! { ldd "$extDir"/*.so | grep 'not found'; }; \
# check for output like "PHP Warning:  PHP Startup: Unable to load dynamic library 'foo' (tried: ...)
	err="$(php --version 3>&1 1>&2 2>&3)"; \
	[ -z "$err" ]

# set recommended PHP.ini settings
# see https://secure.php.net/manual/en/opcache.installation.php
RUN set -eux; \
	docker-php-ext-enable opcache; \
	{ \
		echo 'opcache.memory_consumption=128'; \
		echo 'opcache.interned_strings_buffer=8'; \
		echo 'opcache.max_accelerated_files=4000'; \
		echo 'opcache.revalidate_freq=2'; \
		echo 'opcache.fast_shutdown=1'; \
	} > /usr/local/etc/php/conf.d/opcache-recommended.ini
# https://wordpress.org/support/article/editing-wp-config-php/#configure-error-logging
RUN { \
# https://www.php.net/manual/en/errorfunc.constants.php
# https://github.com/docker-library/wordpress/issues/420#issuecomment-517839670
		echo 'error_reporting = E_ERROR | E_WARNING | E_PARSE | E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING | E_RECOVERABLE_ERROR'; \
		echo 'display_errors = Off'; \
		echo 'display_startup_errors = Off'; \
		echo 'log_errors = On'; \
		echo 'error_log = /dev/stderr'; \
		echo 'log_errors_max_len = 1024'; \
		echo 'ignore_repeated_errors = On'; \
		echo 'ignore_repeated_source = Off'; \
		echo 'html_errors = Off'; \
	} > /usr/local/etc/php/conf.d/error-logging.ini

# move the installation files
COPY setup /usr/src

# get wordpress and push it to the correct location
RUN curl -o wordpress.tar.gz https://nl.wordpress.org/wordpress-5.9-nl_NL.tar.gz; \
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

# install WP CLI
RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar; \
	chmod +x wp-cli.phar; \
	mv wp-cli.phar /usr/local/bin/wp;

# set the keys in the env file to be random
RUN curl https://api.burovoordeboeg.nl/env/ -o /var/www/salts.txt; \
	sed -i -e '/WPSALTS/{r /var/www/salts.txt' -e 'd' -e ' }' /var/www/.env; \
	rm /var/www/salts.txt; \
	curl https://api.burovoordeboeg.nl/env/licenses.php -o /var/www/licenses.txt; \
	sed -i -e '/WPLICENSES/{r /var/www/licenses.txt' -e 'd' -e ' }' /var/www/.env; \
	rm /var/www/licenses.txt;

# https://wordpress.org/support/article/htaccess/
RUN set -eux; \
	[ ! -e /var/www/html/.htaccess ]; \
	{ \
		echo '# BEGIN WordPress'; \
		echo ''; \
		echo 'RewriteEngine On'; \
		echo 'RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]'; \
		echo 'RewriteBase /'; \
		echo 'RewriteRule ^index\.php$ - [L]'; \
		echo 'RewriteCond %{REQUEST_FILENAME} !-f'; \
		echo 'RewriteCond %{REQUEST_FILENAME} !-d'; \
		echo 'RewriteRule . /index.php [L]'; \
		echo ''; \
		echo '# END WordPress'; \
	} > /var/www/html/.htaccess;

# mount the volume
VOLUME /var/www/html

CMD ["apache2-foreground"]
