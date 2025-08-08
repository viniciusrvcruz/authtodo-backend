# Ensure .env exists
if [ ! -f .env ]; then
  echo "Copying .env.example to .env..."
  cp .env.example .env
fi

# Install dependencies (if vendor does not exist)
if [ ! -d vendor ]; then
  echo "Installing dependencies with composer..."
  composer install
fi

# Generate application key (only if APP_KEY is not set)
if ! grep -q "APP_KEY=" .env || [ -z "$(grep 'APP_KEY=' .env | cut -d '=' -f2)" ]; then
  echo "Generating application key..."
  php artisan key:generate
fi

echo "Running migrations..."
php artisan migrate --force

php artisan serve --host=0.0.0.0 --port=80