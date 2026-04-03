# E-Commerce Web App (Laravel + Vue SPA)

A full-stack e-commerce application built with Laravel for the backend and Vue 3 for the SPA frontend.
The project now includes a customer storefront, a separate admin panel, email verification, Google login, product image upload to Firebase Storage, cart and checkout flows, and order management for both users and admins.

## Features

### Customer Storefront

- Browse products in a Vue SPA
- Search products with debounce
- View product stock and pricing
- Adjust quantity with plus/minus controls
- Add items to cart
- Use Buy Now for direct checkout flow
- View uploaded product images

### Cart and Checkout

- Session-based cart for guests and logged-in users
- Update cart quantities
- Remove items from cart
- Cart badge in the navbar
- Checkout protected by authentication and email verification

### Authentication

- Register and login with Laravel Breeze
- Google OAuth2 login with Socialite
- Email verification flow
- Verification notice 

### User Orders

- View order history
- Filter orders by `all`, `pending`, `completed`, and `cancelled`
- Cancel only pending orders
- Display purchased item images

### Admin Panel

- Separate admin layout and navigation
- Admin login redirects into the admin panel
- Manage products from `/admin/products`
- Add, edit, and delete products
- Upload one image per product to Firebase Storage at `products/<product_id>`
- Delete Firebase image when the product is deleted
- Manage orders from `/admin/orders`
- View pending, cancelled, and completed orders
- Mark pending orders as completed

### Realtime / UX

- Product list listens for product update broadcasts
- Order list listens for order status update broadcasts
- Dynamic page titles for auth pages and SPA routes

## Tech Stack

### Backend

- Laravel 
- Laravel Breeze
- Laravel Socialite
- Eloquent ORM
- MySQL
- Pusher-compatible broadcasting

### Frontend

- Vue
- Vue Router
- Axios
- Vite
- Tailwind CSS
- Firebase Web SDK

### External Services

- Google OAuth2
- Firebase Storage
- Pusher Channels

## Main Flows

### Customer Flow

```text
Browse products -> Add to cart / Buy now -> Login for guest user  -> Verify email -> Checkout -> View order history
```

### Admin Flow

```text
Login as admin -> Redirect to admin panel -> Manage products -> Manage orders
```

## Installation

### 1. Clone the repository

```bash
git clone <your-repo-url>
cd ecommerce
```

### 2. Install dependencies

```bash
composer install
npm install
```

### 3. Environment setup

Copy `.env` from `.env.example`, then update the values for:

```env
APP_NAME=E-commerce
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_DATABASE=your_db
DB_USERNAME=root
DB_PASSWORD=

QUEUE_CONNECTION=database
BROADCAST_CONNECTION=pusher

MAIL_MAILER=log

GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
VITE_BROADCAST_DRIVER=pusher

VITE_FIREBASE_API_KEY=
VITE_FIREBASE_AUTH_DOMAIN=
VITE_FIREBASE_PROJECT_ID=
VITE_FIREBASE_STORAGE_BUCKET=
```

### 4. Run migrations

```bash
php artisan migrate:fresh --seed
```

### 5. Start development

Backend:

```bash
php artisan serve
```

Frontend:

```bash
npm run dev
```

### 6. Open the app

```text
http://localhost:8000
```

Use one host consistently in development. Do not switch between `localhost` and `127.0.0.1`, especially for auth, CSRF, and OAuth callbacks.

## Quick Start With Docker

This repo now includes a production-style Docker setup with:

- `nginx`
- `php-fpm`
- `mysql`
- Laravel queue worker
- frontend assets built during image build with `npm run build`

1. Copy the Docker env template:

```bash
cp .env.example .env
```

2. Start the containers:

```bash
docker compose up --build
```

3. Open the app:

```text
App via Nginx: http://localhost:8000
MySQL exposed port: 3307
```

### Docker services

- `app`: PHP-FPM application container
- `queue`: Laravel queue worker
- `nginx`: web server serving the built frontend and Laravel public files
- `mysql`: MySQL 8 database

### Docker Notes

- the frontend is built into the image, so there is no separate Vite dev server in this Docker setup
- the app container runs `php artisan migrate --force` on startup
- it also runs `php artisan db:seed --force` on startup with `APP_SEED=true`
- the seeders are idempotent, so restarting containers does not keep duplicating the seeded products and users
- true database seeding cannot happen during Docker image build because the MySQL container is not available yet, so seeding is done automatically at container startup instead

## Dev Testing Notes

### Email Verification / Reset Password

With `MAIL_MAILER=log`, emails are written to:

- `storage/logs/laravel.log`

For verification and reset links copied from logs:

- use the plain URL, for example:

    ```text
    http://localhost:8000/verify-email/2/13620195ca9e048425a60700517209c13d533256?expires=1775149585&signature=0c3b20ab4e19919b05e42234e1fcb1c1efb777802429a93254409742e951d360
    ```

## Test Accounts

Seeded user:

```text
Email: test@example.com
Password: password
```

Seeded admin:

```text
Email: admin@example.com
Password: password
```

## Database Overview

### Users

- google_id
- name
- email
- password
- role
- email_verified_at

### Products (Soft-delete)

- name
- description
- price
- stock
- image_url
- is_active
- is_delete

### Orders

- user_id
- total_price
- status

### Order Items

- order_id
- product_id
- quantity
- price

## Current Architecture Notes

- Laravel handles auth, sessions, validation, database access, order creation, and admin authorization
- Vue handles storefront pages, cart UI, order UI, and admin SPA pages
- Cart data is stored in Laravel session
- Product images are stored in Firebase Storage, while only the image URL is stored in the database
- Product and order status updates are broadcast so the storefront can refresh in near real time

## Future Improvements

- Product pagination and admin search/filter tools
- Better toast notifications instead of `alert()` / `confirm()`
- Payment gateway integration
- Dashboard metrics for admins
- Automated tests for admin/product/order flows
