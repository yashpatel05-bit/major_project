# 🧱 Class Diagram — Sip N Bite (Web + Android)

> **Project:** Sip N Bite — Smart Café & Food Ordering Platform  
> **Platform Scope:** Web Admin Dashboard (Laravel 11) + Mobile Application (React Native / Android) + Database Layer  
> **Location:** `Documentation/diagrams_file_code/class_diagram.md`

---

## 1. Overview & Diagram Catalog

A **Class Diagram** is a static structural diagram that describes the structure of a system by showing the system's classes, their attributes, operations (or methods), and the relationships among objects (inheritance, association, composition, aggregation, and dependency).

This document contains:
1. **[Exact Reference Style] Core Class Diagram** *(Matches user reference image 1:1)*
2. **Comprehensive Full-System Class Diagram** *(Includes Sip N Bite tables: Table, TableBooking, DeliveryPerson, Address, Feedback)*
3. **Class Dictionary & Structural Relationships** *(Attribute types, method signatures, visibility, cardinality)*
4. **PlantUML Source Code** *(Ready to compile into exact vector diagrams)*

---

## 2. Core Class Diagram (Exact Reference Style)

The diagram below reflects the exact class structure, hierarchy, attributes, and operations shown in the user's reference diagram:
- `User` (Parent class)
- `Customer` and `Admin` (Subclasses inheriting from `User`)
- `Cart` and `Order` (Associated with `Customer`)
- `MenuItem` (Associated with `Cart` and `Category`)
- `Payment` and `OrderItem` (Associated with `Order`)

### Mermaid Diagram Code

```mermaid
classDiagram
    %% Inheritance
    User <|-- Customer
    User <|-- Admin

    %% Associations
    Customer "1" --> "1" Cart : has
    Customer "1" --> "0..*" Order : places
    Cart "1" --> "1..*" MenuItem : contains
    MenuItem "*" --> "1" Category : belongs to
    Order "1" --> "1" Payment : paid via
    Order "1" *-- "1..*" OrderItem : composed of

    %% Class Definitions
    class User {
        +int id
        +string name
        +string email
        +string password
        +login()
        +logout()
    }

    class Customer {
        +string phone
        +string address
        +register()
        +placeOrder()
        +addToCart()
    }

    class Admin {
        +manageMenu()
        +manageCategories()
        +manageOrders()
        +generateInvoice()
    }

    class Cart {
        +int cartId
        +addItem()
        +removeItem()
        +calculateTotal()
    }

    class Order {
        +int orderId
        +date orderDate
        +float totalAmount
        +placeOrder()
        +trackOrder()
    }

    class OrderItem {
        +int quantity
        +float price
    }

    class MenuItem {
        +int id
        +string name
        +float price
        +string description
        +updateItem()
    }

    class Category {
        +int id
        +string categoryName
        +addCategory()
        +deleteCategory()
    }

    class Payment {
        +int paymentId
        +string method
        +float amount
        +processPayment()
    }
```

---

## 3. Comprehensive Sip N Bite Full-System Class Diagram (Web + Android)

Encompasses the complete Sip N Bite application domain, incorporating Table Reservations, Delivery Staff management, Saved Addresses, and Customer Ratings/Feedback.

### Mermaid Diagram Code

