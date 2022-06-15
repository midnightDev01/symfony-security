ARG PHP_VERSION

FROM php:${PHP_VERSION}-fpm-buster

ARG PLATFORM_ENVIRONMENT
ARG PHP_MEMORY_LIMIT
ARG APP_NAME
ARG LC_ALL
ARG LANG
ARG TZ

ARG FPM_MAX_CHILDREN
ARG FPM_START_SERVERS
ARG FPM_MIN_SPARE_SERVERS
ARG FPM_MAX_SPARE_SERVERS

ENV PHP_VERSION="${PHP_VERSION}"
ENV PLATFORM_ENVIRONMENT="${PLATFORM_ENVIRONMENT}"
ENV PHP_MEMORY_LIMIT="${PHP_MEMORY_LIMIT}"
ENV APP_NAME="${APP_NAME}"

ENV LC_ALL=${LC_ALL}
ENV LANG=${LANG}
ENV TZ=${TZ}

ENV FPM_MAX_CHILDREN="${FPM_MAX_CHILDREN}"
ENV FPM_START_SERVERS="${FPM_START_SERVERS}"
ENV FPM_MIN_SPARE_SERVERS="${FPM_MIN_SPARE_SERVERS}"
ENV FPM_MAX_SPARE_SERVERS="${FPM_MAX_SPARE_SERVERS}"

# Setup common linux tools
RUN apt update && \
    apt install -y --no-install-recommends \
    procps \
    zip \
    unzip \
    git \
    jq \
    nano \
    wget \
    netcat \
    sudo \
    libicu-dev \
    libzip-dev \
    zlib1g-dev \
    libcurl4-openssl-dev \
    pkg-config \
    libssl-dev


# Setup Symfony CLI (new!)
RUN wget https://github.com/symfony-cli/symfony-cli/releases/download/v5.4.1/symfony-cli_5.4.1_amd64.deb && \
    dpkg -i symfony-cli_5.4.1_amd64.deb && \
    rm symfony-cli_5.4.1_amd64.deb

# Setup PHP Deployer
RUN curl -LO https://deployer.org/deployer.phar && \
    mv deployer.phar /usr/local/bin/dep && \
    chmod +x /usr/local/bin/dep

# Install cachetool for php OPcache
RUN curl -sLO https://github.com/gordalina/cachetool/releases/latest/download/cachetool.phar && \
    chmod +x cachetool.phar && \
    mv cachetool.phar /usr/local/bin/cachetool

# Some needed PHP extensions
ENV PHP_EXTENTIONS="intl opcache bcmath iconv calendar zip"
RUN docker-php-ext-install $PHP_EXTENTIONS  &&  docker-php-ext-enable $PHP_EXTENTIONS

# Setup MongoDB extension
RUN pecl install mongodb && \
    echo 'extension=mongodb.so' > "$PHP_INI_DIR/conf.d/20-mongodb.ini";

# Setup and configure XDebug
ENV XDEBUG_VERSION 3.1.3
ENV PHP_IDE_CONFIG "serverName=${APP_NAME}"
ENV XDEBUG_SESSION "serverName=${APP_NAME}"
ENV XDEBUG_MODE "debug"

# Setup XDebug (DEV ONLY)
RUN if [ "${PLATFORM_ENVIRONMENT}" = "dev" ]; then \
      mkdir -p /usr/src/php/ext/xdebug && \
      curl -L https://github.com/xdebug/xdebug/archive/$XDEBUG_VERSION.tar.gz | tar xvz -C /usr/src/php/ext/xdebug --strip 1 && \
      docker-php-ext-install xdebug && \
      echo 'xdebug.client_host=172.17.0.1'  >> "$PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini" && \
      echo 'xdebug.client_port=9000'  >> "$PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini" && \
      echo 'xdebug.start_with_request'  >> "$PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini" && \
      echo 'xdebug.remote_cookie_expire_time=3600'  >> "$PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini" && \
      echo 'xdebug.mode=debug' >> "$PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini"; \
    fi

# Setup and enable config SPX:  A simple profiler for PHP (DEV ONLY)
RUN if [ "${PLATFORM_ENVIRONMENT}" = "dev" ]; then \
        git clone https://github.com/NoiseByNorthwest/php-spx.git && \
        cd php-spx && \
        git checkout release/latest && \
        phpize && \
        ./configure && \
        make && \
        make install && \
        mkdir -p /usr/local/lib/php/extensions/spx && \
        mv /var/www/html/php-spx/modules/spx.so /usr/local/lib/php/extensions/spx/ && \
        echo "process.dumpable = yes" >>  "$PHP_INI_DIR/php.ini-development"  && \
        touch "$PHP_INI_DIR/conf.d/20-spx.ini" && \
        echo "extension= /usr/local/lib/php/extensions/spx/spx.so" > "$PHP_INI_DIR/conf.d/20-spx.ini"; \
  fi
