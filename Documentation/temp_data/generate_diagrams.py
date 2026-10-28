import os
import matplotlib.pyplot as plt
import matplotlib.patches as patches

def generate_use_case_diagram(output_path):
    fig, ax = plt.subplots(figsize=(10, 7), dpi=300)
    ax.set_xlim(0, 100)
    ax.set_ylim(0, 100)
    ax.axis('off')
    
    # Title
    ax.text(50, 96, "UML Use Case Diagram - Sip & Bite System", fontsize=14, fontweight='bold', ha='center', color='#1A365D')
    
    # System Boundary Box
    rect = patches.Rectangle((25, 5), 50, 85, linewidth=2, edgecolor='#2B6CB0', facecolor='#F7FAFC', linestyle='--')
    ax.add_patch(rect)
    ax.text(50, 87, "Sip & Bite Application System", fontsize=11, fontweight='bold', ha='center', color='#2B6CB0')
    
    # Draw Stick Figures / Actors
    def draw_actor(x, y, label):
        # Head
        circle = patches.Circle((x, y + 3), 1.8, edgecolor='#1A365D', facecolor='#E2E8F0', linewidth=1.5)
        ax.add_patch(circle)
        # Body
        ax.plot([x, x], [y + 1.2, y - 2.5], color='#1A365D', lw=1.5)
        # Arms
        ax.plot([x - 2, x + 2], [y, y], color='#1A365D', lw=1.5)
        # Legs
        ax.plot([x, x - 1.5], [y - 2.5, y - 5.5], color='#1A365D', lw=1.5)
        ax.plot([x, x + 1.5], [y - 2.5, y - 5.5], color='#1A365D', lw=1.5)
        # Label
        ax.text(x, y - 8, label, fontsize=10, fontweight='bold', ha='center', color='#1A365D')

    draw_actor(10, 50, "Customer")
    draw_actor(90, 70, "Admin / Staff")
    draw_actor(90, 30, "Delivery Person")

    # Use Cases (Ovals)
    use_cases = [
        (50, 80, "UC1: Auth & Profile Management"),
        (50, 72, "UC2: Browse Menu & Search Items"),
        (50, 64, "UC3: Cart Management & Checkout"),
        (50, 56, "UC4: Online Payment & COD"),
        (50, 48, "UC5: Reserve Dining Table"),
        (50, 40, "UC6: Live Order & Distance Track"),
        (50, 32, "UC7: Manage Categories & Menu"),
        (50, 24, "UC8: Assign Delivery & Update Status"),
        (50, 16, "UC9: View Sales Analytics & Reports"),
        (50, 8, "UC10: Submit Ratings & Feedback")
    ]

    uc_centers = {}
    for uc_id, (cx, cy, text) in enumerate(use_cases):
        ellipse = patches.Ellipse((cx, cy), 36, 5.5, edgecolor='#2B6CB0', facecolor='#EBF8FF', linewidth=1.2)
        ax.add_patch(ellipse)
        ax.text(cx, cy, text, fontsize=8.5, ha='center', va='center', color='#2D3748', fontweight='medium')
        uc_centers[uc_id] = (cx, cy)

    # Connect Actors to Use Cases
    # Customer connections (UC1..UC6, UC10)
    customer_x, customer_y = 12, 50
    for u_idx in [0, 1, 2, 3, 4, 5, 9]:
        cx, cy = uc_centers[u_idx]
        ax.plot([customer_x + 2, cx - 18], [customer_y, cy], color='#718096', linestyle='-', linewidth=1)

    # Admin connections (UC1, UC7, UC8, UC9)
    admin_x, admin_y = 88, 70
    for u_idx in [0, 6, 7, 8]:
        cx, cy = uc_centers[u_idx]
        ax.plot([admin_x - 2, cx + 18], [admin_y, cy], color='#718096', linestyle='-', linewidth=1)

    # Delivery Person connections (UC1, UC6, UC8)
    delivery_x, delivery_y = 88, 30
    for u_idx in [0, 5, 7]:
        cx, cy = uc_centers[u_idx]
        ax.plot([delivery_x - 2, cx + 18], [delivery_y, cy], color='#718096', linestyle='-', linewidth=1)

    plt.tight_layout()
    plt.savefig(output_path, format='png', bbox_inches='tight')
    plt.close()
    print(f"Generated Use Case Diagram at {output_path}")

