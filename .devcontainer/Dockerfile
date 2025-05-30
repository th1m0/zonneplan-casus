FROM mcr.microsoft.com/devcontainers/php

ENV COMPOSER_HOME="/home/vscode/.composer"
ENV COMPOSER_BIN_DIR="/home/vscode/.composer/vendor/bin"

ENV PATH="$PATH:$COMPOSER_BIN_DIR"

RUN composer global require laravel/installer
