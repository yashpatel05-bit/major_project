# ⚡ Activity Diagram — Sip N Bite (Web + Android)

> **Project:** Sip N Bite — Smart Café & Food Ordering Platform  
> **Platform Scope:** Web Admin Dashboard (Laravel 11) + Mobile Application (React Native / Android)  
> **Location:** `Documentation/diagrams_file_code/activity_diagram.md`

---

## 1. Executive Summary & Diagram Structure

This document provides dedicated, complete Activity Diagrams for **both Admin and Customer**, formatted to match the exact visual style in the project specification and reference images.

- **Part 1:** **[3.3.1 Admin activity diagram]** *(1:1 exact reproduction of Reference Image 2)*
- **Part 2:** **[3.3.2 Customer activity diagram]** *(Formatted in the exact same style with Fork, Parallel Modules, Join, Checkout & Logout)*
- **Part 3:** **Customer Food Ordering & Payment Sub-Activity Flow** *(Detailed Decision Branches for COD & Razorpay)*
- **Part 4:** **Table Reservation Sub-Activity Flow** *(Slot Availability Verification Flow)*
- **Part 5:** **Complete PlantUML Source Codes** *(Ready for high-res export into project report docs)*

---

## 2. [3.3.1 Admin activity diagram] (Exact Reference Match)

The diagram below exactly reflects the visual structure, fork/join parallelism, and decision loop from the reference image:
- **Initial Node:** `●`
- **Actions:** `Start` ➔ `Login`
- **Decision:** `Valid?`
  - **[No]** ➔ `Invalid Login` ➔ loops back to `Login`
  - **[Yes]** ➔ passes to **FORK Bar**
- **Concurrent Fork Branches:**
  1. `Manage Menu` ➔ `Add/Edit/Delete`
  2. `Manage Category` ➔ `Add/Edit/Delete`
  3. `View History`
- **JOIN Bar:** Synchronizes all parallel management operations
- **Sequential Actions:** `Generate Invoice` ➔ `Logout`
- **Final Node:** `End` (`◎`)

### Mermaid Diagram — Admin Activity

```mermaid
flowchart TD
    %% Initial Node
    StartNode((●)) --> Act_Start[Start]
    Act_Start --> Act_Login[Login]
    
    %% Decision Node
    Act_Login --> Dec_Valid{"Valid?"}
    
    %% Invalid path loop
    Dec_Valid -- No --> Act_Invalid[Invalid Login]
    Act_Invalid --> Act_Login
    
    %% Valid path to Fork
    Dec_Valid -- Yes --> ForkBar{{"════════════ FORK ════════════"}}
    
    %% Parallel branches
    ForkBar --> Branch1_Menu[Manage Menu]
    Branch1_Menu --> Branch1_Actions[Add/Edit/Delete]
    
    ForkBar --> Branch2_Cat[Manage Category]
    Branch2_Cat --> Branch2_Actions[Add/Edit/Delete]
    
    ForkBar --> Branch3_History[View History]
    
    %% Join Bar
    Branch1_Actions --> JoinBar{{"════════════ JOIN ════════════"}}
    Branch2_Actions --> JoinBar
    Branch3_History --> JoinBar
    
    %% Subsequent Actions
    JoinBar --> Act_Invoice[Generate Invoice]
    Act_Invoice --> Act_Logout[Logout]
    Act_Logout --> EndNode(((◎)))

    %% Styling to match classic light blue rounded UML aesthetic
    classDef actionStyle fill:#b8e2f2,stroke:#3a7999,stroke-width:1.5px,rx:15px,ry:15px,color:#0c3847,font-weight:bold;
    classDef decisionStyle fill:#d4effc,stroke:#3a7999,stroke-width:1.5px,color:#0c3847,font-weight:bold;
    classDef barStyle fill:#333333,stroke:#000000,stroke-width:2px,color:#ffffff,font-weight:bold;

    class Act_Start,Act_Login,Act_Invalid,Branch1_Menu,Branch1_Actions,Branch2_Cat,Branch2_Actions,Branch3_History,Act_Invoice,Act_Logout actionStyle;
    class Dec_Valid decisionStyle;
    class ForkBar,JoinBar barStyle;
```

