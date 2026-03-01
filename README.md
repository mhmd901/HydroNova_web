# HydroNova Web (Laravel + Blade)

HydroNova Web is the customer-facing store and admin dashboard for the HydroNova smart hydroponics ecosystem.  
It allows customers to browse hydroponic products and plans, place orders, and interact with an AI assistant.  
Admins can manage products, plans, and customer orders.

## Features
### Customer
- Register / Login (Firebase Authentication)
- Browse products and plans
- Product details (images + optional 3D model links)
- Cart and checkout flow
- Order history and order tracking
- AI Assistant (via n8n + OpenAI API)

### Admin Dashboard
- Admin authentication and access control
- Products CRUD (create/update/delete)
- Plans CRUD + assign products to plans
- Orders management (view/update status)
- Messages/support management (if enabled)

## Tech Stack
- **Laravel (latest)**
- **Blade Templates**
- **Firebase Authentication**
- **Firebase Realtime Database**
- Postman for API testing (JSON requests/responses)

## Architecture Notes
- Follows **MVC** pattern (Controllers / Views / Services).
- Firebase is used for:
  - Email/password authentication
  - Realtime Database storage (products, plans, customers, orders, cart, etc.)
- Arduino does **NOT** communicate with Firebase directly.

---

## Setup & Run Locally

### 1) Clone & install dependencies
```bash
git clone <YOUR_REPO_URL>
cd <YOUR_PROJECT_FOLDER>
composer install
npm install