```mermaid
classDiagram
    %% Inheritance
    User <|-- Customer
    User <|-- Admin

    %% User Relations
    Customer "1" --> "1" Cart : operates
    Customer "1" --> "0..*" Order : places
    Customer "1" --> "0..*" TableBooking : reserves
    Customer "1" --> "0..*" Address : owns
    Customer "1" --> "0..*" Feedback : submits

    %% Catalog Relations
    Category "1" *-- "0..*" MenuItem : organizes
    Cart "1" o-- "0..*" CartItem : contains
    CartItem "*" --> "1" MenuItem : references

    %% Order & Payment Relations
    Order "1" *-- "1..*" OrderItem : contains
    OrderItem "*" --> "1" MenuItem : specifies
    Order "1" --> "1" Payment : settles with
    Order "*" --> "0..1" DeliveryPerson : assigned to
    Order "*" --> "1" Address : delivered to
    Order "1" --> "0..1" Feedback : reviewed by

    %% Table Booking Relations
    Table "1" <-- "0..*" TableBooking : allocates

    %% Classes
    class User {
        +int id
        +string name
        +string email
        +string password
        +string role
        +string status
        +login(email, password) bool
        +logout() void
    }

    class Customer {
        +string phone
        +string profile_image
        +register(data) User
        +addToCart(menuItemId, qty) void
        +placeOrder(addressId, paymentMethod) Order
        +bookTable(tableId, date, time, guests) TableBooking
        +submitFeedback(orderId, rating, comment) Feedback
    }

    class Admin {
        +manageMenu() void
        +manageCategories() void
        +manageOrders(orderId, status) void
        +generateInvoice(orderId) Invoice
        +assignDeliveryPerson(orderId, staffId) void
        +viewReports() DashboardStats
    }

    class Category {
        +int id
        +string name
        +string slug
        +string image
        +string description
        +string status
        +addCategory(data) Category
        +updateCategory(id, data) bool
        +deleteCategory(id) bool
    }

    class MenuItem {
        +int id
        +int category_id
        +string name
        +string description
        +float price
        +string image
        +bool is_available
        +bool is_veg
        +float rating
        +updateItem(data) bool
        +toggleAvailability() bool
    }

    class Cart {
        +int cartId
        +int user_id
        +float subtotal
        +float tax
        +float delivery_fee
        +float total
        +addItem(item, qty) void
        +removeItem(itemId) void
        +updateQuantity(itemId, qty) void
        +calculateTotal() float
        +clearCart() void
    }

    class CartItem {
        +int id
        +int menuItemId
        +string itemName
        +float price
        +int quantity
        +float subtotal
    }

    class Order {
        +int id
        +string order_number
        +int user_id
        +int delivery_person_id
        +int address_id
        +float subtotal
        +float tax
        +float delivery_fee
        +float total_amount
        +string payment_method
        +string payment_status
        +string order_status
        +date created_at
        +createOrder() Order
        +updateStatus(status) bool
        +trackOrder() OrderStatus
        +cancelOrder() bool
    }

    class OrderItem {
        +int id
        +int order_id
        +int menu_item_id
        +string item_name
        +float price
        +int quantity
        +float subtotal
    }

    class Payment {
        +int id
        +int order_id
        +int user_id
        +string razorpay_payment_id
        +string razorpay_order_id
        +float amount
        +string payment_method
        +string status
        +processPayment() bool
        +verifySignature(signature) bool
        +refund() bool
    }

    class Table {
        +int id
        +string table_number
        +int capacity
        +string location_type
        +string status
        +checkAvailability(date, time) bool
    }

    class TableBooking {
        +int id
        +int user_id
        +int table_id
        +date booking_date
        +time booking_time
        +int guests_count
        +string special_request
        +string status
        +createBooking() TableBooking
        +cancelBooking() bool
        +approveBooking() bool
    }

    class DeliveryPerson {
        +int id
        +string name
        +string phone
        +string vehicle_number
        +string status
        +acceptDelivery(orderId) bool
        +completeDelivery(orderId) bool
    }

    class Address {
        +int id
        +int user_id
        +string title
        +string address_line
        +string city
        +string pincode
        +float latitude
        +float longitude
        +bool is_default
        +saveAddress() bool
    }

    class Feedback {
        +int id
        +int user_id
        +int order_id
        +int rating
        +string comment
        +date created_at
        +postReview() bool
    }
```

---

## 4. Class Dictionary & Relational Multiplicities

