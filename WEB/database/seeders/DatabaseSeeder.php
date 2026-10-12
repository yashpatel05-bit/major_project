<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Table;
use App\Models\DeliveryPerson;
use App\Models\Setting;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Feedback;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin & Customer Users (Plain text passwords per prompt)
        $admin = User::create([
            'name' => 'Sip N Bite Admin',
            'email' => 'admin@sipnbite.com',
            'password' => 'admin123',
            'phone' => '+91 9876543210',
            'address' => 'Sip N Bite HQ, Gourmet Avenue, Sector 18',
            'role' => 'admin',
            'status' => 'active'
        ]);

        $customer = User::create([
            'name' => 'Alex Johnson',
            'email' => 'customer@sipnbite.com',
            'password' => 'customer123',
            'phone' => '+91 9123456789',
            'address' => 'Flat 402, Sunshine Apartments, Green Park',
            'role' => 'customer',
            'status' => 'active'
        ]);

        // Default Customer Address
        Address::create([
            'user_id' => $customer->id,
            'title' => 'Home',
            'address_line' => 'Flat 402, Sunshine Apartments, Green Park',
            'city' => 'New Delhi',
            'pincode' => '110016',
            'latitude' => 28.5562,
            'longitude' => 77.2010,
            'is_default' => true
        ]);

        // 2. Categories
        $catBurgers = Category::create([
            'name' => 'Burgers & Wraps',
            'slug' => 'burgers-wraps',
            'image' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=600&auto=format&fit=crop&q=60',
            'description' => 'Juicy, flame-grilled artisan burgers and crispy handcrafted wraps.',
            'status' => 'active'
        ]);

        $catPizzas = Category::create([
            'name' => 'Wood-Fired Pizzas',
            'slug' => 'pizzas',
            'image' => 'https://images.unsplash.com/photo-1513104890138-7c749659a591?w=600&auto=format&fit=crop&q=60',
            'description' => 'Authentic Italian wood-fired pizza topped with rich mozzarella and fresh basil.',
            'status' => 'active'
        ]);

        $catBeverages = Category::create([
            'name' => 'Artisanal Beverages',
            'slug' => 'beverages',
            'image' => 'https://images.unsplash.com/photo-1517701604599-bb29b565090c?w=600&auto=format&fit=crop&q=60',
            'description' => 'Freshly brewed coffees, thick shakes, refreshers, and signature mocktails.',
            'status' => 'active'
        ]);

        $catDesserts = Category::create([
            'name' => 'Decadent Desserts',
            'slug' => 'desserts',
            'image' => 'https://images.unsplash.com/photo-1551024709-8f23befc6f87?w=600&auto=format&fit=crop&q=60',
            'description' => 'Gourmet cakes, warm waffles, lava brownies, and artisanal gelato.',
            'status' => 'active'
        ]);

        // 3. Menu Items
        MenuItem::create([
            'category_id' => $catBurgers->id,
            'name' => 'Classic Truffle Cheese Burger',
            'description' => 'Prime veggie beef patty with truffle oil, melted cheddar, lettuce, and secret sauce.',
            'price' => 299.00,
            'image' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=600&auto=format&fit=crop&q=60',
            'is_available' => true,
            'is_veg' => false,
            'rating' => 4.8
        ]);

        MenuItem::create([
            'category_id' => $catBurgers->id,
            'name' => 'Crispy Cottage Cheese Supreme Burger',
            'description' => 'Golden fried paneer patty stuffed with jalapenos, layered with tangy chipotle mayonnaise.',
            'price' => 249.00,
            'image' => 'https://images.unsplash.com/photo-1550547660-d9450f859349?w=600&auto=format&fit=crop&q=60',
            'is_available' => true,
            'is_veg' => true,
            'rating' => 4.6
        ]);

        MenuItem::create([
            'category_id' => $catPizzas->id,
            'name' => 'Margherita Basilico Wood-Fired Pizza',
            'description' => 'San Marzano tomato sauce, fresh buffalo mozzarella, extra virgin olive oil, and organic basil.',
            'price' => 399.00,
            'image' => 'https://images.unsplash.com/photo-1513104890138-7c749659a591?w=600&auto=format&fit=crop&q=60',
            'is_available' => true,
            'is_veg' => true,
            'rating' => 4.9
        ]);

        MenuItem::create([
            'category_id' => $catPizzas->id,
            'name' => 'Fiery Pepperoni Feast Pizza',
            'description' => 'Crispy spicy pepperoni, mozzarella, crushed chilli flakes, and roasted garlic.',
            'price' => 479.00,
            'image' => 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=600&auto=format&fit=crop&q=60',
            'is_available' => true,
            'is_veg' => false,
            'rating' => 4.7
        ]);

        MenuItem::create([
            'category_id' => $catBeverages->id,
            'name' => 'Signature Iced Caramel Macchiato',
            'description' => 'Freshly pulled espresso poured over cold milk, ice, and homemade buttery caramel drip.',
            'price' => 189.00,
            'image' => 'https://images.unsplash.com/photo-1517701604599-bb29b565090c?w=600&auto=format&fit=crop&q=60',
            'is_available' => true,
            'is_veg' => true,
            'rating' => 4.8
        ]);

        MenuItem::create([
            'category_id' => $catBeverages->id,
            'name' => 'Nutella Hazelnut Thick Shake',
            'description' => 'Rich creamy blend of real Nutella, roasted hazelnuts, vanilla ice cream, and whipped topping.',
            'price' => 219.00,
            'image' => 'https://images.unsplash.com/photo-1572490122747-3968b75cc699?w=600&auto=format&fit=crop&q=60',
            'is_available' => true,
            'is_veg' => true,
            'rating' => 4.9
        ]);

        MenuItem::create([
            'category_id' => $catDesserts->id,
            'name' => 'Molten Chocolate Lava Cake',
            'description' => 'Warm Belgian chocolate cake with a gooey oozing liquid center served with vanilla gelato.',
            'price' => 199.00,
            'image' => 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=600&auto=format&fit=crop&q=60',
            'is_available' => true,
            'is_veg' => true,
            'rating' => 4.9
        ]);

        // 4. Café Dining Tables
        Table::create(['table_number' => 'T1 (Indoor)', 'capacity' => 2, 'location_type' => 'Indoor', 'status' => 'available']);
        Table::create(['table_number' => 'T2 (Indoor)', 'capacity' => 4, 'location_type' => 'Indoor', 'status' => 'available']);
        Table::create(['table_number' => 'T3 (Indoor VIP)', 'capacity' => 6, 'location_type' => 'Indoor', 'status' => 'available']);
        Table::create(['table_number' => 'O1 (Patio Outdoor)', 'capacity' => 4, 'location_type' => 'Outdoor', 'status' => 'available']);
        Table::create(['table_number' => 'R1 (Rooftop View)', 'capacity' => 2, 'location_type' => 'Rooftop', 'status' => 'available']);

        // 5. Delivery Persons
        DeliveryPerson::create([
            'name' => 'Rahul Sharma',
            'phone' => '+91 9811223344',
            'email' => 'rahul.delivery@sipnbite.com',
            'vehicle_number' => 'DL 01 AB 1234',
            'status' => 'available',
            'current_lat' => 28.5560,
            'current_lng' => 77.2015
        ]);

        DeliveryPerson::create([
            'name' => 'Priya Verma',
            'phone' => '+91 9822334455',
            'email' => 'priya.delivery@sipnbite.com',
            'vehicle_number' => 'DL 03 XY 9876',
            'status' => 'available',
            'current_lat' => 28.5570,
            'current_lng' => 77.2025
        ]);

        // 6. Initial Café Settings
        Setting::set('cafe_name', 'Sip N Bite Café & Bistro');
        Setting::set('cafe_tagline', 'Good Food. Great Coffee. Unforgettable Moments.');
        Setting::set('cafe_phone', '+91 9876543210');
        Setting::set('cafe_email', 'contact@sipnbite.com');
        Setting::set('cafe_address', '108 Gourmet Avenue, Sector 18, City Center');
        Setting::set('opening_hours', '09:00 AM - 11:00 PM (Mon - Sun)');
        Setting::set('tax_percentage', '5');
        Setting::set('flat_delivery_fee', '40');

        // 7. Seed sample order & feedback for initial dashboard visibility
        $order = Order::create([
            'order_number' => 'SNB-' . strtoupper(substr(uniqid(), -6)),
            'user_id' => $customer->id,
            'delivery_person_id' => 1,
            'address_id' => 1,
            'subtotal' => 488.00,
            'tax' => 24.40,
            'delivery_fee' => 40.00,
            'total_amount' => 552.40,
            'payment_method' => 'COD',
            'payment_status' => 'paid',
            'order_status' => 'Delivered',
            'notes' => 'Please bring extra napkins.'
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'menu_item_id' => 1,
            'item_name' => 'Classic Truffle Cheese Burger',
            'price' => 299.00,
            'quantity' => 1,
            'subtotal' => 299.00
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'menu_item_id' => 5,
            'item_name' => 'Signature Iced Caramel Macchiato',
            'price' => 189.00,
            'quantity' => 1,
            'subtotal' => 189.00
        ]);

        Feedback::create([
            'user_id' => $customer->id,
            'order_id' => $order->id,
            'rating' => 5,
            'comment' => 'The truffle burger was absolutely sublime and delivered burning hot! 5 stars!'
        ]);
    }
}
