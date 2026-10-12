<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\MenuItem;
use App\Models\Category;

class ChatbotController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $userMessage = trim($request->input('message'));
        $apiKey = env('GEMINI_API_KEY') ?: config('services.gemini.key');
        $model = env('GEMINI_MODEL', 'gemini-1.5-flash');

        // Fetch live available menu items grouped by category for high precision
        $categoriesWithItems = Category::with(['menuItems' => function($q) {
            $q->where('is_available', 1);
        }])->get();

        // 1. If Gemini API key is configured, call Gemini with our strict restaurant context
        if (!empty($apiKey) && strlen($apiKey) > 20) {
            try {
                $menuSummary = [];
                foreach ($categoriesWithItems as $cat) {
                    $itemNames = $cat->menuItems->map(fn($i) => "{$i->name} (₹{$i->price}, " . ($i->is_veg ? "Veg" : "Non-Veg") . ", Rating: {$i->rating})")->implode(', ');
                    if (!empty($itemNames)) {
                        $menuSummary[] = "{$cat->name}: {$itemNames}";
                    }
                }
                $formattedMenu = implode("\n", $menuSummary);

                $systemPrompt = "You are 'Sip N Bite AI', the friendly assistant for Sip N Bite Café.\n\n"
                    . "OUR OFFICIAL RESTAURANT MENU:\n{$formattedMenu}\n\n"
                    . "CAFÉ INFO:\n"
                    . "• Opening Hours: Daily 9:00 AM – 11:00 PM\n"
                    . "• Services: Dine-in, Table Reservations (book in-app via Table Status), Online Delivery, Takeaway\n"
                    . "• Payments: Razorpay (Cards, UPI, Netbanking) & Cash on Delivery\n\n"
                    . "STRICT BEHAVIOR RULES:\n"
                    . "1. ONLY recommend and talk about items that exist on our official menu above. NEVER invent or hallucinate dishes.\n"
                    . "2. If the user asks for a specific food (e.g., burger, pizza, coffee, shake, dessert), ONLY recommend dishes from that exact category.\n"
                    . "3. If the user asks for general recommendations, suggest a varied mix (e.g., 1 main, 1 beverage, 1 dessert) with prices.\n"
                    . "4. If the user asks about ANYTHING unrelated to the café (e.g. coding, math, general news, politics, weather), politely decline and bring the conversation back to Sip N Bite Café food, coffee, or table bookings.\n"
                    . "5. Keep replies short, appetizing, and friendly with emojis.";

                $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

                $response = Http::timeout(10)->withoutVerifying()->withHeaders([
                    'Content-Type' => 'application/json'
                ])->post($apiUrl, [
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [
                                ['text' => $systemPrompt . "\n\nCustomer: " . $userMessage]
                            ]
                        ]
                    ]
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                    if (!empty($reply)) {
                        return response()->json([
                            'success' => true,
                            'reply' => trim($reply)
                        ]);
                    }
                } else {
                    Log::warning("[ChatbotController] Gemini API call returned " . $response->status() . ": " . $response->body());
                }
            } catch (\Exception $e) {
                Log::warning("[ChatbotController] Gemini exception: " . $e->getMessage());
            }
        }

        // 2. Local Context-Aware Café Engine (Active when Gemini key is not set or API is unreachable)
        $fallbackReply = $this->generateLocalResponse($userMessage, $categoriesWithItems);

        return response()->json([
            'success' => true,
            'reply' => $fallbackReply
        ]);
    }

    /**
     * Context-aware rule-based assistant powered by the local café database.
     */
    private function generateLocalResponse(string $message, $categoriesWithItems): string
    {
        $msg = strtolower($message);

        // Greetings
        if (preg_match('/^(hi|hello|hey|namaste|good morning|good evening|good afternoon)(\s.*)?$/i', $msg) && strlen($msg) < 25) {
            return "Hello! 👋 Welcome to Sip N Bite Café! I can help you explore our menu, recommend popular specials, check prices, or guide you through table bookings. What are you craving today? ☕🍔";
        }

        // ─── SPECIFIC FOOD CATEGORY MATCHES (Checked FIRST so specific cravings always win) ───

        // 1. Burgers & Wraps
        if (preg_match('/\b(burger|burgers|wrap|wraps|patty)\b/i', $msg)) {
            $burgerItems = MenuItem::where(function($q) {
                $q->where('name', 'LIKE', '%burger%')->orWhere('name', 'LIKE', '%wrap%');
            })->where('is_available', 1)->get();

            if ($burgerItems->isNotEmpty()) {
                $lines = $burgerItems->map(fn($b) => "• {$b->name} - ₹{$b->price} (⭐ {$b->rating}, " . ($b->is_veg ? "Veg" : "Non-Veg") . ")")->implode("\n");
                return "Craving burgers & wraps? 🍔 Here is our signature specialty:\n\n{$lines}\n\nFreshly prepared with crispy lettuce and our secret house dressing. You can order it right from the menu! 😋";
            }
        }

        // 2. Pizzas
        if (preg_match('/\b(pizza|pizzas|slice|pepperoni|margherita)\b/i', $msg)) {
            $pizzaItems = MenuItem::where('name', 'LIKE', '%pizza%')->where('is_available', 1)->get();
            if ($pizzaItems->isNotEmpty()) {
                $lines = $pizzaItems->map(fn($p) => "• {$p->name} - ₹{$p->price} (⭐ {$p->rating}, " . ($p->is_veg ? "Veg" : "Non-Veg") . ")")->implode("\n");
                return "Here are our authentic Wood-Fired Pizzas! 🍕\n\n{$lines}\n\nBaked fresh in our stone oven with imported mozzarella. Tap 'Menu' or 'Home' to add to your cart!";
            }
        }

        // 3. Shakes & Smoothies
        if (preg_match('/\b(shake|shakes|thick shake|smoothie|nutella)\b/i', $msg)) {
            $shakes = MenuItem::where(function($q) {
                $q->where('name', 'LIKE', '%shake%')->orWhere('name', 'LIKE', '%smoothie%');
            })->where('is_available', 1)->get();

            if ($shakes->isNotEmpty()) {
                $lines = $shakes->map(fn($s) => "• {$s->name} - ₹{$s->price} (⭐ {$s->rating})")->implode("\n");
                return "Looking for a rich, delicious shake? 🥤\n\n{$lines}\n\nBlended thick with real premium ingredients! ✨";
            }
        }

        // 4. Coffee / Beverages / Drinks
        if (preg_match('/\b(coffee|tea|drink|drinks|beverage|beverages|macchiato|latte|cold brew|cappuccino|espresso)\b/i', $msg)) {
            $drinks = MenuItem::where(function($q) {
                $q->where('name', 'LIKE', '%macchiato%')
                  ->orWhere('name', 'LIKE', '%coffee%')
                  ->orWhere('name', 'LIKE', '%shake%')
                  ->orWhere('name', 'LIKE', '%tea%');
            })->where('is_available', 1)->get();

            if ($drinks->isNotEmpty()) {
                $lines = $drinks->map(fn($d) => "• {$d->name} - ₹{$d->price} (⭐ {$d->rating})")->implode("\n");
                return "Here are our refreshing Artisanal Beverages: ☕🥤\n\n{$lines}\n\nFreshly brewed to perk up your day!";
            }
        }

        // 5. Desserts / Cakes / Sweets
        if (preg_match('/\b(dessert|desserts|cake|cakes|lava cake|sweet|sweets|chocolate)\b/i', $msg)) {
            $desserts = MenuItem::where(function($q) {
                $q->where('name', 'LIKE', '%cake%')
                  ->orWhere('name', 'LIKE', '%lava%')
                  ->orWhere('name', 'LIKE', '%dessert%')
                  ->orWhere('name', 'LIKE', '%chocolate%');
            })->where('is_available', 1)->get();

            if ($desserts->isNotEmpty()) {
                $lines = $desserts->map(fn($d) => "• {$d->name} - ₹{$d->price} (⭐ {$d->rating})")->implode("\n");
                return "Treat your sweet tooth! 🍰✨ Here are our Decadent Desserts:\n\n{$lines}\n\nWarm, rich, and totally irresistible!";
            }
        }

        // 6. Non-Vegetarian
        if (preg_match('/\b(non-veg|non veg|nonveg|meat|chicken|pepperoni)\b/i', $msg)) {
            $nonVegItems = MenuItem::where('is_veg', 0)->where('is_available', 1)->get();
            if ($nonVegItems->isNotEmpty()) {
                $lines = $nonVegItems->map(fn($item) => "• {$item->name} - ₹{$item->price} (⭐ {$item->rating})")->implode("\n");
                return "Here are our Non-Veg specialties: 🍖🍕\n\n{$lines}\n\nMade with premium savory cuts!";
            }
        }

        // 7. Vegetarian
        if (preg_match('/\b(veg|vegetarian|pure veg)\b/i', $msg)) {
            $vegItems = MenuItem::where('is_veg', 1)->where('is_available', 1)->inRandomOrder()->take(3)->get();
            if ($vegItems->isNotEmpty()) {
                $lines = $vegItems->map(fn($item) => "• {$item->name} - ₹{$item->price} (⭐ {$item->rating})")->implode("\n");
                return "We have delicious 100% vegetarian options: 🥗🌱\n\n{$lines}\n\nLook for the green veg mark in the menu!";
            }
        }

        // ─── GENERAL RECOMMENDATIONS (Dynamic & Varied) ───
        if (preg_match('/\b(recommend|recommendation|popular|best|special|favorite|suggest|suggestion|what to eat|must try|order)\b/i', $msg)) {
            // Pick a balanced meal recommendation: 1 Main dish, 1 Beverage, 1 Dessert
            $mainItem = MenuItem::whereIn('category_id', [1, 2])->where('is_available', 1)->inRandomOrder()->first();
            $drinkItem = MenuItem::where('category_id', 3)->where('is_available', 1)->inRandomOrder()->first();
            $dessertItem = MenuItem::where('category_id', 4)->where('is_available', 1)->inRandomOrder()->first();

            $suggestions = array_filter([$mainItem, $drinkItem, $dessertItem]);

            if (!empty($suggestions)) {
                $lines = collect($suggestions)->map(fn($item) => "• {$item->name} - ₹{$item->price} (⭐ {$item->rating})")->implode("\n");
                return "Here is a hand-picked recommendation combo for you today! 🌟\n\n{$lines}\n\nEnjoy the full Sip N Bite café experience! 🍽️";
            }

            // Fallback to 3 random top-rated items
            $randomItems = MenuItem::where('is_available', 1)->inRandomOrder()->take(3)->get();
            $lines = $randomItems->map(fn($item) => "• {$item->name} - ₹{$item->price} (⭐ {$item->rating})")->implode("\n");
            return "Here are our chef picks for you today:\n\n{$lines}\n\nOrder them directly from the Home screen! ✨";
        }

        // Table Booking / Reservation
        if (preg_match('/\b(table|book|booking|reserve|reservation|seat)\b/i', $msg)) {
            return "Need a table? 🪑 You can reserve your dining spot in seconds! Tap on 'Table Status' from the top shortcuts, choose your date, time slot, and party size to confirm.";
        }

        // Menu / Categories overview
        if (preg_match('/\b(menu|items|categories|food|dish|dishes|catalog)\b/i', $msg)) {
            $categories = Category::pluck('name')->implode(', ');
            return "Our café menu features: {$categories}! 📋\n\nYou can ask me for recommendations in any of these categories (e.g. 'suggest a burger' or 'suggest a coffee') or tap the Menu tab at the bottom to browse the full catalog with pictures!";
        }

        // Pricing / Offers / Discount
        if (preg_match('/\b(price|cost|rate|cheap|expensive|offer|discount|deal)\b/i', $msg)) {
            return "Our delicious dishes start from just ₹189! We also run special chef deals. Check the 'Today\'s Special' banner on your Home screen for today's best offers! 🏷️";
        }

        // Café Timings / Open hours
        if (preg_match('/\b(time|timing|open|close|hours|when)\b/i', $msg)) {
            return "Sip N Bite Café is open daily from 9:00 AM to 11:00 PM. We look forward to serving you! ⏰";
        }

        // Orders / Delivery / Tracking
        if (preg_match('/\b(order|delivery|track|tracking|status)\b/i', $msg)) {
            return "You can track your orders in real-time! Tap on 'My Orders' on the home screen or profile to see your order status and delivery updates. 🛵";
        }

        // Location / Address
        if (preg_match('/\b(location|where|address|place|find you)\b/i', $msg)) {
            return "We are located downtown! You can also enjoy food delivered hot to your doorstep by saving your delivery address in the 'Addresses' tab. 📍";
        }

        // Dynamic search across all items in the database by single words
        $words = preg_split('/\s+/', $msg);
        $stopWords = ['can', 'you', 'give', 'me', 'some', 'any', 'the', 'for', 'about', 'with', 'like', 'how', 'what', 'which', 'who', 'tell', 'want', 'need', 'show'];
        foreach ($words as $word) {
            $cleanWord = preg_replace('/[^a-z0-9]/', '', $word);
            if (strlen($cleanWord) >= 4 && !in_array($cleanWord, $stopWords)) {
                $matchedItem = MenuItem::where('name', 'LIKE', "%{$cleanWord}%")
                    ->orWhere('description', 'LIKE', "%{$cleanWord}%")
                    ->first();
                if ($matchedItem) {
                    return "Here is our matching specialty:\n• {$matchedItem->name} - ₹{$matchedItem->price} (⭐ {$matchedItem->rating})\n{$matchedItem->description}\n\nYou can order it right now from the menu! 🍽️";
                }
            }
        }

        // Default friendly fallback
        return "I am your Sip N Bite assistant! ☕ Ask me about our burgers, pizzas, coffees, thick shakes, desserts, table bookings, or café timings, and I'll be glad to help! 😊";
    }
}