# Increase memory limit (production)
RUN if [ "${PLATFORM_ENVIRONMENT}" != "dev" ] ; then \
      sed -i "/^memory_limit\s=/s/=.*/= ${PHP_MEMORY_LIMIT}/"  /usr/local/etc/php/php.ini-production; \
    fi

# Set memory to unlimited (!!DEV ONLY !!)
RUN if [ "${PLATFORM_ENVIRONMENT}" = "dev" ] ; then \
      sed -i "/^memory_limit\s=/s/=.*/= -1/"  /usr/local/etc/php/php.ini-development; \
    fi

# Use corresponding ini
RUN echo "PLATFORM_ENVIRONMENT = $PLATFORM_ENVIRONMENT"
RUN echo "PHP_VERSION = ${PHP_VERSION}"
RUN echo "PHP_MEMORY_LIMIT = $PHP_MEMORY_LIMIT"
RUN echo "APP_NAME = $APP_NAME"

RUN if [ "${PLATFORM_ENVIRONMENT}" = "dev" ] ; then \
      mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"; \
    else \
      mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"; \
    fi

# Fix Time Zone
RUN echo "date.timezone = $TZ" >>  "$PHP_INI_DIR/php.ini"

# copy Symfony CLI production performance optimizers
COPY ./php.prod.ini "/${APP_NAME}/php.prod.ini"
RUN if [ "${PLATFORM_ENVIRONMENT}" = "production" ] ; then \
    cat "/${APP_NAME}/etc/php.prod.ini"  >>  "$PHP_INI_DIR/php.ini" \
    sed -i "s/__APP_NAME__/$APP_NAME/g"  "$PHP_INI_DIR/php.ini" \
    rm "/${APP_NAME}/etc/php.prod.ini"; \
    fi

RUN  cat "$PHP_INI_DIR/php.ini";

# Configure PHP FPM
COPY ./www.conf.dist /usr/local/etc/php-fpm.d/www.conf.dist
RUN rm -rf /usr/local/etc/php-fpm.d/www.conf && \
    mv /usr/local/etc/php-fpm.d/www.conf.dist /usr/local/etc/php-fpm.d/www.conf && \
    sed -i "s/__FPM_MAX_CHILDREN__/$FPM_MAX_CHILDREN/g" /usr/local/etc/php-fpm.d/www.conf && \
    sed -i "s/__FPM_START_SERVERS__/$FPM_START_SERVERS/g" /usr/local/etc/php-fpm.d/www.conf && \
    sed -i "s/__FPM_MIN_SPARE_SERVERS__/$FPM_MIN_SPARE_SERVERS/g" /usr/local/etc/php-fpm.d/www.conf && \
    sed -i "s/__FPM_MAX_SPARE_SERVERS__/$FPM_MAX_SPARE_SERVERS/g" /usr/local/etc/php-fpm.d/www.conf;

RUN  cat "/usr/local/etc/php-fpm.d/www.conf";

# Setup composer
COPY --from=composer /usr/bin/composer /usr/local/bin/composer

# command line setup
# do minimal setup so we can be semi-efficient when using
# the command line of the container. Without PS1, we will
# get a prompt like "I have no name!@<container_id_hash>:/$"
# since we don't create a user or group.
RUN set -ex; \
    echo "PS1='\h:\w\$ '" >> /etc/bash.bashrc; \
    echo "alias ls='ls --color=auto'" >> /etc/bash.bashrc; \
    echo "alias grep='grep --color=auto'" >> /etc/bash.bashrc;


# Create user named "docker" with no password
RUN useradd --create-home --shell /bin/bash docker \
    && passwd docker -d \
    && adduser docker sudo

# Don't require a password for sudo
RUN sed -i 's/^\(%sudo.*\)ALL$/\1NOPASSWD:ALL/' /etc/sudoers

# set an entrypoint script that allows us to
# dynamically change the uid/gid of the container's user
COPY entry_point.sh /opt/bin/
RUN mkdir -p /tmp/phpstan && \
    chown -R 1000:1000  /tmp/phpstan/

ENTRYPOINT ["/opt/bin/entry_point.sh"]
WORKDIR /var/www/${APP_NAME}/laravel

EXPOSE 9000
STOPSIGNAL SIGQUIT

CMD ["php-fpm"]
