# 📋 Sip N Bite — Complete Project Data Flow Documentation

> This document explains the entire data flow of the **Sip N Bite** project — from the MySQL database all the way to the user-facing screens on both the **WEB** (Laravel + Blade) and **ANDROID_INTERNAL** (Kotlin) platforms, and how these two platforms share data through a common backend.

---

## 📁 Project Structure Overview

```
SIP_N_BITE/
├── WEB/                    ← Laravel 12 Backend + Web Frontend (Blade)
│   ├── app/
│   │   ├── Models/         ← Eloquent ORM Models (12 models)
│   │   └── Http/
│   │       └── Controllers/
│   │           ├── Admin/      ← Admin Dashboard Controllers (Web)
│   │           ├── Customer/   ← Customer-facing Controllers (Web)
│   │           ├── Api/        ← REST API Controllers (for Android)
│   │           └── Auth/       ← Authentication Controllers (Web)
│   ├── database/
│   │   └── migrations/     ← 14 migration files (DB schema)
│   ├── routes/
│   │   ├── web.php         ← Web routes (Admin + Customer pages)
│   │   └── api.php         ← REST API routes (for Android app)
│   └── resources/views/    ← Blade templates (UI)
│
├── ANDROID_INTERNAL/       ← Android App (Kotlin, Staff/Internal Use)
│   └── app/src/main/java/com/example/myapplication/
│       ├── models/         ← Kotlin data classes (mirrors DB schema)
│       ├── utils/          ← ApiClient, SessionManager, CartManager
│       ├── adapters/       ← RecyclerView adapters
│       └── *.kt            ← Activities & Fragments (UI screens)
│
├── ANDROID/                ← Android App shell (customer-facing placeholder)
└── Documentation/          ← Project documentation & diagrams
```

---

## 🗄️ Layer 1: Database (MySQL)

**Connection Config** (from `WEB/.env`):
- **Engine:** MySQL
- **Host:** `127.0.0.1:3306`
- **Database:** `sipnbite`

### Database Tables (14 Migrations)

| # | Table | Purpose | Key Columns |
|---|-------|---------|-------------|
| 1 | `users` | Customer & Admin accounts | id, name, email, password, phone, address, role (`admin`/`customer`), status (`active`/`inactive`) |
| 2 | `categories` | Food/Drink categories | id, name, slug, image, description, status |
| 3 | `menu_items` | Food & beverage items | id, category_id (FK), name, description, price, image, is_available, is_veg, rating |
| 4 | `tables` | Restaurant table inventory | id, table_number, capacity, location_type, status |
| 5 | `table_bookings` | Table reservations | id, user_id (FK), table_id (FK), booking_date, booking_time, guests_count, special_request, status |
| 6 | `delivery_persons` | Delivery staff profiles | id, name, phone, vehicle_number, status (`available`/`on_delivery`) |
| 7 | `addresses` | User's delivery addresses | id, user_id (FK), title, address_line, city, pincode, latitude, longitude, is_default |
| 8 | `orders` | Customer orders | id, order_number, user_id (FK), delivery_person_id (FK), address_id (FK), subtotal, tax, delivery_fee, total_amount, payment_method, payment_status, order_status, notes |
| 9 | `order_items` | Items within an order | id, order_id (FK), menu_item_id (FK), item_name, price, quantity, subtotal |
| 10 | `payments` | Payment transaction records | id, order_id (FK), user_id (FK), razorpay_payment_id, razorpay_order_id, amount, payment_method, status |
| 11 | `feedback` | Customer ratings & reviews | id, user_id (FK), order_id (FK), rating, comment |
| 12 | `settings` | Café configuration (name, address, etc.) | id, key, value |
| 13 | `sessions` | Laravel session storage | id, user_id, ip_address, user_agent, payload |
| 14 | `cache` / `jobs` | Laravel framework tables | (internal use) |

### Entity Relationship Summary

```
users ──┬── 1:N ──→ orders ──┬── 1:N ──→ order_items
        │                    ├── 1:1 ──→ payments
        │                    ├── 1:1 ──→ feedback
        │                    ├── N:1 ──→ delivery_persons
        │                    └── N:1 ──→ addresses
        │
        ├── 1:N ──→ table_bookings ──→ N:1 ──→ tables
        ├── 1:N ──→ addresses
        └── 1:N ──→ feedback

categories ── 1:N ──→ menu_items
```