---

## 3. [3.3.2 Customer activity diagram] (Exact Reference Style)

Engineered in the exact same structural pattern as the Admin diagram to cover the customer journey on the **React Native Android mobile app** and **Web customer portal**:
- **Initial Node:** `●`
- **Actions:** `Start` ➔ `Login / Register`
- **Decision:** `Valid?`
  - **[No]** ➔ `Invalid Credentials` ➔ loops back to `Login / Register`
  - **[Yes]** ➔ passes to **FORK Bar**
- **Concurrent Fork Branches:**
  1. `Browse Menu` ➔ `Add Items to Cart`
  2. `Table Booking` ➔ `Select Date / Time Slot`
  3. `AI Café Concierge` ➔ `Ask Recommendations`
- **JOIN Bar:** Synchronizes user activities
- **Sequential Actions:** `Proceed to Checkout` ➔ `Payment (COD / Online)` ➔ `Track Order` ➔ `Submit Feedback` ➔ `Logout`
- **Final Node:** `End` (`◎`)

### Mermaid Diagram — Customer Activity

```mermaid
flowchart TD
    %% Initial Node
    C_StartNode((●)) --> C_Start[Start]
    C_Start --> C_Auth[Login / Register]
    
    %% Decision Node
    C_Auth --> C_Valid{"Valid?"}
    
    %% Invalid path loop
    C_Valid -- No --> C_Invalid[Invalid Credentials]
    C_Invalid --> C_Auth
    
    %% Valid path to Fork
    C_Valid -- Yes --> C_ForkBar{{"════════════ FORK ════════════"}}
    
    %% Parallel Customer features
    C_ForkBar --> C_Branch1[Browse Menu & Filter]
    C_Branch1 --> C_Branch1_Sub[Add Items to Cart]
    
    C_ForkBar --> C_Branch2[Table Reservation]
    C_Branch2 --> C_Branch2_Sub[Select Date, Slot & Guests]
    
    C_ForkBar --> C_Branch3[AI Café Concierge]
    C_Branch3 --> C_Branch3_Sub[Ask Dish Questions]
    
    %% Join Bar
    C_Branch1_Sub --> C_JoinBar{{"════════════ JOIN ════════════"}}
    C_Branch2_Sub --> C_JoinBar
    C_Branch3_Sub --> C_JoinBar
    
    %% Subsequent Actions
    C_JoinBar --> C_Checkout[Proceed to Checkout]
    C_Checkout --> C_Payment[Select Payment: COD or Razorpay]
    C_Payment --> C_Track[Track Order Live]
    C_Track --> C_Feedback[Submit Feedback & Rating]
    C_Feedback --> C_Logout[Logout]
    C_Logout --> C_EndNode(((◎)))

    %% Styling to match classic light blue rounded UML aesthetic
    classDef actionStyle fill:#b8e2f2,stroke:#3a7999,stroke-width:1.5px,rx:15px,ry:15px,color:#0c3847,font-weight:bold;
    classDef decisionStyle fill:#d4effc,stroke:#3a7999,stroke-width:1.5px,color:#0c3847,font-weight:bold;
    classDef barStyle fill:#333333,stroke:#000000,stroke-width:2px,color:#ffffff,font-weight:bold;

    class C_Start,C_Auth,C_Invalid,C_Branch1,C_Branch1_Sub,C_Branch2,C_Branch2_Sub,C_Branch3,C_Branch3_Sub,C_Checkout,C_Payment,C_Track,C_Feedback,C_Logout actionStyle;
    class C_Valid decisionStyle;
    class C_ForkBar,C_JoinBar barStyle;
```

---

## 4. Customer Food Ordering & Checkout Sub-Activity Flow

Provides deep granularity into the cart calculation, payment branching, and database updates for the food ordering subsystem.

