# 📌 Use Case Diagram — Sip N Bite (Web + Android)

> **Project:** Sip N Bite — Smart Café & Food Ordering Platform  
> **Platform Scope:** Web Admin Dashboard (Laravel 11) + Mobile Application (React Native / Android) + Web Customer Portal  
> **Location:** `Documentation/diagrams_file_code/use_case_diagram.md`

---

## 1. Executive Summary & Diagram Structure

This document provides dedicated, complete Use Case Diagrams for **both Admin and Customer**, formatted to match the exact visual style in the project specification and reference images.

- **Part 1:** **Admin Use Case Diagram** *(Matches Reference Image 1:1)*
- **Part 2:** **Customer Use Case Diagram** *(Formatted in the exact same reference style for Android / Web Customer)*
- **Part 3:** **Unified System Use Case Diagram** *(Admin + Customer + Delivery Staff + Payment Gateway + AI)*
- **Part 4:** **Complete PlantUML Source Codes** *(Ready for high-res export into project report docs)*

---

## 2. Admin Use Case Diagram (Exact Reference Style)

The diagram below corresponds 1:1 to the user's reference image:
- **Actor:** `Admin` (Left stick figure)
- **System Boundary:** `ADMIN`
- **Use Cases:**
  1. `Login`
  2. `Manage menu`
  3. `Manage category`
  4. `Manage Order`
  5. `Generate Invoice`
  6. `Logout`

### Mermaid Diagram (Admin)

```mermaid
flowchart LR
    %% Actor
    Admin["🧑‍💼<br/><b>Admin</b>"]

    %% System Boundary
    subgraph SystemBoundary ["<b>ADMIN</b>"]
        direction TB
        UC_Login(["Login"])
        UC_ManageMenu(["Manage menu"])
        UC_ManageCat(["Manage category"])
        UC_ManageOrder(["Manage Order"])
        UC_GenInvoice(["Generate Invoice"])
        UC_Logout(["Logout"])
    end

    %% Solid Connections from Actor to Use Cases
    Admin --- UC_Login
    Admin --- UC_ManageMenu
    Admin --- UC_ManageCat
    Admin --- UC_ManageOrder
    Admin --- UC_GenInvoice
    Admin --- UC_Logout

    %% Styling matching reference image blue ovals
    classDef actorStyle fill:#ffffff,stroke:#343a40,stroke-width:2px;
    classDef boundaryStyle fill:#ffffff,stroke:#000000,stroke-width:2px;
    classDef useCaseStyle fill:#9ec5e8,stroke:#4a779d,stroke-width:1.5px,color:#000000,font-weight:500;

    class Admin actorStyle;
    class SystemBoundary boundaryStyle;
    class UC_Login,UC_ManageMenu,UC_ManageCat,UC_ManageOrder,UC_GenInvoice,UC_Logout useCaseStyle;
```

---

## 3. Customer Use Case Diagram (Exact Reference Style)

Formulated in the exact same style as the Admin diagram to reflect the customer experience on the **React Native Android mobile app** and **Web customer portal**:
- **Actor:** `Customer` (Left stick figure)
- **System Boundary:** `CUSTOMER`
- **Use Cases:**
  1. `Register / Login`
  2. `Browse Menu & Search`
  3. `Manage Cart`
  4. `Place Order`
  5. `Make Payment (Online / COD)`
  6. `Track Order`
  7. `Book Dining Table`
  8. `Chat with AI Assistant`
  9. `Give Feedback & Rating`
  10. `Logout`

### Mermaid Diagram (Customer)