---

## ⚙️ Layer 2: Eloquent ORM Models (`WEB/app/Models/`)

Laravel's Eloquent ORM sits between the database and the controllers. Each model maps to one database table and defines:
- **Fillable fields** (mass-assignment protection)
- **Relationships** (belongsTo, hasMany, hasOne)
- **Type casts** (e.g., price → float, is_veg → boolean)

### Key Models & Their Relationships

| Model File | Table | Relationships Defined |
|-----------|-------|----------------------|
| `User.php` | users | hasMany → Orders, TableBookings, Addresses |
| `Category.php` | categories | hasMany → MenuItems |
| `MenuItem.php` | menu_items | belongsTo → Category |
| `Order.php` | orders | belongsTo → User, DeliveryPerson, Address; hasMany → OrderItems; hasOne → Payment, Feedback |
| `OrderItem.php` | order_items | belongsTo → Order, MenuItem |
| `Payment.php` | payments | belongsTo → Order, User |
| `TableBooking.php` | table_bookings | belongsTo → User, Table |
| `Table.php` | tables | hasMany → TableBookings |
| `DeliveryPerson.php` | delivery_persons | hasMany → Orders |
| `Address.php` | addresses | belongsTo → User |
| `Feedback.php` | feedback | belongsTo → User, Order |
| `Setting.php` | settings | standalone key-value |

### How Eloquent Fetches Data (Example)

```php
// In CatalogApiController → getMenuItems()
$items = MenuItem::with('category')         // Eager load relationship
    ->where('is_available', true)           // Filter condition
    ->where('category_id', $categoryId)     // Optional filter
    ->orderBy('rating', 'desc')             // Sort
    ->get();                                // Execute SQL → returns Collection
```

**SQL generated behind the scenes:**
```sql
SELECT * FROM menu_items WHERE is_available = 1 AND category_id = ? ORDER BY rating DESC;
SELECT * FROM categories WHERE id IN (?);   -- eager loaded
```

---

## 🌐 Layer 3A: WEB Data Flow (Laravel Blade — Server-Side Rendering)

The web application follows Laravel's classic **MVC pattern**: the browser sends an HTTP request → Laravel routes it to a Controller → the Controller queries Models → the Model returns data → the Controller passes data to a Blade View → HTML is rendered and sent back to the browser.

### Web Routes Architecture