def generate_class_diagram(output_path):
    fig, ax = plt.subplots(figsize=(10, 7.5), dpi=300)
    ax.set_xlim(0, 100)
    ax.set_ylim(0, 100)
    ax.axis('off')
    
    ax.text(50, 97, "UML Class Diagram - Sip & Bite Domain Model", fontsize=14, fontweight='bold', ha='center', color='#1A365D')

    classes = {
        "User": (8, 65, 24, 25, [
            "+ id: bigint (PK)",
            "+ name: string",
            "+ email: string",
            "+ phone: string",
            "+ role: enum",
            "-------------------",
            "+ register()",
            "+ login()",
            "+ updateProfile()"
        ]),
        "Order": (40, 65, 24, 25, [
            "+ id: bigint (PK)",
            "+ order_number: string",
            "+ user_id: bigint (FK)",
            "+ total_amount: decimal",
            "+ order_status: enum",
            "-------------------",
            "+ createOrder()",
            "+ cancelOrder()",
            "+ updateStatus()"
        ]),
        "OrderItem": (72, 65, 22, 22, [
            "+ id: bigint (PK)",
            "+ order_id: bigint (FK)",
            "+ menu_item_id: FK",
            "+ quantity: int",
            "+ total_price: decimal",
            "-------------------",
            "+ calculateSubtotal()"
        ]),
        "MenuItem": (72, 35, 22, 22, [
            "+ id: bigint (PK)",
            "+ category_id: FK",
            "+ name: string",
            "+ price: decimal",
            "+ is_veg: boolean",
            "-------------------",
            "+ getDetails()",
            "+ updateStock()"
        ]),
        "Category": (72, 5, 22, 22, [
            "+ id: bigint (PK)",
            "+ name: string",
            "+ slug: string",
            "+ status: enum",
            "-------------------",
            "+ addCategory()",
            "+ listItems()"
        ]),
        "TableBooking": (8, 30, 24, 24, [
            "+ id: bigint (PK)",
            "+ user_id: bigint (FK)",
            "+ booking_date: date",
            "+ booking_time: string",
            "+ guests_count: int",
            "-------------------",
            "+ bookTable()",
            "+ cancelBooking()"
        ]),
        "DeliveryPerson": (40, 30, 24, 24, [
            "+ id: bigint (PK)",
            "+ user_id: bigint (FK)",
            "+ vehicle_type: string",
            "+ is_available: bool",
            "+ current_lat/lng",
            "-------------------",
            "+ updateLocation()",
            "+ acceptDelivery()"
        ]),
        "Payment": (40, 2, 24, 20, [
            "+ id: bigint (PK)",
            "+ order_id: bigint (FK)",
            "+ amount: decimal",
            "+ status: enum",
            "-------------------",
            "+ processPayment()"
        ])
    }

    for name, (x, y, w, h, members) in classes.items():
        rect = patches.Rectangle((x, y), w, h, linewidth=1.5, edgecolor='#1A365D', facecolor='#F7FAFC')
        ax.add_patch(rect)
        # Class title bar
        title_rect = patches.Rectangle((x, y + h - 4), w, 4, linewidth=1.5, edgecolor='#1A365D', facecolor='#2B6CB0')
        ax.add_patch(title_rect)
        ax.text(x + w/2, y + h - 2.5, name, fontsize=9.5, fontweight='bold', ha='center', va='center', color='white')
        
        # Members text
        line_y = y + h - 6
        for m in members:
            if m.startswith("---"):
                ax.plot([x, x + w], [line_y, line_y], color='#CBD5E0', lw=1)
                line_y -= 2
                continue
            ax.text(x + 1, line_y, m, fontsize=7, color='#2D3748', va='center')
            line_y -= 1.8

    # Associations / Lines
    # User -> Order (1 to *)
    ax.annotate("", xy=(40, 77), xytext=(32, 77), arrowprops=dict(arrowstyle="->", color="#2B6CB0", lw=1.5))
    ax.text(34, 78.5, "1..*", fontsize=7.5, color='#2B6CB0', fontweight='bold')

    # Order -> OrderItem (1 to *)
    ax.annotate("", xy=(72, 76), xytext=(64, 76), arrowprops=dict(arrowstyle="->", color="#2B6CB0", lw=1.5))
    ax.text(66, 77.5, "1..*", fontsize=7.5, color='#2B6CB0', fontweight='bold')

    # OrderItem -> MenuItem (* to 1)
    ax.annotate("", xy=(83, 57), xytext=(83, 65), arrowprops=dict(arrowstyle="->", color="#2B6CB0", lw=1.5))

    # MenuItem -> Category (* to 1)
    ax.annotate("", xy=(83, 27), xytext=(83, 35), arrowprops=dict(arrowstyle="->", color="#2B6CB0", lw=1.5))

    # User -> TableBooking (1 to *)
    ax.annotate("", xy=(20, 54), xytext=(20, 65), arrowprops=dict(arrowstyle="->", color="#2B6CB0", lw=1.5))

    # Order -> DeliveryPerson (0..1 to 1)
    ax.annotate("", xy=(52, 54), xytext=(52, 65), arrowprops=dict(arrowstyle="->", color="#2B6CB0", lw=1.5))

    # Order -> Payment (1 to 1)
    ax.annotate("", xy=(52, 22), xytext=(52, 30), arrowprops=dict(arrowstyle="->", color="#2B6CB0", lw=1.5))

    plt.tight_layout()
    plt.savefig(output_path, format='png', bbox_inches='tight')
    plt.close()
    print(f"Generated Class Diagram at {output_path}")