| Class Name | Layer / Model File | Primary Role | Relationships & Multiplicities |
|------------|-------------------|--------------|--------------------------------|
| **User** | `WEB/app/Models/User.php` | Base identity model for authentication | Superclass of Customer and Admin (`1` to `N` with Orders, Bookings, Addresses). |
| **Customer** | Mobile App / Customer Web | Specialized user interacting with menu & cart | Inherits `User`. Has `1:1` Cart, `1:N` Orders, `1:N` Bookings. |
| **Admin** | `WEB/app/Http/Controllers/Admin` | System administrator with full privileges | Inherits `User`. Manages Menu, Categories, Orders, and Invoices. |
| **Category** | `WEB/app/Models/Category.php` | Groups food items into logical sections | `1:N` with `MenuItem` (Composition/Aggregation). |
| **MenuItem** | `WEB/app/Models/MenuItem.php` | Culinary products sold by the café | `N:1` with `Category`, referenced by `OrderItem` and `Cart`. |
| **Cart** | `React_Native/src/context/CartContext.js` | Client-side/Session shopping container | Aggregates `MenuItem` / `CartItem`. Transforms into `Order`. |
| **Order** | `WEB/app/Models/Order.php` | Customer purchase order record | `1:N` with `OrderItem`, `1:1` with `Payment`, `N:1` with `User`. |
| **OrderItem**| `WEB/app/Models/OrderItem.php` | Individual line item snapshots within order | `N:1` with `Order` (Composition), `N:1` with `MenuItem`. |
| **Payment** | `WEB/app/Models/Payment.php` | Financial transaction audit record | `1:1` with `Order`, integrates Razorpay API. |
| **Table** | `WEB/app/Models/Table.php` | Physical café dining tables inventory | `1:N` with `TableBooking`. |
| **TableBooking**| `WEB/app/Models/TableBooking.php` | Table reservation records | `N:1` with `User`, `N:1` with `Table`. |
| **DeliveryPerson**| `WEB/app/Models/DeliveryPerson.php`| Courier staff fulfilling deliveries | `1:N` with `Order`. |
| **Address** | `WEB/app/Models/Address.php` | User delivery location & GPS coordinates | `N:1` with `User`, `1:N` with `Order`. |
| **Feedback** | `WEB/app/Models/Feedback.php` | Customer ratings & review statements | `N:1` with `User`, `1:1` with `Order`. |

---

## 5. PlantUML Source Code

### PlantUML Code (Exact Reference Match)

```plantuml
@startuml
skinparam classAttributeIconSize 0
skinparam roundcorner 10
skinparam class {
    BackgroundColor #F8F9FA
    ArrowColor #2C3E50
    BorderColor #2C3E50
}

class User {
    +int id
    +string name
    +string email
    +string password
    +login()
    +logout()
}

class Customer {
    +string phone
    +string address
    +register()
    +placeOrder()
    +addToCart()
}

class Admin {
    +manageMenu()
    +manageCategories()
    +manageOrders()
    +generateInvoice()
}

class Cart {
    +int cartId
    +addItem()
    +removeItem()
    +calculateTotal()
}

class Order {
    +int orderId
    +date orderDate
    +float totalAmount
    +placeOrder()
    +trackOrder()
}

class OrderItem {
    +int quantity
    +float price
}

class MenuItem {
    +int id
    +string name
    +float price
    +string description
    +updateItem()
}

class Category {
    +int id
    +string categoryName
    +addCategory()
    +deleteCategory()
}

class Payment {
    +int paymentId
    +string method
    +float amount
    +processPayment()
}

' Inheritance
User <|-- Customer
User <|-- Admin

' Associations
Customer "1" -- "1" Cart : uses >
Customer "1" -- "0..*" Order : places >
Cart "1" -- "1..*" MenuItem : contains >
MenuItem "*" -- "1" Category : classified under >
Order "1" -- "1" Payment : settled by >
Order "1" *-- "1..*" OrderItem : consists of >
@enduml
```
