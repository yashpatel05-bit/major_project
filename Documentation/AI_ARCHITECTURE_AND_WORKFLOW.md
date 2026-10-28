# ☕ Sip N Bite Café — AI Architecture & How It Works

This document provides a simple, beginner-friendly explanation of how the **Sip N Bite AI Assistant** works across the **Android App (React Native)**, the **Web Backend (Laravel)**, and the **Database (MySQL)**.

---

## 📌 1. High-Level Overview: How AI Works in Sip N Bite

Whenever a customer taps the AI Assistant button in the mobile app and types a question, the request travels through a clean **3-layer pipeline**:

```mermaid
graph LR
    A[📱 Mobile App / Web] -->|1. POST /api/chat| B[⚙️ Laravel Backend Controller]
    B -->|2. Check Live Menu & Context| C[(🗄️ MySQL Database)]
    B -->|3. Generate Restaurant-Only Reply| D[🤖 AI Engine]
    D -->|4. Clean JSON Response| B
    B -->|5. { success: true, reply }| A
```

1. **Mobile App (Frontend)**: Captures the user's input and displays chat bubbles.
2. **Laravel Backend (`ChatbotController`)**: Receives the message, loads live café data from MySQL, and manages the AI logic.
3. **AI Engine (Dual Mode)**:
   - **Mode A (Online Generative AI)**: Google Gemini API with custom prompt engineering.
   - **Mode B (Local Smart Fallback Engine)**: Database-driven rule and intent matcher that operates even with no external API key or internet.

---

## 🎯 2. How the AI Responds ONLY for Our Restaurant (Restaurant-Only Guardrails)

A common question is: ***"How do you train or instruct the AI to only talk about Sip N Bite Café and not talk about general topics like coding, homework, or politics?"***

We achieve this using **Two Key Techniques**:

### Technique 1: System Prompt Grounding (For Generative AI / Gemini)
Instead of letting the AI answer freely like ChatGPT, we inject a **System Instruction** before every single message. This is known in modern AI as **Context Grounding (RAG - Retrieval-Augmented Generation)**.

```php
// 1. We dynamically fetch ALL real items from our MySQL database
$menuSummary = "Burgers & Wraps: Crispy Cottage Cheese Supreme Burger (₹249, Veg)...";

// 2. We inject this menu into the AI's "brain" before it reads the user's message
$systemPrompt = "You are 'Sip N Bite AI', the friendly assistant for Sip N Bite Café.
OUR OFFICIAL RESTAURANT MENU:
{$menuSummary}

STRICT BEHAVIOR RULES:
1. ONLY recommend and talk about items that exist on our official menu above. NEVER invent or hallucinate dishes.
2. If the user asks for a specific food (e.g. burger, pizza, shake), ONLY recommend dishes from that exact category.
3. If the user asks about ANYTHING unrelated to the café (coding, sports, politics), politely decline and bring the conversation back to food and table bookings.";
```

Because the AI receives this strict system prompt with every request:
- It **knows the exact prices and dishes** directly from your database.
- It **refuses off-topic questions** (e.g. if asked *"Write a python code"*, it politely reminds the user it is only here to help with dining and food).
- It **never hallucinates items** your café doesn't sell.

---

### Technique 2: Database-Driven Local Intent Matcher (Instant Local Fallback)
If the Google Gemini API key is missing or offline, our backend does **NOT** fail. Instead, it uses a **custom intelligent keyword-to-database engine**:

1. **Category Prioritization**:
   - If the user mentions **"burger"**, the backend queries:
     ```php
     MenuItem::where('name', 'LIKE', '%burger%')->where('is_available', 1)->get();
     ```
     👉 Recommends **Crispy Cottage Cheese Supreme Burger**, its price (₹249), rating (4.6), and description!
   - If the user mentions **"pizza"**, it queries only pizzas (`Margherita Basilico`, `Fiery Pepperoni Feast`).
   - If the user mentions **"shake"** or **"drink"**, it queries beverages (`Nutella Hazelnut Thick Shake`, `Caramel Macchiato`).
   - If the user mentions **"dessert"** or **"sweet"**, it queries desserts (`Molten Chocolate Lava Cake`).