def generate_activity_diagram(output_path):
    fig, ax = plt.subplots(figsize=(9, 8), dpi=300)
    ax.set_xlim(0, 100)
    ax.set_ylim(0, 100)
    ax.axis('off')
    
    ax.text(50, 97, "UML Activity Diagram - Order & Payment Workflow", fontsize=14, fontweight='bold', ha='center', color='#1A365D')

    # Start Node
    start = patches.Circle((50, 92), 2, color='#1A365D')
    ax.add_patch(start)
    ax.text(50, 95, "Start", fontsize=8, ha='center', fontweight='bold', color='#1A365D')

    steps = [
        (50, 83, "Customer Browses Menu & Selects Items"),
        (50, 73, "Adds to Cart & Confirms Address"),
        (50, 63, "Selects Payment Method (Razorpay / COD)"),
    ]

    for cx, cy, text in steps:
        rect = patches.FancyBboxPatch((cx - 28, cy - 3), 56, 6, boxstyle="round,pad=0.3", edgecolor='#2B6CB0', facecolor='#EBF8FF', lw=1.2)
        ax.add_patch(rect)
        ax.text(cx, cy, text, fontsize=8.5, ha='center', va='center', color='#1A365D', fontweight='medium')

    # Decision Diamond (Payment Check)
    diamond = patches.RegularPolygon((50, 51), 4, radius=5, edgecolor='#C53030', facecolor='#FFF5F5', lw=1.5)
    ax.add_patch(diamond)
    ax.text(50, 51, "Payment\nSuccess?", fontsize=7.5, ha='center', va='center', color='#9B2C2C', fontweight='bold')

    # Fail Path
    ax.annotate("", xy=(18, 51), xytext=(45, 51), arrowprops=dict(arrowstyle="->", color="#C53030", lw=1.2))
    ax.text(32, 52.5, "No (Payment Failed)", fontsize=7.5, color='#C53030', fontweight='bold')
    fail_box = patches.FancyBboxPatch((5, 48), 26, 6, boxstyle="round,pad=0.3", edgecolor='#C53030', facecolor='#FFF5F5', lw=1.2)
    ax.add_patch(fail_box)
    ax.text(18, 51, "Show Error & Retry", fontsize=8, ha='center', va='center', color='#C53030')

    # Success Path down
    ax.annotate("", xy=(50, 42), xytext=(50, 46), arrowprops=dict(arrowstyle="->", color="#2F855A", lw=1.2))
    ax.text(52, 44, "Yes / COD", fontsize=7.5, color='#2F855A', fontweight='bold')

    remaining_steps = [
        (50, 39, "Order Created (Status: Pending)"),
        (50, 29, "Kitchen/Admin Accepts Order (Status: Preparing)"),
        (50, 19, "Delivery Assigned & Order Dispatched (Out for Delivery)"),
        (50, 9, "Delivered to Customer & Payment Settled")
    ]

    for cx, cy, text in remaining_steps:
        rect = patches.FancyBboxPatch((cx - 28, cy - 3), 56, 6, boxstyle="round,pad=0.3", edgecolor='#2B6CB0', facecolor='#EBF8FF', lw=1.2)
        ax.add_patch(rect)
        ax.text(cx, cy, text, fontsize=8.5, ha='center', va='center', color='#1A365D', fontweight='medium')

    # Arrows connecting vertical steps
    ax.annotate("", xy=(50, 86), xytext=(50, 90), arrowprops=dict(arrowstyle="->", color="#1A365D", lw=1.2))
    ax.annotate("", xy=(50, 76), xytext=(50, 80), arrowprops=dict(arrowstyle="->", color="#1A365D", lw=1.2))
    ax.annotate("", xy=(50, 66), xytext=(50, 70), arrowprops=dict(arrowstyle="->", color="#1A365D", lw=1.2))
    ax.annotate("", xy=(50, 56), xytext=(50, 60), arrowprops=dict(arrowstyle="->", color="#1A365D", lw=1.2))
    ax.annotate("", xy=(50, 32), xytext=(50, 36), arrowprops=dict(arrowstyle="->", color="#1A365D", lw=1.2))
    ax.annotate("", xy=(50, 22), xytext=(50, 26), arrowprops=dict(arrowstyle="->", color="#1A365D", lw=1.2))
    ax.annotate("", xy=(50, 12), xytext=(50, 16), arrowprops=dict(arrowstyle="->", color="#1A365D", lw=1.2))
    ax.annotate("", xy=(50, 2), xytext=(50, 6), arrowprops=dict(arrowstyle="->", color="#1A365D", lw=1.2))

    # End Node
    end_outer = patches.Circle((50, 1), 1.8, edgecolor='#1A365D', facecolor='white', lw=1.2)
    end_inner = patches.Circle((50, 1), 1.0, color='#1A365D')
    ax.add_patch(end_outer)
    ax.add_patch(end_inner)
    ax.text(50, -2, "End", fontsize=8, ha='center', fontweight='bold', color='#1A365D')

    plt.tight_layout()
    plt.savefig(output_path, format='png', bbox_inches='tight')
    plt.close()
    print(f"Generated Activity Diagram at {output_path}")

if __name__ == "__main__":
    generate_use_case_diagram("use_case_diagram.png")
    generate_class_diagram("class_diagram.png")
    generate_activity_diagram("activity_diagram.png")
