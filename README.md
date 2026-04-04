# 🛒 E-Commerce Web App (Laravel + Vue SPA)

A full-stack e-commerce application with Laravel as the backend and Vue 3 as the SPA frontend.

It includes:
- Customer storefront
- Admin panel
- Laravel Breeze authentication
- Google OAuth login
- Email verification
- Firebase Storage image upload
- Cart and checkout flow
- User and admin order management
- Realtime product/order updates via broadcasting

## 📚 Table of Contents

- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [Main Flows](#-main-flows)
- [Run the Project](#-run-the-project)
- [Email Verification / Password Reset Logs](#-email-verification--password-reset-logs)
- [Test Accounts](#-test-accounts)
- [Database Overview](#-database-overview)
- [Architecture Notes](#-architecture-notes)
- [Future Improvements](#-future-improvements)

## ✨ Features

### 🛍️ Customer Storefront
- Browse products in a Vue SPA
- Search products with debounce
- View stock and pricing
- Adjust quantity with plus/minus controls
- Add items to cart
- Use Buy Now for direct checkout
- View product images

### 🛒 Cart & Checkout
- Session-based cart for guests and logged-in users
- Update cart quantities
- Remove items from cart
- Navbar cart badge
- Checkout protected by authentication and email verification

### 🔐 Authentication
- Register/login with Laravel Breeze
- Google OAuth2 login with Socialite
- Email verification flow
- Verification notice support

### 📦 User Orders
- View order history
- Filter by `all`, `pending`, `completed`, `cancelled`
- Cancel pending orders only
- Show purchased item images

### 🛠️ Admin Panel
- Separate admin layout/navigation
- Admin login redirects to admin panel
- Manage products at `/admin/products`
- Add, edit, delete products
- Upload one image per product to Firebase path: `products/<product_id>`
- Delete Firebase image when product is deleted
- Manage orders at `/admin/orders`
- View pending/cancelled/completed orders
- Mark pending orders as completed

### ⚡ Realtime & UX
- Product list listens for product update broadcasts
- Order list listens for order status update broadcasts
- Dynamic page titles for auth pages and SPA routes

## 🧰 Tech Stack

### Backend
- Laravel
- Laravel Breeze
- Laravel Socialite
- Eloquent ORM
- MySQL
- Pusher-compatible broadcasting

### Frontend
- Vue 
- Vite
- Tailwind CSS
- Firebase Web SDK

### External Services
- Google OAuth2
- Firebase Storage
- Pusher Channels (WebSockets)

## 🔄 Main Flows

### 👤 Customer Flow

```text
Browse products -> Add to cart / Buy now -> Login (if guest) -> Verify email -> Checkout -> View order history
```

### 🧑‍💼 Admin Flow

```text
Login as admin -> Redirect to admin panel -> Manage products -> Manage orders -> check customer's view
```

## 🚀 Run the Project

Choose one mode:
- Local mode (PHP + Node on your machine)
- Docker mode (services in containers)

Do not run both modes at the same time.

### 🖥️ Local Mode

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

3. Create local env file:
```bash
cp .env.dev.example .env.dev
```

4. Update `.env.dev`:
```env
APP_ENV=dev
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
npm run dev -- --mode dev
```

7. Open:
```text
http://localhost:8000
```

### 🐳 Docker Mode

Services included:
- `nginx` (web server)
- `app` (Laravel + PHP-FPM)
- `queue` (Laravel queue worker)
- `mysql` (database)

1. Create env file:
```bash
cp .env.example .env
```

2. Update `.env` with Docker DB settings:
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

4. Open:
```text
App: http://localhost:8000
MySQL host port: 3307
```

5. Stop containers:
```bash
docker compose down
```

Optional: remove Docker MySQL volume data:
```bash
docker compose down -v
```

Notes:
- Frontend assets are built during image build (`npm run build`)
- App container runs migrations on startup
- App container seeds on startup when `APP_SEED=true`

## 📩 Email Verification / Password Reset Logs

When `MAIL_MAILER=log`, email links are written to logs.

### Local Mode
```text
storage/logs/laravel.log
```

### Docker Mode 
```bash
docker compose logs -f app
```

Then trigger a verification/password reset action in the app and copy the full URL from logs into your browser.

Note: Use one host consistently during development. Do not switch between `localhost` and `127.0.0.1` for auth/callback flows.

## 🧪 Test Accounts

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

## 🗄️ Database Overview

![Database Design](./db.drawio.png)

### Users
- google_id
- name
- email
- password
- role
- email_verified_at

### Products (Soft Delete)
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

## 🏗️ Architecture Notes

- Laravel handles authentication, session, validation, database access, order creation, and admin authorization
- Vue handles storefront pages, cart UI, orders UI, and admin SPA pages
- Cart data is stored in Laravel session
- Product images are stored in Firebase Storage, while only image URL is stored in the database
- Product and order updates are broadcasted via Pusher for near real-time refresh

## 🧭 Future Improvements

- Product pagination and admin search/filter tools
- Better toast notifications instead of `alert()` / `confirm()`
- Payment gateway integration
- Admin dashboard metrics
- Automated tests for admin/product/order flows
