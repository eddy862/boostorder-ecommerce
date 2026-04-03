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

## Run The Project

Choose one mode:

- Local mode (PHP + Node running directly on your machine)
- Docker mode (all services in containers)

Do not run both modes at the same time.

### Local Mode

1. Clone and enter the project:

```bash
git clone <your-repo-url>
cd ecommerce
```

2. Install dependencies:

```bash
composer install
npm install
```

3. Create your env file:

```bash
cp .env.example .env
```

4. Update `.env` for local database and app URL:

```env
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_db
DB_USERNAME=root
DB_PASSWORD=

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

5. Run migrations and seeders:

```bash
php artisan migrate:fresh --seed
```

6. Start backend and frontend in separate terminals:

```bash
php artisan serve
```

```bash
npm run dev
```

7. Open the app:

```text
http://localhost:8000
```

### Docker Mode

This setup includes:

- `nginx` (web server)
- `app` (Laravel + PHP-FPM)
- `queue` (Laravel queue worker)
- `mysql` (database)

1. Create your env file:

```bash
cp .env.example .env
```

2. Use Docker DB settings in `.env`:

```env
APP_URL=http://localhost:8000

DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=ecommerce_db
DB_USERNAME=root
DB_PASSWORD=root

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

3. Build and start containers:

```bash
docker compose up --build -d
```

4. Open the app:

```text
App: http://localhost:8000
MySQL host port: 3307
```

5. Stop containers:

```bash
docker compose down
```

Notes:

- frontend assets are built during Docker image build (`npm run build`)
- app container runs migrations on startup
- app container seeds on startup when `APP_SEED=true`

## Email Verification / Password Reset Logs

When `MAIL_MAILER=log`, email links are written to logs.

### Local Mode

Check:

```text
storage/logs/laravel.log
```

### Docker Mode (recommended)

Stream logs from the app container:

```bash
docker compose logs -f app
```

Then trigger "send verification email" in the app and copy the full URL from the log output into your browser.

Use one host consistently in development. Do not switch between `localhost` and `127.0.0.1`, especially for auth, CSRF, and OAuth callbacks.

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

![database design](./db.drawio.png)

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