```mermaid
flowchart LR
    %% Actor
    Customer["👤<br/><b>Customer</b>"]

    %% System Boundary
    subgraph CustomerBoundary ["<b>CUSTOMER</b>"]
        direction TB
        UC_C_Auth(["Register / Login"])
        UC_C_Browse(["Browse Menu & Search"])
        UC_C_Cart(["Manage Cart"])
        UC_C_Order(["Place Order"])
        UC_C_Pay(["Make Payment (Online / COD)"])
        UC_C_Track(["Track Order"])
        UC_C_Table(["Book Dining Table"])
        UC_C_AI(["Chat with AI Assistant"])
        UC_C_Feedback(["Give Feedback & Rating"])
        UC_C_Logout(["Logout"])
    end

    %% Solid Connections from Customer to Use Cases
    Customer --- UC_C_Auth
    Customer --- UC_C_Browse
    Customer --- UC_C_Cart
    Customer --- UC_C_Order
    Customer --- UC_C_Pay
    Customer --- UC_C_Track
    Customer --- UC_C_Table
    Customer --- UC_C_AI
    Customer --- UC_C_Feedback
    Customer --- UC_C_Logout

    %% Styling matching reference image blue ovals
    classDef actorStyle fill:#ffffff,stroke:#343a40,stroke-width:2px;
    classDef boundaryStyle fill:#ffffff,stroke:#000000,stroke-width:2px;
    classDef useCaseStyle fill:#9ec5e8,stroke:#4a779d,stroke-width:1.5px,color:#000000,font-weight:500;

    class Customer actorStyle;
    class CustomerBoundary boundaryStyle;
    class UC_C_Auth,UC_C_Browse,UC_C_Cart,UC_C_Order,UC_C_Pay,UC_C_Track,UC_C_Table,UC_C_AI,UC_C_Feedback,UC_C_Logout useCaseStyle;
```

---

## 4. Comprehensive Unified System Use Case Diagram (Admin + Customer + Cloud)

Integrates both primary user roles (`Admin` and `Customer`), secondary actors (`Delivery Staff`, `Razorpay Gateway`, `Gemini AI`), and relational dependencies (`<<include>>` and `<<extend>>`).

### Mermaid Diagram (Unified System)

```mermaid
flowchart LR
    %% Primary Actors
    Admin["🧑‍💼<br/><b>Admin</b>"]
    Customer["👤<br/><b>Customer</b>"]
    Delivery["🛵<br/><b>Delivery Staff</b>"]

    %% External System Actors
    Razorpay["🏦<br/><b>Razorpay API</b>"]
    Gemini["🤖<br/><b>Google Gemini AI</b>"]

    subgraph SipNBite ["<b>SIP N BITE SYSTEM BOUNDARY</b>"]
        direction TB

        %% Common
        UC_Shared_Login(["Login / Auth"])
        UC_Shared_Logout(["Logout"])

        %% Customer Modules
        subgraph Sub_Customer ["Customer Modules (Android App & Web)"]
            UC_Cust_Browse(["Browse & Filter Menu"])
            UC_Cust_Cart(["Manage Cart Items"])
            UC_Cust_Checkout(["Place Order"])
            UC_Cust_Pay(["Process Payment"])
            UC_Cust_Track(["Track Order in Real-Time"])
            UC_Cust_Book(["Reserve Dining Table"])
            UC_Cust_AI(["Consult AI Assistant"])
            UC_Cust_Review(["Submit Feedback"])
        end

        %% Admin Modules
        subgraph Sub_Admin ["Admin Modules (Web Dashboard)"]
            UC_Adm_Menu(["Manage Menu Items"])
            UC_Adm_Cat(["Manage Categories"])
            UC_Adm_Orders(["Manage Orders & Dispatch"])
            UC_Adm_Invoice(["Generate & Print Invoice"])
            UC_Adm_Tables(["Manage Restaurant Tables"])
            UC_Adm_Assign(["Assign Delivery Partner"])
            UC_Adm_Reports(["View Sales Analytics"])
        end

        %% Delivery Module
        subgraph Sub_Delivery ["Fulfillment"]
            UC_Del_Update(["Update Delivery Status"])
        end
    end

    %% Customer Links
    Customer --- UC_Shared_Login
    Customer --- UC_Cust_Browse
    Customer --- UC_Cust_Cart
    Customer --- UC_Cust_Checkout
    Customer --- UC_Cust_Track
    Customer --- UC_Cust_Book
    Customer --- UC_Cust_AI
    Customer --- UC_Cust_Review
    Customer --- UC_Shared_Logout

    %% Admin Links
    Admin --- UC_Shared_Login
    Admin --- UC_Adm_Menu
    Admin --- UC_Adm_Cat
    Admin --- UC_Adm_Orders
    Admin --- UC_Adm_Invoice
    Admin --- UC_Adm_Tables
    Admin --- UC_Adm_Assign
    Admin --- UC_Adm_Reports
    Admin --- UC_Shared_Logout

    %% Delivery Links
    Delivery --- UC_Shared_Login
    Delivery --- UC_Del_Update
    Delivery --- UC_Shared_Logout

    %% Include / Extend / Third-Party
    UC_Cust_Checkout -.->|<<include>>| UC_Cust_Cart
    UC_Cust_Checkout -.->|<<include>>| UC_Cust_Pay
    UC_Cust_Pay --- Razorpay
    UC_Cust_AI --- Gemini
    UC_Adm_Orders -.->|<<include>>| UC_Adm_Invoice
    UC_Adm_Orders -.->|<<extend>>| UC_Adm_Assign
```

