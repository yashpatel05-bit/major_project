# ☕ Sip N Bite — Smart Café & Food Ordering Platform

An end-to-end full-stack restaurant management and food ordering solution featuring a **Laravel 11 REST API Backend**, a **React Native (Expo) Mobile Application**, and an integrated **AI Café Assistant**.

---

## 📁 Repository Structure

```
major_project/
├── React_Native/
│   └── SipNBite/            # React Native (Expo) Android/iOS Mobile App
│       ├── App.jsx           # App entry & custom capsule navigation
│       ├── src/
│       │   ├── api/          # Axios API client
│       │   ├── components/   # Reusable UI components
│       │   ├── config/       # Dynamic IP & environment configuration
│       │   ├── context/      # Authentication & Cart State
│       │   └── screens/      # Home, Menu, AI Chatbot, Bookings, Orders...
│       └── package.json
│
├── WEB/                     # Laravel 11 Backend & Web Application
│   ├── app/
│   │   ├── Http/Controllers/Api/  # RESTful APIs (Auth, Catalog, Orders, Bookings)
│   │   ├── Http/Controllers/      # ChatbotController (AI Assistant)
│   │   └── Models/               # Eloquent Models
│   ├── routes/api.php        # Mobile endpoints
│   ├── routes/web.php        # Web management dashboard
│   ├── database/migrations/  # Database schemas
│   └── .env.example          # Sample environment configuration
```

---

## 🚀 Key Features

- **📱 Mobile App (React Native + Expo)**:
  - Interactive Menu with search, category filtering & Veg/Non-Veg toggles.
  - Cart & Checkout with Razorpay payment gateway integration and Cash on Delivery.
  - Real-time Order Tracking and Order History.
  - Table Reservation system with real-time status and party size selection.
  - Profile management, saved addresses, and customer feedback.
- **⚙️ Backend (Laravel 11 + MySQL)**:
  - RESTful APIs powering mobile authentication, catalog, orders, and table bookings.
  - Administrative dashboard for menu, table, and order management.
  - Automated distance calculation and geocoding integration.
- **🤖 Sip N Bite AI Assistant**:
  - Context-grounded restaurant assistant using Google Gemini API (`gemini-1.5-flash`).
  - Strict negative guardrails preventing hallucinated dishes or off-topic responses.
  - Offline-resilient local fallback engine directly querying the MySQL database.

---

## 🛠️ Setup & Installation

### 1. Backend (Laravel)
```bash
cd WEB
composer install
cp .env.example .env
php artisan key:generate
# Configure database settings in .env
php artisan migrate --seed
php artisan serve --host=0.0.0.0 --port=8000
```

### 2. Frontend (React Native)
```bash
cd React_Native/SipNBite
npm install
npx expo start
```
Scan the QR code with **Expo Go** on Android/iOS to run the app.