```mermaid
flowchart TD
    InitNode((●)) --> OpenApp[Open Sip N Bite Mobile App]
    OpenApp --> BrowseMenu[Browse Food Menu & Search]
    BrowseMenu --> SelectItem[Select Food Item & Quantity]
    SelectItem --> AddCart[Add to Cart]
    
    AddCart --> ViewCart{"Proceed to Checkout?"}
    ViewCart -- No --> BrowseMenu
    ViewCart -- Yes --> SelectAddr[Select Saved Delivery Address]
    
    SelectAddr --> CalcBill[System Computes Subtotal + 5% GST + Delivery Fee]
    CalcBill --> PayMethod{"Select Payment Method"}
    
    %% Payment Method Branches
    PayMethod -- Razorpay Online --> OpenGateway[Open Razorpay Checkout Gateway]
    OpenGateway --> PayStatus{"Payment Success?"}
    PayStatus -- Failed --> RetryPay[Payment Failed: Retry or Switch to COD]
    RetryPay --> PayMethod
    PayStatus -- Successful --> RecordPay[Record Online Payment in MySQL]
    
    PayMethod -- Cash on Delivery --> SetCOD[Mark Payment as COD Pending]
    
    RecordPay --> CreateOrder[Insert Order & OrderItems into MySQL]
    SetCOD --> CreateOrder
    
    CreateOrder --> ClearCart[Clear In-Memory & Local Cart]
    ClearCart --> LiveTracking[Redirect to Real-time Order Tracking Screen]
    LiveTracking --> StopNode(((◎)))

    classDef actionStyle fill:#e2f0d9,stroke:#385723,stroke-width:1.5px,rx:12px,ry:12px,color:#274e13,font-weight:bold;
    classDef decisionStyle fill:#fff2cc,stroke:#b2a100,stroke-width:1.5px,color:#7f6000,font-weight:bold;
    
    class OpenApp,BrowseMenu,SelectItem,AddCart,SelectAddr,CalcBill,OpenGateway,RetryPay,RecordPay,SetCOD,CreateOrder,ClearCart,LiveTracking actionStyle;
    class ViewCart,PayMethod,PayStatus decisionStyle;
```

---

## 5. Dining Table Reservation Sub-Activity Flow

Models table slot checking, availability validation, and reservation pass generation.

```mermaid
flowchart TD
    StartReserve((●)) --> OpenBooking[Navigate to Table Booking Screen]
    OpenBooking --> InputDetails[Select Booking Date, Time Slot & Guest Count]
    InputDetails --> CheckAvail[Query Tables with Matching Capacity]
    
    CheckAvail --> AvailDecision{"Table Available for Slot?"}
    AvailDecision -- No --> ShowUnavailable[Display 'No Slots Available' Message]
    ShowUnavailable --> ChangeSlot[Select Alternative Date or Time Slot]
    ChangeSlot --> InputDetails
    
    AvailDecision -- Yes --> ShowTables[Display Available Dining Tables]
    ShowTables --> PickTable[Select Preferred Table & Add Special Request]
    PickTable --> SubmitReservation[Submit Table Reservation Request]
    
    SubmitReservation --> SaveDB[Insert Record into table_bookings status='confirmed']
    SaveDB --> GeneratePass[Generate Table Booking Confirmation & QR Pass]
    GeneratePass --> EndReserve(((◎)))

    classDef actionStyle fill:#e8eaf6,stroke:#3f51b5,stroke-width:1.5px,rx:12px,ry:12px,color:#1a237e,font-weight:bold;
    classDef decisionStyle fill:#fff9c4,stroke:#fbc02d,stroke-width:1.5px,color:#5d4037,font-weight:bold;

    class OpenBooking,InputDetails,CheckAvail,ShowUnavailable,ChangeSlot,ShowTables,PickTable,SubmitReservation,SaveDB,GeneratePass actionStyle;
    class AvailDecision decisionStyle;
```

---

## 6. Activity Execution Step Dictionary (Admin vs. Customer)