---

## 5. Use Case Specification Table (Admin vs. Customer)

| Actor | Use Case Name | Action Flow | Output / Result |
|-------|---------------|-------------|-----------------|
| **Admin** | **Login** | Enters admin credentials on `/login`. | Redirected to `/admin/dashboard` upon verification. |
| **Admin** | **Manage menu** | Creates, modifies prices, flags veg/non-veg, toggles availability. | Real-time update in `menu_items` table. |
| **Admin** | **Manage category** | Adds or updates categories (e.g., Pizza, Burgers, Fries, Drinks). | Categorization refreshed across Web and Mobile. |
| **Admin** | **Manage Order** | Reviews new incoming orders, changes status (`Pending` ➔ `Preparing` ➔ `Out for Delivery`). | Real-time push notification sent to customer. |
| **Admin** | **Generate Invoice** | Generates official tax invoice with GST calculation. | Printable invoice generated & stored. |
| **Admin** | **Logout** | Clicks logout button. | Session destroyed, redirected to login. |
| **Customer** | **Register / Login** | Signs up with name, email, phone, and password on mobile app. | Bearer token / session created. |
| **Customer** | **Browse Menu & Search**| Views categorized dishes with price, veg badge, and search filter. | Real-time catalog rendered via REST API. |
| **Customer** | **Manage Cart** | Adds dish, increments/decrements quantity, calculates live subtotal. | In-memory & stored cart state updated. |
| **Customer** | **Place Order** | Selects address, chooses payment mode (COD / Razorpay), confirms checkout. | New record created in `orders` and `order_items`. |
| **Customer** | **Make Payment** | Opens Razorpay modal or selects Cash on Delivery. | Transaction verified and saved in `payments`. |
| **Customer** | **Track Order** | Views live order status step-by-step from kitchen to delivery. | Visual progress bar updated in real-time. |
| **Customer** | **Book Dining Table** | Selects date, time slot, and guest count to reserve table. | Entry stored in `table_bookings` table. |
| **Customer** | **Chat with AI** | Interacts with AI assistant for recommendations & dish details. | Response generated by Gemini 1.5 Flash. |
| **Customer** | **Give Feedback** | Rates completed order from 1 to 5 stars with comment. | Feedback recorded in `feedback` table. |
| **Customer** | **Logout** | Signs out of the mobile app or web portal. | Auth token cleared from device storage. |

---

## 6. PlantUML Source Code

### 6.1 PlantUML Code — Admin Use Case Diagram (Exact Reference Match)

```plantuml
@startuml
skinparam actorStyle awesome
skinparam packageStyle rectangle
skinparam roundcorner 20
skinparam usecase {
    BackgroundColor #9ec5e8
    BorderColor #4a779d
    ArrowColor #4a779d
    FontColor Black
}

actor "Admin" as admin

rectangle "ADMIN" {
    usecase "Login" as UC1
    usecase "Manage menu" as UC2
    usecase "Manage category" as UC3
    usecase "Manage Order" as UC4
    usecase "Generate Invoice" as UC5
    usecase "Logout" as UC6
}

admin -- UC1
admin -- UC2
admin -- UC3
admin -- UC4
admin -- UC5
admin -- UC6
@enduml
```

