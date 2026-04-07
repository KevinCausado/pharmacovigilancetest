# Pharmacovigilance Alert System

A web application for compounding pharmacies to identify and notify customers who purchased medications associated with a specific lot number.

## Stack

- **Backend:** PHP 8.2 / Laravel 11 (REST API)
- **Frontend:** Vue 3 + Vite (SPA)
- **Database:** MySQL
- **Auth:** Laravel Sanctum (token-based)

## Requirements

- PHP 8.2+
- Composer
- Node.js 18+
- MySQL 5.7+

## Setup

**1. Clone and install**

```bash
git clone <repo-url>
cd pharmacovigilance
```

**2. Backend**

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
```

Edit `.env` with your database credentials:

```
DB_DATABASE=pharmacovigilance
DB_USERNAME=root
DB_PASSWORD=
```

Run migrations and seed sample data:

```bash
php artisan migrate --seed
```

Start the server:

```bash
php artisan serve
```

**3. Frontend**

```bash
cd ../frontend
npm install
npm run dev
```

Open `http://localhost:5173`

## Default credentials

| Username | Password | Role |
|----------|----------|------|
| admin | password123 | admin |
| pharmacist1 | password123 | pharmacist |

## How it works

1. Log in with the credentials above
2. Enter lot number `951357` in the search form
3. Optionally filter by date range (defaults to last 30 days)
4. Click **Alert Buyer** on any row to send an email notification
5. Select multiple rows and use **Alert Selected** for bulk alerts
6. View sent alerts under the **Alert Log** menu

> Emails are logged to `backend/storage/logs/laravel.log` instead of being sent — no SMTP setup required.

## Design decisions

- **Separate frontend/backend:** The API and the SPA are independent projects. Laravel serves only JSON, Vue handles all rendering. This makes the projects independently deployable.
- **Sanctum tokens:** Chosen over session auth because it works cleanly with a decoupled SPA without cookie/CSRF complexity.
- **Eager loading:** All API responses use `with()` to avoid N+1 queries on orders, customers and medications.
- **Audit log:** Every alert sent is recorded in the `alerts` table with timestamp, user, customer and lot number — useful for compliance tracking.
- **Vite proxy:** During development, Vite proxies `/api` calls to Laravel so there are no CORS issues and no hardcoded URLs in the frontend code.

## Project structure

```
pharmacovigilance/
├── backend/
│   ├── app/
│   │   ├── Http/Controllers/Api/   # AuthController, OrderController, AlertController...
│   │   ├── Models/                 # User, Customer, Medication, Order, OrderItem, Alert
│   │   └── Mail/                   # PharmaceuticalAlertMail
│   ├── database/
│   │   ├── migrations/             # Table definitions
│   │   └── seeders/                # Sample data including lot 951357
│   └── routes/api.php              # All API routes
└── frontend/
    └── src/
        ├── views/                  # LoginView, OrdersView, OrderDetailView...
        ├── stores/                 # Pinia auth store
        ├── services/api.js         # Axios instance with interceptors
        └── router/                 # Vue Router with auth guards
```