| Step # | Admin Activity Path | Customer Activity Path | Technical Operation / Endpoint |
|--------|---------------------|------------------------|--------------------------------|
| **1** | Start & Login | Start & Register / Login | `POST /login` or `POST /api/login` |
| **2** | Validation Loop | Validation Loop | Verification of email/hash in `users` table |
| **3** | Fork: Split into Parallel Modules | Fork: Split into Parallel Modules | Fork bar concurrency |
| **4a**| Manage Menu (Add/Edit/Delete) | Browse Menu & Filter by Category | `GET/POST/PUT /admin/menu-items` vs `GET /api/menu` |
| **4b**| Manage Category (Add/Edit/Delete) | Table Reservation (Pick Date & Guests) | `GET/POST /admin/categories` vs `POST /api/table-bookings` |
| **4c**| View History (Orders & Analytics) | AI Café Concierge (Ask Dish Details) | `GET /admin/orders` vs `POST /api/chatbot/message` |
| **5** | Join: Synchronize Management Tasks | Join: Synchronize Customer Modules | Join bar synchronization |
| **6** | Generate Invoice | Proceed to Checkout & Pay (COD / Razorpay) | PDF generation vs `POST /api/checkout` |
| **7** | Review Customer Feedback | Real-time Order Tracking & Submit Feedback | `GET /admin/feedbacks` vs `POST /api/feedback` |
| **8** | Logout & End | Logout & End | Session termination / Token revocation |

---

## 7. PlantUML Source Code

### 7.1 PlantUML Code — [3.3.1 Admin activity diagram] (Exact Reference Match)

```plantuml
@startuml
skinparam activity {
    BackgroundColor LightBlue
    BorderColor #2980B9
    ArrowColor #2980B9
    DiamondBackgroundColor #EBF5FB
    DiamondBorderColor #2980B9
}
skinparam roundcorner 20

title [3.3.1 Admin activity diagram]

start
:Start;
:Login;

repeat
  if (Valid?) then (Yes)
    break
  else (No)
    :Invalid Login;
    :Login;
  endif
repeat while (true)

fork
  :Manage Menu;
  :Add/Edit/Delete;
fork again
  :Manage Category;
  :Add/Edit/Delete;
fork again
  :View History;
end fork

:Generate Invoice;
:Logout;

stop
@enduml
```

### 7.2 PlantUML Code — [3.3.2 Customer activity diagram] (Exact Reference Style)

```plantuml
@startuml
skinparam activity {
    BackgroundColor LightBlue
    BorderColor #2980B9
    ArrowColor #2980B9
    DiamondBackgroundColor #EBF5FB
    DiamondBorderColor #2980B9
}
skinparam roundcorner 20

title [3.3.2 Customer activity diagram]

start
:Start;
:Login / Register;

repeat
  if (Valid?) then (Yes)
    break
  else (No)
    :Invalid Credentials;
    :Login / Register;
  endif
repeat while (true)

fork
  :Browse Menu;
  :Add Items to Cart;
fork again
  :Table Reservation;
  :Select Date, Slot & Guests;
fork again
  :AI Café Concierge;
  :Ask Recommendations;
end fork

:Proceed to Checkout;
:Payment (COD / Online);
:Track Order;
:Submit Feedback;
:Logout;

stop
@enduml
```

### 7.3 PlantUML Code — Detailed Food Ordering & Payment Flow

```plantuml
@startuml
skinparam activity {
    BackgroundColor #E8F8F5
    BorderColor #117864
    ArrowColor #117864
}

start
:Open Sip N Bite Mobile App;
:Browse Food Menu;
:Filter by Category / Veg;
:Select Dish & Quantity;
:Add to Cart;

if (Proceed to Checkout?) then (Yes)
  :Select Delivery Address;
  :Compute Subtotal, Tax & Delivery Fee;
  
  if (Payment Method?) then (Online Razorpay)
    :Open Razorpay Modal;
    if (Payment Success?) then (Yes)
      :Capture Razorpay Transaction ID;
    else (No)
      :Show Payment Failed;
      detach
    endif
  else (Cash On Delivery)
    :Select COD Option;
  endif
  
  :Insert Order & OrderItems into MySQL;
  :Clear Shopping Cart;
  :Display Order Confirmation;
  :Navigate to Live Order Tracking;
else (No)
  :Continue Shopping;
endif

stop
@enduml
```
