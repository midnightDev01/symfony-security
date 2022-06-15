#!/bin/bash

. ./.env

exists=$(cat "/etc/hosts" | grep "${APP_NAME}")
if [ -z "${exists}" ]; then
  echo "127.0.0.1 ${APP_NAME}.docker" | sudo tee -a /etc/hosts >/dev/null
fi
