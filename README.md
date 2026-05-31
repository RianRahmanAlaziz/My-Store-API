# Modern Shoes E-Commerce API

Backend API untuk aplikasi E-Commerce Sepatu Modern yang dibangun menggunakan Laravel 13 dan REST API Architecture.


# 🛠️ Tech Stack

* Laravel 13
* PHP 8.3+
* MySQL
* Laravel Sanctum
* REST API
* Eloquent ORM


---

# 📦 Modul yang Sudah Dibuat

## Category Management

CRUD Category

Endpoint:

GET /api/categories

POST /api/categories

GET /api/categories/{id}

PUT /api/categories/{id}

DELETE /api/categories/{id}

---

## Brand Management

CRUD Brand

Endpoint:

GET /api/brands

POST /api/brands

GET /api/brands/{id}

PUT /api/brands/{id}

DELETE /api/brands/{id}

---

## Product Management

CRUD Product

Endpoint:

GET /api/products

POST /api/products

GET /api/products/{id}

PUT /api/products/{id}

DELETE /api/products/{id}

Filter:

GET /api/products?search=nike

GET /api/products?category=running

GET /api/products?brand=nike

GET /api/products?sort=latest

GET /api/products?sort=low-price

GET /api/products?sort=high-price

---

## Product Images

CRUD Product Images

Endpoint:

GET /api/products/{product}/images

POST /api/products/{product}/images

GET /api/products/{product}/images/{image}

PUT /api/products/{product}/images/{image}

DELETE /api/products/{product}/images/{image}

---

## Product Variants

CRUD Product Variants

Endpoint:

GET /api/products/{product}/variants

POST /api/products/{product}/variants

GET /api/products/{product}/variants/{variant}

PUT /api/products/{product}/variants/{variant}

DELETE /api/products/{product}/variants/{variant}

---

# 🗄️ Database Structure

## categories

| Field | Type   |
| ----- | ------ |
| id    | bigint |
| name  | string |
| slug  | string |

---

## brands

| Field | Type   |
| ----- | ------ |
| id    | bigint |
| name  | string |
| slug  | string |

---

## products

| Field          | Type    |
| -------------- | ------- |
| id             | bigint  |
| category_id    | bigint  |
| brand_id       | bigint  |
| name           | string  |
| slug           | string  |
| sku            | string  |
| price          | decimal |
| original_price | decimal |
| description    | text    |
| is_new         | boolean |
| is_trending    | boolean |
| is_best_seller | boolean |
| is_active      | boolean |

---

## product_images

| Field      | Type    |
| ---------- | ------- |
| id         | bigint  |
| product_id | bigint  |
| image      | string  |
| is_main    | boolean |

---

## product_variants

| Field      | Type    |
| ---------- | ------- |
| id         | bigint  |
| product_id | bigint  |
| size       | string  |
| color      | string  |
| stock      | integer |

---

# 🔗 Relationships

Category

1 Category → Many Products

Brand

1 Brand → Many Products

Product

1 Product → Many Images

1 Product → Many Variants

---

# 🚀 Installation

Install dependency

composer install

Copy env

cp .env.example .env

Generate key

php artisan key:generate

Migrasi database

php artisan migrate

Seeder data

php artisan db:seed

Jalankan server

php artisan serve

---

# 📌 Roadmap

## Phase 1 (Selesai)

* [x] Category CRUD
* [x] Brand CRUD
* [x] Product CRUD
* [x] Product Image CRUD
* [x] Product Variant CRUD
* [x] API Resources
* [x] Request Validation


## Phase 2 (Selesai)

* [x] Authentication (Sanctum)
* [x] Register
* [x] Login
* [x] Logout
* [x] User Profile

## Phase 3 (Selesai)

* [x] Cart Management
* [x] Wishlist
* [x] Checkout
* [x] Shipping Address

## Phase 4

* [x] Order Management
* [x] Admin Middleware
* [ ] Payment Integration
* [ ] Invoice
* [ ] Order History

## Phase 5

* [ ] Product Review & Rating
* [ ] Dashboard Analytics
* [ ] Sales Report

---

# 👨‍💻 Developer

Rian Rahman Al Aziz

Programmer & Full Stack Developer

E-Commerce Project