| Route Group | Prefix | Middleware | Controller Namespace |
|------------|--------|-----------|---------------------|
| Authentication | `/login`, `/register`, `/logout` | None | `Auth\` |
| Customer Pages | `/`, `/menu`, `/cart`, `/checkout`, etc. | `auth` (some) | `Customer\` |
| Admin Dashboard | `/admin/*` | `auth` | `Admin\` |

### Complete Web Data Flow (Per Feature)

---

#### 🏠 Homepage (`GET /`)

```
Browser Request → web.php route → HomeController@index
                                        │
                    ┌───────────────────┤
                    ▼                   ▼                   ▼
            Category::where()    MenuItem::where()    Feedback::with('user')
            →take(6)→get()       →orderBy('rating')   →where('rating','>=',4)
                                 →take(8)→get()       →latest()→take(3)→get()
                    │                   │                   │
                    └───────┬───────────┘                   │
                            ▼                               │
                    view('customer.home',                    │
                        compact('categories',               │
                                'popularItems',             │
                                'feedbacks'))  ◄─────────────┘
                            │
                            ▼
                    Blade renders HTML
                    → sent to browser
```

**Controller:** `Customer\HomeController@index`
**View:** `resources/views/customer/home.blade.php`
**Data passed:** `$categories`, `$popularItems`, `$feedbacks`

---

#### 🍔 Menu Page (`GET /menu`)

```
Browser Request (+ optional ?category=X&search=Y&type=veg)
        │
        ▼
    web.php → MenuController@index
        │
        ├─→ Category::where('status','active')->get()          → $categories
        ├─→ MenuItem::where('is_available',true)
        │       →where('category_id', $request->category)      (if filter set)
        │       →where('name','like','%search%')                (if search set)
        │       →where('is_veg', true/false)                    (if type set)
        │       →latest()->get()                                → $menuItems
        │
        ▼
    view('customer.menu', compact('categories', 'menuItems'))
        │
        ▼
    Blade renders menu grid with category filter chips
```

---

#### 🛒 Cart System (Session-Based on Web)

```
POST /cart/add  →  CartController@add
    │
    ├─→ MenuItem::findOrFail($id)           ← fetch item from DB
    ├─→ session()->get('cart', [])           ← read existing cart from session
    ├─→ Add/increment item in cart array
    └─→ session()->put('cart', $cart)        ← save back to session
         │
         ▼
    redirect back with success flash

GET /cart  →  CartController@index
    │
    ├─→ session()->get('cart', [])
    ├─→ Calculate: subtotal, tax (5%), delivery (₹40), total
    └─→ view('customer.cart', compact(...))
```

> **Important:** On WEB, the cart is stored in the **Laravel session** (database driver), NOT in the MySQL `orders` table. The cart only becomes an order during checkout.

---

#### 💳 Checkout & Order Placement (Web)

```
GET /checkout → CheckoutController@index
    │
    ├─→ session()->get('cart')              ← cart from session
    ├─→ Address::where('user_id', Auth::id())  ← user's saved addresses
    ├─→ Calculate totals
    └─→ view('customer.checkout', compact(...))
         │
         │ (User fills form, selects payment method)
         │
POST /checkout → CheckoutController@placeOrder
    │
    ├─→ Validate request (address_id, payment_method)
    ├─→ Calculate: subtotal, tax, delivery_fee, total
    ├─→ Order::create([...])                ← INSERT into orders table
    ├─→ OrderItem::create([...])            ← INSERT order_items (loop)
    ├─→ Payment::create([...])              ← INSERT payment (if Razorpay)
    ├─→ session()->forget('cart')           ← clear cart session
    └─→ redirect to order tracking page
```

---

#### 👤 Admin Dashboard (`GET /admin/dashboard`)

```
Browser → web.php (auth middleware) → DashboardController@index
    │
    ├─→ Order::count()                                → $totalOrders
    ├─→ Order::where('payment_status','paid')->sum()  → $totalRevenue
    ├─→ TableBooking::whereIn('status',[...])->count() → $activeBookings
    ├─→ DeliveryPerson::where('status','on_delivery') → $activeDeliveryStaff
    ├─→ User::where('role','customer')->count()       → $totalCustomers
    ├─→ MenuItem::count()                             → $totalMenuItems
    ├─→ Order::with(['user','deliveryPerson'])->latest()->take(6)
    ├─→ TableBooking::with(['user','table'])->latest()->take(5)
    ├─→ Feedback::with('user')->latest()->take(4)
    │
    └─→ view('admin.dashboard', compact(...))
```

---

#### 📦 Admin Order Management

```
GET  /admin/orders       → AdminOrderController@index    (list all orders)
GET  /admin/orders/{id}  → AdminOrderController@show     (single order detail)
POST /admin/orders/{id}/status → AdminOrderController@updateStatus
    │
    ├─→ Update order_status (Pending → Preparing → Out for Delivery → Delivered)
    ├─→ Assign delivery_person_id (manual assignment by Admin)
    ├─→ Update DeliveryPerson status (available ↔ on_delivery)
    └─→ If Delivered + COD → set payment_status = 'paid'
```

> **Key Design Decision:** Delivery person assignment is **manual** — done by Admin through the dashboard, NOT auto-assigned.

---

## 📱 Layer 3B: ANDROID_INTERNAL Data Flow (Kotlin — REST API Client)

The Android app does **NOT** connect to the MySQL database directly. Instead, it communicates with the **Laravel backend** via **REST API endpoints** (`/api/*` routes). Data flows as JSON over HTTP.

### Android Architecture Layers

```
┌─────────────────────────────────────────────────────────┐
│                    UI LAYER (Screens)                    │
│  LoginActivity │ MainActivity │ CartActivity │ etc.     │
│  HomeFragment  │ MenuFragment │ BookingFragment          │
│  OrdersFragment │ ProfileFragment                        │
├─────────────────────────────────────────────────────────┤
│                 ADAPTER LAYER                            │
│  CategoryAdapter │ MenuItemAdapter │ CartAdapter         │
│  OrderAdapter                                            │
├─────────────────────────────────────────────────────────┤
│                 UTILITY LAYER                            │
│  ApiClient (HTTP)  │  SessionManager  │  CartManager     │
│  (Singleton)       │  (SharedPrefs)   │  (In-Memory)     │
├─────────────────────────────────────────────────────────┤
│                DATA MODEL LAYER                          │
│  User │ Category │ MenuItem │ CartItem │ Order           │
│  TableBooking │ DeliveryPerson │ UserAddress             │
│  OrderItemData │ RestaurantTable │ ApiResponse           │
├─────────────────────────────────────────────────────────┤
│              NETWORK (HTTP over WiFi/Data)               │
│         ↕ JSON Request/Response ↕                        │
├─────────────────────────────────────────────────────────┤
│        LARAVEL BACKEND (WEB/routes/api.php)              │
│              ↕ Eloquent ORM ↕                            │
├─────────────────────────────────────────────────────────┤
│              MySQL DATABASE (sipnbite)                   │
└─────────────────────────────────────────────────────────┘
```

### ApiClient — The Network Bridge

**File:** `ANDROID_INTERNAL/app/src/.../utils/ApiClient.kt`

This is a **singleton** object that handles ALL HTTP communication:

```kotlin
object ApiClient {
    var BASE_URL = "http://10.0.2.2:8000/api"   // Emulator → localhost

    // Core HTTP method (used by all other methods)
    private fun makeHttpRequest(
        endpoint: String,                        // e.g., "/login"
        method: String,                          // "GET" or "POST"
        jsonBody: String? = null,                // JSON payload
        callback: ApiCallback<String>            // async callback
    )
    // Implementation:
    // 1. Opens HttpURLConnection on background thread (Executors)
    // 2. Sets Content-Type: application/json
    // 3. Sends request body (if POST)
    // 4. Reads response (inputStream or errorStream)
    // 5. Posts result back to Main thread via Handler
}
```

**Base URL:** `http://10.0.2.2:8000/api`
- `10.0.2.2` is the Android emulator's alias for the host machine's `localhost`
- This means the Android app hits the **same Laravel server** running on `localhost:8000`
- Users can change this URL from the Profile screen (stored in SharedPreferences)

### Android Data Flow Per Feature

---

#### 🔐 Login Flow

```
LoginActivity
    │
    │ User enters email + password, taps "Login"
    │
    ▼
ApiClient.login(email, password, callback)
    │
    ├─→ POST http://10.0.2.2:8000/api/login
    │   Body: { "email": "...", "password": "..." }
    │
    ├─→ Laravel routes/api.php → AuthApiController@login
    │       │
    │       ├─→ User::where('email', $email)->first()      ← DB query
    │       ├─→ Verify password (plain-text comparison)
    │       ├─→ Check user status != 'inactive'
    │       └─→ Return JSON: { success: true, data: { id, name, email, ... } }
    │
    ├─→ ApiClient parses JSON → creates User data class
    │
    ▼
SessionManager.createLoginSession(user)
    │
    ├─→ Stores in SharedPreferences:
    │     isLoggedIn = true
    │     userId = user.id
    │     userName = user.name
    │     userEmail = user.email
    │     userPhone = user.phone
    │     userRole = user.role
    │
    ▼
Navigate to MainActivity
```

---

#### 🏠 Home Screen Data Flow

```
HomeFragment.onCreateView()
    │
    ├─→ SessionManager.getUser()          ← read from SharedPreferences
    │   → Display "Welcome, {name}! 👋"
    │
    ├─→ ApiClient.getCategories(callback)
    │       │
    │       ├─→ GET /api/categories
    │       ├─→ CatalogApiController@getCategories
    │       │       └─→ Category::where('status','active')->get()
    │       │           └─→ JSON: { success: true, data: [...] }
    │       │
    │       └─→ Parse JSON → List<Category>
    │           → Set CategoryAdapter on rvHomeCategories
    │
    └─→ ApiClient.getMenuItems(categoryId=null, callback)
            │
            ├─→ GET /api/menu
            ├─→ CatalogApiController@getMenuItems
            │       └─→ MenuItem::with('category')
            │           ->where('is_available', true)
            │           ->orderBy('rating', 'desc')->get()
            │           └─→ JSON: { success: true, data: [...] }
            │
            └─→ Parse JSON → List<MenuItem>
                → Set MenuItemAdapter on rvHomeFeatured
```

---

#### 🍔 Menu Screen Data Flow

```
MenuFragment.onCreateView()
    │
    ├─→ loadCategories()
    │       └─→ GET /api/categories → List<Category>
    │           → CategoryAdapter (horizontal RecyclerView)
    │           → On category tap → loadMenuItems(selectedCategoryId)
    │
    └─→ loadMenuItems(categoryId)
            └─→ GET /api/menu?category_id=X → List<MenuItem>
                → MenuItemAdapter (vertical RecyclerView)
                → Each item has +/- buttons → CartManager.addItem() / removeItem()
```

---

#### 🛒 Cart & Checkout (Android)

```
User taps + on menu item
    │
    ▼
CartManager.addItem(menuItem)              ← IN-MEMORY list (not persisted)
    │
    ├─→ If item exists in list → quantity++
    └─→ Else → add new CartItem(menuItem, quantity=1)

User taps Cart icon on toolbar
    │
    ▼
CartActivity.onCreate()
    │
    ├─→ CartManager.getCartItems()         ← read in-memory list
    ├─→ CartManager.getSubtotal()          ← sum(price * quantity)
    ├─→ CartManager.getTax()               ← subtotal * 0.05
    ├─→ CartManager.getDeliveryFee()       ← ₹40 fixed
    ├─→ CartManager.getTotal()             ← subtotal + tax + delivery
    ├─→ Display in CartAdapter (RecyclerView)
    │
    │ User selects payment (COD / Razorpay) and taps "Place Order"
    │
    ▼
ApiClient.checkout(userId, addressId, paymentMethod, razorpayId, notes, items, callback)
    │
    ├─→ POST /api/checkout
    │   Body: {
    │     "user_id": 1,
    │     "payment_method": "COD",
    │     "items": [
    │       { "menu_item_id": 5, "item_name": "...", "price": 199, "quantity": 2, "subtotal": 398 }
    │     ]
    │   }
    │
    ├─→ Laravel: OrderApiController@checkout
    │       ├─→ Calculate subtotal, tax, delivery_fee, total
    │       ├─→ Order::create([...])           ← INSERT INTO orders
    │       ├─→ OrderItem::create([...])       ← INSERT INTO order_items (loop)
    │       ├─→ Payment::create([...])         ← INSERT INTO payments (if Razorpay)
    │       └─→ $order->load(['items','deliveryPerson','address'])
    │           └─→ JSON: { success: true, data: { id, order_number, ... } }
    │
    ├─→ Parse JSON → Order data class
    │
    ▼
CartManager.clear()                        ← empty in-memory cart
Navigate to OrderTrackingActivity(ORDER_ID)
```

---

#### 📦 Order Tracking (Android)

```
OrderTrackingActivity.onCreate()
    │
    ├─→ orderId = intent.getIntExtra("ORDER_ID", 0)
    │
    └─→ ApiClient.getOrderDetails(orderId, callback)
            │
            ├─→ GET /api/orders/{orderId}
            ├─→ OrderApiController@getOrderDetails
            │       └─→ Order::with(['items','deliveryPerson','address','payment','feedback'])
            │           ->find($id)
            │           └─→ JSON with all nested relationships
            │
            └─→ Parse JSON → Order (with items, delivery_person, address)
                │
                ├─→ Display order_number, order_status, total_amount
                ├─→ Display delivery person name/phone (if assigned)
                └─→ Display order items list

    btnRefreshTracking.onClick → re-fetch order details
    (User can poll to see status updates made by Admin)
```

---

#### 🪑 Table Booking (Android)

```
BookingFragment
    │
    │ User fills: date, time, guest count, special request
    │
    ▼
ApiClient.createBooking(userId, date, time, guests, request, callback)
    │
    ├─→ POST /api/bookings
    │   Body: { "user_id": 1, "booking_date": "2026-09-10", "booking_time": "19:30", "guests_count": 4 }
    │
    ├─→ BookingApiController@createBooking
    │       └─→ TableBooking::create([...])     ← INSERT INTO table_bookings
    │           → status defaults to "pending"
    │           → Admin confirms/assigns table later via Web dashboard
    │
    └─→ JSON: { success: true, data: { id, booking_date, status: "Pending" } }
        → Toast: "Table Reserved Successfully!"
```

---

## 🔗 How Android and Web Connect Together (Shared Data)

### The Bridge: Laravel REST API

The Android app and Web frontend are **two separate clients** that both connect to the **same Laravel backend** and the **same MySQL database**. They share data through this common backend.

```
┌──────────────────┐         ┌──────────────────────────────┐         ┌──────────────────┐
│                  │         │                              │         │                  │
│   WEB (Browser)  │◄───────►│     LARAVEL BACKEND          │◄───────►│  ANDROID APP     │
│   Blade Views    │  HTTP   │     (WEB/ folder)            │  HTTP   │  (ANDROID_       │
│   Session-based  │  HTML   │                              │  JSON   │  INTERNAL/)      │
│   Auth           │         │  ┌──────────────────────┐   │  REST   │  SharedPrefs     │
│                  │         │  │  MySQL DB (sipnbite)  │   │  API    │  Auth             │
│  Port: 8000      │         │  └──────────────────────┘   │         │                  │
│  Routes: web.php │         │                              │         │  Base URL:        │
│                  │         │  Routes:                      │         │  10.0.2.2:8000   │
│                  │         │  ├── web.php (HTML pages)     │         │  Routes: api.php │
│                  │         │  └── api.php (JSON APIs)      │         │                  │
└──────────────────┘         └──────────────────────────────┘         └──────────────────┘
```

### Shared Data Points — What's Connected

| Data Entity | Created By | Visible On | How They Share |
|------------|-----------|------------|----------------|
| **Users** | Web (Register page) OR Android (RegisterActivity) | Both platforms | Same `users` table; user registered on Android can log into Web and vice versa |
| **Categories** | Admin Web Dashboard | Web menu page + Android HomeFragment/MenuFragment | Admin creates categories via Web → Android fetches via `GET /api/categories` |
| **Menu Items** | Admin Web Dashboard | Web menu page + Android HomeFragment/MenuFragment | Admin adds items via Web → Android fetches via `GET /api/menu` |
| **Orders** | Web (CheckoutController) OR Android (CartActivity) | Both platforms | Orders placed from Android appear in Admin Web dashboard; orders from Web are also visible |
| **Order Status Updates** | Admin Web Dashboard | Android OrderTrackingActivity | Admin updates status on Web → Android user refreshes to see update via `GET /api/orders/{id}` |
| **Table Bookings** | Web (BookingController) OR Android (BookingFragment) | Admin Web Dashboard | Bookings from both platforms go to same `table_bookings` table |
| **Delivery Person Assignment** | Admin Web Dashboard ONLY | Android OrderTrackingActivity | Admin assigns delivery person on Web → Android sees it via order detail API |
| **Payments** | Created during checkout on both platforms | Admin Web Dashboard (Payments section) | Both platforms create payment records in same `payments` table |
| **Feedback** | Web (Customer) OR Android (could be added) | Admin Web Dashboard | Stored in same `feedback` table |
| **Addresses** | Web (ProfileController) OR Android (API available) | Both platforms during checkout | Stored in same `addresses` table |

### Real-World Data Sharing Scenarios

#### Scenario 1: Order Placed on Android → Managed on Web

```
1. Customer opens Android app → browses menu → adds items to cart
2. Customer taps "Place Order" in CartActivity
3. Android sends POST /api/checkout to Laravel backend
4. Laravel creates Order + OrderItems + Payment in MySQL
5. Admin opens Web browser → /admin/orders
6. Admin sees the new order (same MySQL data)
7. Admin updates order_status to "Preparing" → POST /admin/orders/{id}/status
8. Admin assigns a delivery person
9. Customer opens OrderTrackingActivity on Android → taps "Refresh"
10. Android sends GET /api/orders/{id}
11. Customer sees: status = "Preparing", delivery person = "Ramesh, +91-XXXXX"
```

#### Scenario 2: User Registered on Android → Logs into Web

```
1. User opens Android app → RegisterActivity
2. Fills name, email, password → POST /api/register
3. Laravel creates user in `users` table (role = 'customer')
4. User opens browser → /login
5. Enters same email + password
6. Laravel LoginController verifies against same `users` table
7. User is logged in on Web with all their data
```

#### Scenario 3: Admin Adds Menu Item → Appears on Android

```
1. Admin opens Web → /admin/menu → adds "Masala Chai" (₹49, Veg)
2. Laravel stores in `menu_items` table
3. Customer opens Android app → HomeFragment calls getMenuItems()
4. ApiClient sends GET /api/menu
5. Laravel CatalogApiController queries MenuItem::where('is_available', true)
6. "Masala Chai" appears in the Android app's menu list
```

---

## 🔀 Key Differences Between Web and Android Data Handling

| Aspect | WEB (Browser) | ANDROID_INTERNAL (App) |
|--------|--------------|----------------------|
| **Rendering** | Server-side (Blade templates) | Client-side (XML layouts + Kotlin) |
| **Data Format** | PHP variables passed to Blade views | JSON parsed into Kotlin data classes |
| **Authentication** | Laravel session cookies (`sessions` DB table) | SharedPreferences (`SessionManager`) |
| **Cart Storage** | PHP session (`session()->get('cart')`) | In-memory list (`CartManager` singleton) |
| **API Communication** | Direct Eloquent queries (server-side) | HTTP REST calls via `ApiClient` |
| **Route Files** | `routes/web.php` (returns HTML views) | `routes/api.php` (returns JSON) |
| **Controllers** | `Customer\*Controller`, `Admin\*Controller` | `Api\*Controller` |
| **Middleware** | `auth` (session-based) | None (user_id passed in request body) |
| **Cart Persistence** | Survives page reload (session) | Lost on app kill (in-memory) |
| **Base URL Config** | Server-side (no URL needed) | Configurable in Profile (`etBaseUrl`) |

---

## 📡 Complete API Endpoint Reference

All endpoints are prefixed with `/api` and return JSON with the structure:
```json
{ "success": true/false, "message": "...", "data": { ... } }
```

### Authentication APIs

| Method | Endpoint | Controller | Purpose |
|--------|----------|-----------|---------|
| POST | `/api/register` | `AuthApiController@register` | Create new user account |
| POST | `/api/login` | `AuthApiController@login` | Authenticate user |
| POST | `/api/reset-password` | `AuthApiController@resetPassword` | Reset password by email |
| GET | `/api/profile/{id}` | `AuthApiController@getProfile` | Get user profile with addresses |
| POST | `/api/profile/{id}` | `AuthApiController@updateProfile` | Update name, phone, address, password |

### Catalog APIs

| Method | Endpoint | Controller | Purpose |
|--------|----------|-----------|---------|
| GET | `/api/categories` | `CatalogApiController@getCategories` | List active categories |
| GET | `/api/menu` | `CatalogApiController@getMenuItems` | List available menu items (filterable) |
| GET | `/api/menu/{id}` | `CatalogApiController@getMenuItem` | Single menu item detail |

### Order APIs

| Method | Endpoint | Controller | Purpose |
|--------|----------|-----------|---------|
| POST | `/api/checkout` | `OrderApiController@checkout` | Place new order |
| POST | `/api/verify-payment` | `OrderApiController@verifyPayment` | Verify Razorpay payment |
| GET | `/api/orders/user/{userId}` | `OrderApiController@getUserOrders` | List user's orders |
| GET | `/api/orders/{id}` | `OrderApiController@getOrderDetails` | Single order detail with relations |

### Table Booking APIs

| Method | Endpoint | Controller | Purpose |
|--------|----------|-----------|---------|
| GET | `/api/tables` | `BookingApiController@getTables` | List available tables |
| POST | `/api/bookings` | `BookingApiController@createBooking` | Create table reservation |
| GET | `/api/bookings/user/{userId}` | `BookingApiController@getUserBookings` | List user's bookings |
| POST | `/api/bookings/{id}/cancel` | `BookingApiController@cancelBooking` | Cancel a reservation |

### Address APIs

| Method | Endpoint | Controller | Purpose |
|--------|----------|-----------|---------|
| GET | `/api/addresses/user/{userId}` | `AddressApiController@getUserAddresses` | List user's addresses |
| POST | `/api/addresses` | `AddressApiController@storeAddress` | Add new address |
| DELETE | `/api/addresses/{id}` | `AddressApiController@deleteAddress` | Delete an address |

### Other APIs

| Method | Endpoint | Controller | Purpose |
|--------|----------|-----------|---------|
| POST | `/api/feedback` | `FeedbackApiController@submitFeedback` | Submit rating & review |
| POST | `/api/calculate-distance` | `DistanceApiController@calculateDistance` | Calculate delivery distance (Google Maps / fallback mock) |

---

## 🔄 Data Flow Summary Diagram

```
╔═══════════════════════════════════════════════════════════════════════════════╗
║                                                                               ║
║   👤 CUSTOMER (Android App)          👤 CUSTOMER (Web Browser)                ║
║   ┌─────────────────────┐            ┌─────────────────────────┐              ║
║   │ LoginActivity       │            │ /login (Blade form)     │              ║
║   │ HomeFragment        │            │ / (Homepage)            │              ║
║   │ MenuFragment        │            │ /menu (Menu page)       │              ║
║   │ CartActivity        │            │ /cart (Session cart)     │              ║
║   │ BookingFragment     │            │ /checkout (Place order)  │              ║
║   │ OrdersFragment      │            │ /my-orders (Order list)  │              ║
║   │ OrderTrackingActivity│            │ /table-booking           │              ║
║   │ ProfileFragment     │            │ /profile                 │              ║
║   └─────────┬───────────┘            └────────────┬────────────┘              ║
║             │                                     │                           ║
║             │ HTTP REST (JSON)                     │ HTTP (HTML + Forms)       ║
║             │ via ApiClient                        │ via Browser               ║
║             │                                     │                           ║
║   ╔═════════▼═════════════════════════════════════▼══════════════════╗        ║
║   ║                    LARAVEL BACKEND                               ║        ║
║   ║                                                                  ║        ║
║   ║   routes/api.php ─────► Api/*Controller ──────┐                  ║        ║
║   ║   routes/web.php ─────► Customer/*Controller ──┤                 ║        ║
║   ║                   ─────► Admin/*Controller ────┤                 ║        ║
║   ║                   ─────► Auth/*Controller ─────┤                 ║        ║
║   ║                                                │                 ║        ║
║   ║                                    ┌───────────▼──────────┐      ║        ║
║   ║                                    │   ELOQUENT MODELS     │      ║        ║
║   ║                                    │   User, MenuItem,     │      ║        ║
║   ║                                    │   Order, Payment,     │      ║        ║
║   ║                                    │   Category, etc.      │      ║        ║
║   ║                                    └───────────┬──────────┘      ║        ║
║   ║                                                │                 ║        ║
║   ║                                    ┌───────────▼──────────┐      ║        ║
║   ║                                    │     MySQL Database    │      ║        ║
║   ║                                    │     'sipnbite'        │      ║        ║
║   ║                                    │     14 tables         │      ║        ║
║   ║                                    └──────────────────────┘      ║        ║
║   ╚══════════════════════════════════════════════════════════════════╝        ║
║                                                                               ║
║   👨‍💼 ADMIN (Web Browser Only)                                                ║
║   ┌───────────────────────────────────────────┐                               ║
║   │ /admin/dashboard    ← Analytics & stats   │                               ║
║   │ /admin/categories   ← Manage categories   │                               ║
║   │ /admin/menu         ← Manage menu items   │                               ║
║   │ /admin/orders       ← Manage orders        │                               ║
║   │ /admin/bookings     ← Manage reservations   │                               ║
║   │ /admin/customers    ← Toggle user accounts   │                               ║
║   │ /admin/delivery-persons ← Manage delivery staff │                           ║
║   │ /admin/payments     ← Track payments         │                               ║
║   │ /admin/feedback     ← View customer feedback  │                               ║
║   │ /admin/invoices/{id} ← Generate invoice       │                               ║
║   │ /admin/settings     ← Café configuration      │                               ║
║   └───────────────────────────────────────────┘                               ║
║                                                                               ║
╚═══════════════════════════════════════════════════════════════════════════════╝
```

---

## 🔑 Key Takeaways

1. **Single Database, Multiple Clients:** Both Web and Android share the same `sipnbite` MySQL database through the Laravel backend.

2. **Two Route Files, One Server:** `web.php` serves HTML pages (Blade), `api.php` serves JSON responses — both run on the same Laravel server (`localhost:8000`).

3. **Android ↔ Web Bridge = REST API:** The `Api/*Controller` classes in the `WEB/` folder are the bridge that lets the Android app read and write to the same database.

4. **Admin is Web-Only:** All administrative functions (managing orders, menu, delivery staff, bookings) are done through the Web admin dashboard. The Android app is customer-facing only.

5. **Real-Time Sync (Manual Refresh):** The Android app doesn't use WebSockets or push notifications. Users manually refresh (e.g., "Refresh" button on Order Tracking) to see updates made by Admin on the Web dashboard.

6. **Cart Storage Differs:** Web uses server-side sessions; Android uses in-memory storage via `CartManager` singleton.

7. **Auth Differs:** Web uses Laravel session cookies; Android stores user data in SharedPreferences via `SessionManager`.

---

*Document generated on: September 5, 2026*
*Project: Sip N Bite — Café Management System*
