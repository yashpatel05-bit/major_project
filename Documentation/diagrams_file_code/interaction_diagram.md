# 🔄 Interaction Diagram — Sip N Bite (Web + Android)

> **Project:** Sip N Bite — Smart Café & Food Ordering Platform  
> **Platform Scope:** Web Admin Dashboard + Mobile Application (React Native / Android) + REST APIs + External Gateways  
> **Location:** `Documentation/diagrams_file_code/interaction_diagram.md`

---

## 1. Overview & Diagram Catalog

**Interaction Diagrams** model the dynamic behavioral aspects of a software system by depicting how objects collaborate and communicate via message exchanges across time.

This document contains:
1. **Sequence Diagram 1: Customer Food Ordering & Online Payment Flow** *(Android Mobile App ↔ Laravel REST API ↔ Razorpay ↔ MySQL)*
2. **Sequence Diagram 2: Admin Order Management & Invoice Generation Flow** *(Admin Web ↔ Controllers ↔ Invoice PDF Generator)*
3. **Sequence Diagram 3: Dining Table Reservation Flow** *(Android / Web ↔ Booking API ↔ Table Availability Verification)*
4. **Sequence Diagram 4: AI Café Assistant Conversation Flow** *(Mobile App ↔ ChatbotController ↔ Gemini 1.5 Flash ↔ Offline DB Fallback)*
5. **Collaboration / Communication Diagram** *(Object structural communication view with message sequences)*
6. **PlantUML Source Codes** *(Full vector UML scripts)*

---

## 2. Sequence Diagram 1: Customer Food Ordering & Online Payment Flow

Depicts an end-to-end food ordering sequence initiated from the React Native Android application, interacting with Laravel's `OrderApiController`, processing payment with Razorpay, and saving records to MySQL.

### Mermaid Sequence Diagram

```mermaid
sequenceDiagram
    autonumber
    actor Customer as 👤 Customer (Mobile App)
    participant UI as 📱 CartScreen / Checkout
    participant API as ⚙️ OrderApiController
    participant DB as 🗄️ MySQL Database
    participant Razorpay as 💳 Razorpay Gateway
    participant Kitchen as 🧑‍🍳 Kitchen / Admin View

    Customer ->> UI: Tap "Proceed to Checkout"
    UI ->> API: GET /api/addresses
    API ->> DB: Query addresses WHERE user_id = ?
    DB -->> API: Return saved addresses
    API -->> UI: Render address selection list
    
    Customer ->> UI: Select Address & Choose "Razorpay Online"
    Customer ->> UI: Tap "Place Order & Pay"
    
    UI ->> API: POST /api/checkout/initiate-payment
    API ->> Razorpay: Create Order (amount, currency, receipt)
    Razorpay -->> API: Return razorpay_order_id & signature token
    API -->> UI: Return payment order details
    
    UI ->> Razorpay: Open Razorpay SDK Checkout Modal
    Customer ->> Razorpay: Enter UPI / Card / NetBanking details
    Razorpay -->> UI: Payment Success (razorpay_payment_id, signature)
    
    UI ->> API: POST /api/checkout/confirm
    Note over API: Verify Razorpay Signature & Hash
    
    critical Database Transaction
        API ->> DB: INSERT INTO orders (order_number, total_amount, payment_status='paid')
        API ->> DB: INSERT INTO order_items (order_id, menu_item_id, qty, price)
        API ->> DB: INSERT INTO payments (order_id, razorpay_payment_id, amount, status='success')
    end
    
    API ->> Kitchen: Broadcast new live order notification
    API -->> UI: Return { success: true, order_id: 1042 }
    UI ->> Customer: Display Order Confirmation & Navigate to Live Tracking
```

---

## 3. Sequence Diagram 2: Admin Order Management & Invoice Generation Flow

Directly aligns with the user's reference diagram workflow: Admin logs into the Web Dashboard, reviews incoming orders, updates dispatch status, and generates the customer invoice.

### Mermaid Sequence Diagram

