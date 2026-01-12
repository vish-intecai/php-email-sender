# PHP Email Sender API

A production-ready PHP email API using **PHPMailer**  
Works on **macOS**, **Windows**, **Ubuntu**, and **Docker**  
Deployable on shared hosting and VPS environments.

---

## Project Structure

```
email-sender/
├── send-email.php
├── composer.json
├── composer.lock
├── vendor/
├── .env
└── Dockerfile
```

---

## Requirements

- PHP **8.1** or above  
- [Composer](https://getcomposer.org/)  
- SMTP credentials

---

## Environment File

Create a `.env` file in the project root:

```env
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=yourmail@gmail.com
MAIL_PASSWORD=app_password
MAIL_FROM_ADDRESS=no-reply@local.test
MAIL_FROM_NAME=Local Test
```

---

## Local Setup

### macOS

```sh
# Install Homebrew, PHP, and Composer
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
brew install php composer

# Verify installations
php -v
composer -V

# Install project dependencies
composer install

# Run the API
php -S localhost:8000
```

### Windows

1. Install [XAMPP](https://www.apachefriends.org) (includes PHP).
2. Enable Apache and PHP in XAMPP.
3. Install [Composer for Windows](https://getcomposer.org/Composer-Setup.exe).
4. Verify:
    ```sh
    php -v
    composer -V
    ```
5. Install dependencies:
    ```sh
    composer install
    ```
6. Run the API:
    ```sh
    php -S localhost:8000
    ```

### Ubuntu

```sh
sudo apt update
sudo apt install php php-cli php-mbstring php-curl php-xml unzip

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Verify installations
php -v
composer -V

# Install dependencies
composer install

# Run the API
php -S localhost:8000
```

---

## API Usage

**Endpoint:**  
`POST http://localhost:8000/send-email.php`

**Payload Example:**
```json
{
  "toEmail": "user@example.com",
  "toName": "User",
  "subject": "Test Mail",
  "htmlBody": "<h1>Hello</h1>",
  "textBody": "Hello"
}
```

---

## Docker Deployment

### Dockerfile

```Dockerfile
FROM php:8.2-cli

WORKDIR /app

RUN apt-get update && apt-get install -y \
    unzip \
    git \
    libzip-dev \
    && docker-php-ext-install zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer install --no-dev --optimize-autoloader

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000"]
```

### Build and Run

```sh
# Build Docker image
docker build -t php-email-api .

# Run container
docker run \
  -p 8000:8000 \
  --env-file .env \
  php-email-api
```
API available at [http://localhost:8000/send-email.php](http://localhost:8000/send-email.php)

---

## Docker Hub (Optional)

```sh
docker tag php-email-api yourname/php-email-api:latest
docker push yourname/php-email-api:latest
```

---

## Deploy on Shared Hosting

1. Upload to `public_html`:
    ```
    public_html/
    ├── send-email.php
    ├── vendor/
    └── .env
    ```
2. Set PHP version to **8.1** or above.
3. **Enable extensions:** openssl, mbstring, curl
4. Use SMTP credentials from your provider.

**API URL:**  
`https://yourdomain.com/send-email.php`

---

## Production Checklist

- `.env` present and loaded
- `vendor` directory exists
- PHP version verified (8.1+)
- SMTP reachable and working
- HTTPS enabled
- Errors disabled in production

---

## Common Errors & Solutions

- **vendor autoload missing**  
  &rarr; Run `composer install`
- **Undefined MAIL keys**  
  &rarr; Check if `.env` is present and loaded
- **SMTP timeout / connection errors**  
  &rarr; Wrong port or blocked SMTP by host

---

## Notes

- Restart the PHP server after making changes to `.env`
- **Do not commit** `.env` to version control
- Use SMTP app passwords if required by your email provider
# php-email-sender
