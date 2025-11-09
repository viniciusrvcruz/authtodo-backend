#!/bin/bash

until curl -s http://authtodo_api/ > /dev/null; do
  echo "Waiting for API..."
  sleep 2
done

php artisan queue:work