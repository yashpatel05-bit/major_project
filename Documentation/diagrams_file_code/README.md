# 📊 Sip N Bite — UML Diagrams & Architecture Specifications

> **Project:** Sip N Bite — Smart Café & Food Ordering Platform  
> **Target Scope:** Web Admin Portal (Laravel 11) + Mobile Application (React Native / Android)  
> **Directory:** `Documentation/diagrams_file_code/`

---

## 📁 Diagram Index

This folder contains the complete UML architectural diagram suite created for the **Sip N Bite** project, engineered with the exact notation, styling, and modules required for software engineering reports and academic documentation.

| # | Diagram Type | Markdown File | Description & Reference Mapping |
|---|--------------|---------------|---------------------------------|
| 1 | **Use Case Diagram** | [`use_case_diagram.md`](./use_case_diagram.md) | Features the **Admin Use Case Diagram** matching user reference image 1:1, along with the Customer mobile app use case diagram and unified system use case diagram. |
| 2 | **Class Diagram** | [`class_diagram.md`](./class_diagram.md) | Features the **Core Class Diagram** with `User`, `Customer`, `Admin`, `Cart`, `Order`, `OrderItem`, `MenuItem`, `Category`, `Payment` matching user reference image 1:1, plus the complete extended database entity model. |
| 3 | **Interaction Diagram** | [`interaction_diagram.md`](./interaction_diagram.md) | Features UML **Sequence Diagrams** for Customer Order Placement & Razorpay Payment, Admin Order Management & Invoice Generation, Table Booking, AI Assistant flow, and an object **Collaboration Diagram**. |
| 4 | **Activity Diagram** | [`activity_diagram.md`](./activity_diagram.md) | Features the **[3.3.1 Admin Activity Diagram]** with Login validation loop, Fork, parallel tasks (Manage Menu, Manage Category, View History), Join, Invoice generation, and Logout matching user reference image 1:1, plus Customer Order and Table Booking workflows. |
| 5 | **Deployment Diagram** | [`deployment_diagram.md`](./deployment_diagram.md) | Physical and logical deployment architecture covering Android client device, Admin browser workstation, Laravel application server, MySQL database tier, and external cloud services (Razorpay, Google Gemini AI, Geocoding). |

---

## 🛠️ How to Render & Export the Diagrams

All diagram files in this directory contain **dual diagram implementations**:
1. **Mermaid Diagrams (`mermaid`)**:
   - Renders natively in VS Code Markdown Preview, GitHub, GitLab, and Antigravity IDE.
   - Simply open any `.md` file in this directory and open the Markdown Preview (`Ctrl + Shift + V` in VS Code).
2. **PlantUML Scripts (`plantuml`)**:
   - Industry-standard UML scripting language matching classic academic formatting.
   - Can be copied into the [PlantUML Online Server](https://www.plantuml.com/plantuml) or [PlantText](https://www.planttext.com/) to download high-resolution PNG, SVG, or vector PDF diagrams to insert into Microsoft Word (`.docx`) project reports.