```mermaid
sequenceDiagram
    autonumber
    actor Admin as 🧑‍💼 Admin (Web Portal)
    participant Auth as 🔐 AuthController
    participant Dashboard as 🖥️ DashboardController
    participant OrderCtrl as 📦 OrderController
    participant InvoiceCtrl as 🧾 InvoiceController
    participant DB as 🗄️ MySQL Database
    actor Customer as 👤 Customer (Android App)

    Admin ->> Auth: POST /login (email, password)
    Auth ->> DB: Check credentials WHERE role = 'admin'
    DB -->> Auth: Valid Admin Record
    Auth -->> Admin: 200 OK (Set Session / Redirect)
    
    Admin ->> Dashboard: GET /admin/dashboard
    Dashboard ->> DB: Fetch counts (Orders, Revenue, Reservations)
    DB -->> Dashboard: Summary metrics
    Dashboard -->> Admin: Render Admin Dashboard
    
    Admin ->> OrderCtrl: GET /admin/orders?status=pending
    OrderCtrl ->> DB: SELECT * FROM orders WITH user, items
    DB -->> OrderCtrl: Return pending orders
    OrderCtrl -->> Admin: Render Order Table
    
    Admin ->> OrderCtrl: POST /admin/orders/{id}/update-status (status="Preparing")
    OrderCtrl ->> DB: UPDATE orders SET order_status = 'preparing'
    DB -->> OrderCtrl: Row updated
    OrderCtrl -->> Customer: Push Notification: "Your order is being prepared!"
    
    Admin ->> InvoiceCtrl: GET /admin/invoices/{order_id}/generate
    InvoiceCtrl ->> DB: Query order, order_items, customer & payment details
    DB -->> InvoiceCtrl: Full invoice data
    Note over InvoiceCtrl: Calculate Subtotal, 5% CGST/SGST, Delivery Charge, Total
    InvoiceCtrl -->> Admin: Render / Download Printable PDF Tax Invoice
```

---

## 4. Sequence Diagram 3: Dining Table Reservation Flow

Illustrates table booking from the mobile app, verification of available slots, and admin confirmation.

### Mermaid Sequence Diagram

```mermaid
sequenceDiagram
    autonumber
    actor Customer as 👤 Customer
    participant App as 📱 TableBookingScreen
    participant API as ⚙️ BookingApiController
    participant DB as 🗄️ MySQL Database
    participant Admin as 🧑‍💼 Restaurant Admin

    Customer ->> App: Select Date, Time Slot & Party Size (Guests)
    App ->> API: GET /api/tables/available?date=...&time=...&guests=4
    API ->> DB: Query tables WHERE capacity >= 4 AND id NOT IN (booked_slots)
    DB -->> API: Return available tables list
    API -->> App: Render available tables & seating areas
    
    Customer ->> App: Select Table #4 & Tap "Confirm Reservation"
    App ->> API: POST /api/table-bookings (table_id, date, time, guests)
    API ->> DB: INSERT INTO table_bookings (status='confirmed')
    DB -->> API: Booking created (ID: TB-205)
    API -->> App: Booking Success with reservation summary
    API ->> Admin: Alert: "New Table Reservation #TB-205"
    App ->> Customer: Show Booking Pass with QR Code
```

---

## 5. Sequence Diagram 4: AI Café Assistant Interaction Flow

Details how customer messages are processed by Google Gemini 1.5 Flash API with strict restaurant grounding and resilient fallback to the local MySQL database.

### Mermaid Sequence Diagram

```mermaid
sequenceDiagram
    autonumber
    actor Customer as 👤 Customer
    participant App as 📱 ChatBotScreen (React Native)
    participant API as 🤖 ChatbotController (Laravel)
    participant DB as 🗄️ MySQL Catalog (Local)
    participant Gemini as ☁️ Google Gemini API (1.5 Flash)

    Customer ->> App: Send message: "What cold beverages do you have under ₹150?"
    App ->> API: POST /api/chatbot/message { prompt: "..." }
    
    API ->> DB: Query active menu_items & categories (Grounding Context)
    DB -->> API: [Iced Coffee: ₹120, Cold Brew: ₹140, Mojito: ₹130, ...]
    
    alt Gemini AI Service Available
        API ->> Gemini: Send Prompt + Strict Restaurant Guardrails + Menu Context
        Gemini -->> API: Generated response: "We have Iced Coffee (₹120), Cold Brew (₹140)..."
        API -->> App: Return JSON { reply: "...", source: "gemini" }
    else Gemini API Error / Offline / Timeout
        Note over API: Local Fallback Engine activated
        API ->> DB: Perform direct SQL keyword search on menu_items
        DB -->> API: Matched items
        API -->> App: Return JSON { reply: "Here are matching drinks...", source: "local_catalog" }
    end
    
    App ->> Customer: Display formatted chat bubble with action buttons
```

---

## 6. Collaboration / Communication Diagram