2. **Dynamic / Varied General Recommendations**:
   - If the user simply asks *"What do you suggest to eat?"*, it does **NOT** pick a fixed 3 items anymore.
   - It builds a **balanced 3-course café combo**:
     - 🍕 1 Main dish (Burger or Pizza)
     - 🥤 1 Beverage (Coffee or Shake)
     - 🍰 1 Dessert (Lava Cake)
   - Every time the user asks, it chooses fresh, randomized combinations from the database.

---

## 🔄 3. Complete End-to-End Workflow

Here is step-by-step what happens when a customer interacts with the AI:

```
[ Step 1: User Types ]
Customer in Mobile App types: "Suggest something in burger"
         │
         ▼
[ Step 2: Mobile App (React Native) ]
ChatBotScreen.jsx handles keyboard pop-up, packages text into JSON:
{ "message": "Suggest something in burger" }
Sends HTTP POST to: http://10.206.5.191:8000/api/chat
         │
         ▼
[ Step 3: Laravel Route & Controller ]
Route::post('/chat', [ChatbotController::class, 'chat'])
ChatbotController receives and validates the input.
         │
         ▼
[ Step 4: Decision Matrix ]
Is GEMINI_API_KEY present in .env?
   ├── YES ──► Formats System Prompt with live MySQL menu
   │           Sends request to Google Gemini API
   │           Returns generated natural-language response
   │
   └── NO (or on error) ──► Uses Local Café Intelligence
                           Detects keyword: "burger"
                           Queries MenuItem matching "burger"
                           Returns formatted dish details with emojis
         │
         ▼
[ Step 5: JSON Response ]
Laravel returns HTTP 200:
{
  "success": true,
  "reply": "Craving burgers & wraps? 🍔 Here is our signature specialty:\n• Crispy Cottage Cheese Supreme Burger - ₹249 (⭐ 4.6, Veg)..."
}
         │
         ▼
[ Step 6: Mobile Screen Rendering ]
ChatBotScreen receives the reply, creates a bot chat bubble, and auto-scrolls to bottom.
```

---

## 🛡️ 4. What Topics Can the AI Answer?

| User Question | What the AI Does | Example Response |
| :--- | :--- | :--- |
| **"Suggest a burger"** | Finds burgers in MySQL | *"Craving burgers & wraps? 🍔 Here is our Crispy Cottage Cheese Supreme Burger - ₹249..."* |
| **"Suggest a pizza"** | Finds wood-fired pizzas | *"Here are our authentic Wood-Fired Pizzas! 🍕 Margherita Basilico (₹399), Fiery Pepperoni (₹479)..."* |
| **"What should I drink?"** | Finds beverages & shakes | *"Here are our refreshing Artisanal Beverages: ☕ Nutella Shake (₹219), Caramel Macchiato (₹189)..."* |
| **"Suggest something to eat"** | Combines 1 Main + 1 Drink + 1 Dessert | *"Here is a hand-picked combo for you today! 🌟 Margherita Pizza + Nutella Shake + Lava Cake!"* |
| **"Can I book a table?"** | Detects booking intent | *"Need a table? 🪑 You can reserve your spot in seconds! Tap on 'Table Status' from the top shortcuts..."* |
| **"Are you open?"** | Answers café timings | *"Sip N Bite Café is open daily from 9:00 AM to 11:00 PM. We look forward to serving you! ⏰"* |
| **"Write a python code"** | Non-restaurant guardrail triggers | *"I am your Sip N Bite assistant! Ask me about our coffee, burgers, pizzas, table bookings, or timings! 😊"* |

---

## ⚙️ 5. How to Add a Live Google Gemini API Key

If you want the AI to use Google's cutting-edge Generative AI (Gemini) instead of the local rule engine:

1. Open [WEB/.env](file:///d:/SIP_N_BITE/WEB/.env).
2. Locate the line:
   ```env
   GEMINI_API_KEY=
   GEMINI_MODEL=gemini-1.5-flash
   ```
3. Paste your free Google AI Studio API key:
   ```env
   GEMINI_API_KEY=AIzaSyYourNewActualKeyHere...
   ```
4. Save the file. The backend will instantly start routing questions through Gemini while retaining all restaurant-only guardrails!