### 6.2 PlantUML Code — Customer Use Case Diagram (Exact Reference Style)

```plantuml
@startuml
skinparam actorStyle awesome
skinparam packageStyle rectangle
skinparam roundcorner 20
skinparam usecase {
    BackgroundColor #9ec5e8
    BorderColor #4a779d
    ArrowColor #4a779d
    FontColor Black
}

actor "Customer" as customer

rectangle "CUSTOMER" {
    usecase "Register / Login" as C_UC1
    usecase "Browse Menu & Search" as C_UC2
    usecase "Manage Cart" as C_UC3
    usecase "Place Order" as C_UC4
    usecase "Make Payment (Online / COD)" as C_UC5
    usecase "Track Order" as C_UC6
    usecase "Book Dining Table" as C_UC7
    usecase "Chat with AI Assistant" as C_UC8
    usecase "Give Feedback & Rating" as C_UC9
    usecase "Logout" as C_UC10
}

customer -- C_UC1
customer -- C_UC2
customer -- C_UC3
customer -- C_UC4
customer -- C_UC5
customer -- C_UC6
customer -- C_UC7
customer -- C_UC8
customer -- C_UC9
customer -- C_UC10
@enduml
```

### 6.3 PlantUML Code — Unified System Use Case Diagram

```plantuml
@startuml
left to right direction
skinparam packageStyle rectangle
skinparam roundcorner 15
skinparam shadowing false

actor "Customer\n(Android/Web)" as customer
actor "Admin\n(Web Dashboard)" as admin
actor "Delivery Staff" as delivery
actor "Razorpay Gateway" as razorpay << Service >>
actor "Google Gemini AI" as gemini << Service >>

rectangle "Sip N Bite Platform" {
    package "Authentication" {
        usecase "Login & Authentication" as UC_Login
        usecase "Register Customer Account" as UC_Register
        usecase "Logout" as UC_Logout
    }

    package "Catalog & Menu" {
        usecase "Browse & Search Menu" as UC_Browse
        usecase "Manage Menu Items" as UC_ManageMenu
        usecase "Manage Categories" as UC_ManageCategory
        usecase "Chat with AI Assistant" as UC_ChatAI
    }

    package "Order & Payment" {
        usecase "Manage Cart" as UC_Cart
        usecase "Checkout & Place Order" as UC_Checkout
        usecase "Process Online Payment" as UC_OnlinePayment
        usecase "Live Order Tracking" as UC_Track
        usecase "Manage Order Status" as UC_ManageOrders
        usecase "Generate Invoice" as UC_Invoice
    }

    package "Table Reservations" {
        usecase "Reserve Table" as UC_BookTable
        usecase "Manage Table Bookings" as UC_ManageBookings
    }

    package "Feedback & Reviews" {
        usecase "Submit Order Feedback" as UC_Feedback
        usecase "View Customer Feedback" as UC_ViewFeedback
    }
}

' Customer connections
customer -- UC_Login
customer -- UC_Register
customer -- UC_Browse
customer -- UC_ChatAI
customer -- UC_Cart
customer -- UC_Checkout
customer -- UC_Track
customer -- UC_BookTable
customer -- UC_Feedback
customer -- UC_Logout

' Admin connections
admin -- UC_Login
admin -- UC_ManageMenu
admin -- UC_ManageCategory
admin -- UC_ManageOrders
admin -- UC_Invoice
admin -- UC_ManageBookings
admin -- UC_ViewFeedback
admin -- UC_Logout

' Delivery Staff
delivery -- UC_Login
delivery -- UC_Track
delivery -- UC_Logout

' Includes & Extends
UC_Checkout ..> UC_Cart : <<include>>
UC_Checkout ..> UC_OnlinePayment : <<extend>>
UC_OnlinePayment -- razorpay
UC_ChatAI -- gemini
UC_ManageOrders ..> UC_Invoice : <<include>>
@enduml
```