A **Collaboration Diagram** highlights the architectural topology and numbered message exchanges between collaborating objects.

```mermaid
flowchart TD
    %% Objects
    CustObj[":Customer<br/>(Client Device)"]
    CartObj[":CartManager<br/>(State Container)"]
    ApiObj[":OrderApiController<br/>(API Gateway)"]
    DbObj[":MySQL Database<br/>(Data Layer)"]
    PayObj[":RazorpayService<br/>(Payment Gateway)"]
    InvObj[":InvoiceGenerator<br/>(PDF Service)"]

    %% Messages
    CustObj -- "1: addItem(menuItem)<br/>2: checkout()" --> CartObj
    CartObj -- "3: postOrder(items, address)" --> ApiObj
    ApiObj -- "4: initiatePayment(amount)" --> PayObj
    PayObj -- "5: verifyPayment(signature)" --> ApiObj
    ApiObj -- "6: saveOrder(orderDetails)<br/>7: saveOrderItems(itemsList)" --> DbObj
    ApiObj -- "8: generateInvoice(orderId)" --> InvObj
    InvObj -- "9: fetchTaxDetails()" --> DbObj
    InvObj -- "10: returnInvoicePDF()" --> CustObj

    classDef objStyle fill:#e8f4fd,stroke:#0d6efd,stroke-width:2px,color:#0b5ed7,font-weight:bold;
    class CustObj,CartObj,ApiObj,DbObj,PayObj,InvObj objStyle;
```

---

## 7. PlantUML Source Code

### PlantUML Code for Customer Order & Payment Interaction

```plantuml
@startuml
autonumber
skinparam style strictuml
skinparam sequenceMessageAlign center
skinparam roundcorner 10

actor "Customer\n(Mobile App)" as Customer
boundary "Mobile UI" as UI
control "OrderApiController" as API
entity "MySQL Database" as DB
boundary "Razorpay Gateway" as Razorpay

Customer -> UI: Click "Place Order"
activate UI
UI -> API: POST /api/checkout
activate API

API -> DB: Begin Database Transaction
API -> DB: INSERT INTO orders (...)
API -> DB: INSERT INTO order_items (...)
DB --> API: Order Created (#1001)

API -> Razorpay: Create Order ID
Razorpay --> API: razorpay_order_id: "order_xyz123"
API --> UI: Return Order & Payment Details
deactivate API

UI -> Razorpay: Launch Checkout Form
Customer -> Razorpay: Submit Payment Credentials
Razorpay --> UI: Payment Success (payment_id)

UI -> API: POST /api/checkout/verify
activate API
API -> DB: UPDATE payments SET status='success'
API -> DB: UPDATE orders SET payment_status='paid'
DB --> API: Commit Transaction
API --> UI: Order Confirmed
deactivate API

UI --> Customer: Show Order Confirmation Screen
deactivate UI
@enduml
```

### PlantUML Code for Admin Order & Invoice Interaction

```plantuml
@startuml
autonumber
skinparam style strictuml
skinparam sequenceMessageAlign center

actor "Admin" as Admin
boundary "Admin View" as View
control "OrderController" as OrderCtrl
control "InvoiceController" as InvoiceCtrl
entity "MySQL DB" as DB

Admin -> View: Open "Orders Management"
activate View
View -> OrderCtrl: GET /admin/orders
activate OrderCtrl
OrderCtrl -> DB: SELECT * FROM orders WHERE status='pending'
DB --> OrderCtrl: Return Order List
OrderCtrl --> View: Display Orders
deactivate OrderCtrl

Admin -> View: Click "Accept & Prepare Order"
View -> OrderCtrl: POST /admin/orders/{id}/status (status="preparing")
activate OrderCtrl
OrderCtrl -> DB: UPDATE orders SET status='preparing'
DB --> OrderCtrl: Updated
OrderCtrl --> View: Order Status Updated
deactivate OrderCtrl

Admin -> View: Click "Generate Invoice"
View -> InvoiceCtrl: GET /admin/invoices/{id}/generate
activate InvoiceCtrl
InvoiceCtrl -> DB: Query Order, Items, Tax, Payment
DB --> InvoiceCtrl: Order Data
InvoiceCtrl -> InvoiceCtrl: Calculate Totals & Generate PDF
InvoiceCtrl --> View: Return Invoice PDF Stream
deactivate InvoiceCtrl
View --> Admin: Display & Print Invoice
deactivate View
@enduml
```
