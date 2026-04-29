# 🏨 Hotel Booking API

A production-ready RESTful API for hotel reservation management built with Laravel. Features a multi-role system with Customers, Hotel Admins, and a Super Admin — complete with room availability checking, conflict-free reservation logic, and Paystack payment integration.

---

## Features

- **Multi-Role Authentication** — Separate auth flows for Customers, Hotel Admins, and Super Admin (via Laravel Sanctum)
- **Hotel Management** — Hotel Admins can create and manage their hotels, room types, and pricing
- **Approval Workflow** — Super Admin reviews and approves or rejects hotel registrations before they go live
- **Room Availability** — Real-time availability checking per hotel and room type
- **Conflict Prevention** — Intersection logic that prevents double-bookings across overlapping date ranges
- **Booking Management** — Full booking lifecycle: create, view, and cancel reservations
- **Paystack Payments** — Initialize and verify payments via Paystack, with webhook support for real-time payment events
- **Versioned API** — All routes are prefixed under `/api/v1/`

---

## Tech Stack

- **Framework:** Laravel (PHP)
- **Authentication:** Laravel Sanctum
- **Payments:** Paystack
- **Dev Environment:** GitHub Codespaces

---

## Getting Started

### Prerequisites

- PHP >= 8.1
- Composer
- MySQL

### Installation

```bash
# Clone the repository
git clone https://github.com/johannesanih/hotelbookingapi.git
cd hotelbookingapi

# Install dependencies
composer install

# Set up environment
cp .env.example .env
php artisan key:generate

# Configure your database and Paystack keys in .env, then run migrations
php artisan migrate

# Start the server
php artisan serve
```

> **Tip:** You can also open this project directly in GitHub Codespaces for a zero-setup experience.

---

## API Reference

All routes are prefixed with `/api/v1/`

---

### Customer Auth

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/customer/register` | Public | Register a new customer |
| POST | `/customer/login` | Public | Login and receive token |
| POST | `/customer/logout` | Required | Logout |
| GET | `/customer/me` | Required | Get authenticated customer |

---

### Hotels (Customer)

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/hotels` | Public | Browse all approved hotels |
| GET | `/hotels/{id}` | Public | View hotel details |
| GET | `/hotels/{id}/roomtype/{room_type_id}/availability` | Public | Check room availability for a date range |

---

### Bookings (Customer)

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/booking/create` | Required | Create a new reservation |
| GET | `/bookings` | Required | List all bookings |
| GET | `/bookings/{id}` | Required | View a booking |
| PUT | `/booking/cancel/{id}` | Required | Cancel a booking |

---

### Payments (Customer)

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/payment/initialize` | Required | Initialize Paystack payment |
| POST | `/payment/verify` | Required | Verify payment after completion |
| GET | `/payment/callback` | Public | Paystack redirect callback |
| POST | `/webhook/paystack` | Public | Paystack webhook handler |

---

### Hotel Admin Auth

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/hotel-admin/register` | Public | Register as a hotel admin |
| POST | `/hotel-admin/login` | Public | Login and receive token |
| POST | `/hotel-admin/logout` | Required | Logout |
| GET | `/hotel-admin/me` | Required | Get authenticated hotel admin |

---

### Hotel Management (Hotel Admin)

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/hotel/create` | Required | Register a new hotel |
| PUT | `/hotel/update/{id}` | Required | Update hotel details |
| DELETE | `/hotel/delete/{id}` | Required | Delete a hotel |

---

### Room Type Management (Hotel Admin)

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/roomtype/create` | Required | Create a room type |
| PUT | `/roomtype/update/{id}` | Required | Update a room type |
| DELETE | `/roomtype/delete/{id}` | Required | Delete a room type |

---

### Pricing (Hotel Admin)

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/price/create` | Required | Set pricing for a room type |

---

### Super Admin

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| PUT | `/hotel/approve/{id}` | Required | Approve a hotel listing |
| PUT | `/hotel/reject/{id}` | Required | Reject a hotel listing |

---

## Architecture Overview

```
Customer
  └── Browse hotels → Check availability → Book room → Pay via Paystack

Hotel Admin
  └── Register hotel → Add room types → Set pricing → Await approval

Super Admin
  └── Review hotel registrations → Approve or Reject

Paystack
  └── Payment initialized → Customer pays → Webhook confirms → Booking confirmed
```

---

## Roadmap

- [x] Customer authentication
- [x] Hotel Admin authentication
- [x] Super Admin approval workflow
- [x] Room availability checking
- [x] Reservation conflict/intersection detection
- [x] Paystack payment integration
- [x] Paystack webhook handler
- [ ] Super Admin dashboard endpoints
- [ ] Email notifications
- [ ] Booking history and reporting

---

## Author

**Johannes Anih**
[GitHub](https://github.com/johannesanih)

---

## License

[MIT](https://opensource.org/licenses/MIT)
