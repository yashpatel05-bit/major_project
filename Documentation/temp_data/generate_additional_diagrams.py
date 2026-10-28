import os
import matplotlib.pyplot as plt
import matplotlib.patches as patches
import numpy as np

out_dir = r'd:\SIP_N_BITE\Documentation\final_doc\generated_diagrams'
os.makedirs(out_dir, exist_ok=True)

# ----------------------------------------------------
# 1. Customer Food Ordering & Razorpay Payment Sequence Diagram
# ----------------------------------------------------
def generate_customer_sequence_diagram():
    fig, ax = plt.subplots(figsize=(11, 8.5), dpi=300)
    ax.set_xlim(0, 100)
    ax.set_ylim(0, 100)
    ax.axis('off')
    
    # Title
    ax.text(50, 96, "3.3.1 Sequence Diagram: Customer Food Ordering & Online Payment Flow", 
            fontsize=12, fontweight='bold', ha='center', color='#1A365D')
    
    # Lifelines / Participants
    participants = [
        (10, "Customer\n(Mobile App)"),
        (30, "Cart / Checkout\n(React Native UI)"),
        (52, "OrderApiController\n(Laravel 11 REST)"),
        (74, "Razorpay\nGateway API"),
        (92, "MySQL Database\n(sipnbite)")
    ]
    
    lifeline_x = {}
    for x, label in participants:
        lifeline_x[label] = x
        # Box header
        box = patches.FancyBboxPatch((x - 8, 86), 16, 6, boxstyle="round,pad=0.3,rounding_size=1",
                                     edgecolor='#4a779d', facecolor='#b8e2f2', linewidth=1.5)
        ax.add_patch(box)
        ax.text(x, 89, label, fontsize=8, fontweight='bold', ha='center', va='center', color='#0c3847')
        
        # Dashed lifeline
        ax.plot([x, x], [86, 10], color='#a0aec0', linestyle='--', linewidth=1.2, zorder=1)
        
        # Bottom box
        box_bot = patches.FancyBboxPatch((x - 8, 4), 16, 5, boxstyle="round,pad=0.3,rounding_size=1",
                                         edgecolor='#4a779d', facecolor='#b8e2f2', linewidth=1.5)
        ax.add_patch(box_bot)
        ax.text(x, 6.5, label.split('\n')[0], fontsize=7.5, fontweight='bold', ha='center', va='center', color='#0c3847')

    # Activations & Messages
    # Helper for messages
    def draw_msg(y, x1, x2, label, is_return=False, note=None):
        style = '--' if is_return else '-'
        color = '#2b6cb0' if not is_return else '#4a5568'
        ax.annotate('', xy=(x2, y), xytext=(x1, y),
                    arrowprops=dict(arrowstyle="->", color=color, lw=1.3, linestyle=style))
        mid_x = (x1 + x2) / 2
        ax.text(mid_x, y + 1.2, label, fontsize=8, ha='center', va='bottom', color='#1a202c', fontweight='medium')
        if note:
            ax.text(mid_x, y - 1.8, note, fontsize=7, ha='center', va='top', color='#718096', style='italic')

    # Steps
    draw_msg(80, 10, 30, "1: Click 'Place Order & Pay'")
    draw_msg(73, 30, 52, "2: POST /api/checkout/initiate-payment")
    draw_msg(66, 52, 74, "3: Create Razorpay Order (amount, receipt)")
    draw_msg(60, 74, 52, "4: return razorpay_order_id & signature", is_return=True)
    draw_msg(54, 52, 30, "5: return order options & key_id", is_return=True)
    draw_msg(47, 30, 74, "6: Launch Razorpay Checkout Modal (Cards/UPI)")
    draw_msg(40, 74, 30, "7: Payment Success (razorpay_payment_id)", is_return=True)
    draw_msg(33, 30, 52, "8: POST /api/checkout/verify (signature, id)")
    draw_msg(26, 52, 92, "9: INSERT INTO orders & payments (status='paid')")
    draw_msg(20, 92, 52, "10: Transaction Committed (Order #1042)", is_return=True)
    draw_msg(14, 52, 30, "11: return { status: 'success', order_id: 1042 }", is_return=True)
    draw_msg(9, 30, 10, "12: Display Order Confirmation & Navigate to Tracking", is_return=True)

    plt.tight_layout()
    out_path = os.path.join(out_dir, "interaction_customer_order_seq.png")
    plt.savefig(out_path, format='png', bbox_inches='tight')
    plt.close()
    print("Saved:", out_path)

# ----------------------------------------------------
# 2. Admin Order Management & Invoice Sequence Diagram
# ----------------------------------------------------
def generate_admin_sequence_diagram():
    fig, ax = plt.subplots(figsize=(11, 8.5), dpi=300)
    ax.set_xlim(0, 100)
    ax.set_ylim(0, 100)
    ax.axis('off')
    
    ax.text(50, 96, "3.3.2 Sequence Diagram: Admin Order Management & Invoice Generation Flow", 
            fontsize=12, fontweight='bold', ha='center', color='#1A365D')
    
    participants = [
        (10, "Admin\n(Web Browser)"),
        (30, "OrderController\n(Admin Controller)"),
        (52, "InvoiceController\n(PDF Engine)"),
        (74, "MySQL Database\n(sipnbite)"),
        (92, "Customer App\n(Push Notification)")
    ]
    
    for x, label in participants:
        box = patches.FancyBboxPatch((x - 8, 86), 16, 6, boxstyle="round,pad=0.3,rounding_size=1",
                                     edgecolor='#4a779d', facecolor='#b8e2f2', linewidth=1.5)
        ax.add_patch(box)
        ax.text(x, 89, label, fontsize=8, fontweight='bold', ha='center', va='center', color='#0c3847')
        ax.plot([x, x], [86, 10], color='#a0aec0', linestyle='--', linewidth=1.2, zorder=1)
        
        box_bot = patches.FancyBboxPatch((x - 8, 4), 16, 5, boxstyle="round,pad=0.3,rounding_size=1",
                                         edgecolor='#4a779d', facecolor='#b8e2f2', linewidth=1.5)
        ax.add_patch(box_bot)
        ax.text(x, 6.5, label.split('\n')[0], fontsize=7.5, fontweight='bold', ha='center', va='center', color='#0c3847')

    def draw_msg(y, x1, x2, label, is_return=False):
        style = '--' if is_return else '-'
        color = '#2b6cb0' if not is_return else '#4a5568'
        ax.annotate('', xy=(x2, y), xytext=(x1, y),
                    arrowprops=dict(arrowstyle="->", color=color, lw=1.3, linestyle=style))
        mid_x = (x1 + x2) / 2
        ax.text(mid_x, y + 1.2, label, fontsize=8, ha='center', va='bottom', color='#1a202c', fontweight='medium')

    draw_msg(80, 10, 30, "1: GET /admin/orders (View Pending Orders)")
    draw_msg(73, 30, 74, "2: SELECT * FROM orders WHERE status='pending'")
    draw_msg(66, 74, 30, "3: return pending orders list", is_return=True)
    draw_msg(60, 30, 10, "4: Render orders table in Admin Dashboard", is_return=True)
    draw_msg(52, 10, 30, "5: POST /admin/orders/1042/update-status ('preparing')")
    draw_msg(45, 30, 74, "6: UPDATE orders SET order_status='preparing'")
    draw_msg(39, 30, 92, "7: Send Live Push Alert: 'Kitchen is preparing your meal'")
    draw_msg(32, 10, 52, "8: GET /admin/invoices/1042/generate")
    draw_msg(25, 52, 74, "9: SELECT order, customer, items, tax, payment details")
    draw_msg(19, 74, 52, "10: return comprehensive invoice dataset", is_return=True)
    draw_msg(13, 52, 10, "11: Stream Printable GST Tax Invoice PDF", is_return=True)

    plt.tight_layout()
    out_path = os.path.join(out_dir, "interaction_admin_invoice_seq.png")
    plt.savefig(out_path, format='png', bbox_inches='tight')
    plt.close()
    print("Saved:", out_path)

# ----------------------------------------------------
# 3. Collaboration / Communication Diagram
# ----------------------------------------------------
def generate_collaboration_diagram():
    fig, ax = plt.subplots(figsize=(10, 7), dpi=300)
    ax.set_xlim(0, 100)
    ax.set_ylim(0, 100)
    ax.axis('off')
    
    ax.text(50, 96, "3.3.3 UML Collaboration / Communication Diagram - Sip N Bite", 
            fontsize=12, fontweight='bold', ha='center', color='#1A365D')
    
    nodes = {
        "Customer": (15, 75, "1: Customer\n(Client Device)"),
        "CartManager": (50, 75, "2: CartManager\n(Context/Session)"),
        "OrderApi": (85, 75, "3: OrderApiController\n(Laravel REST)"),
        "Razorpay": (85, 25, "4: RazorpayService\n(Payment Gateway)"),
        "Database": (50, 25, "5: MySQL Database\n(Data Layer)"),
        "InvoiceGen": (15, 25, "6: InvoiceGenerator\n(PDF Engine)")
    }
    
    for k, (x, y, text) in nodes.items():
        box = patches.FancyBboxPatch((x - 12, y - 6), 24, 12, boxstyle="round,pad=0.3,rounding_size=1.5",
                                     edgecolor='#4a779d', facecolor='#e8f4fd', linewidth=1.5)
        ax.add_patch(box)
        ax.text(x, y, text, fontsize=8.5, fontweight='bold', ha='center', va='center', color='#0c3847')

    # Links & Numbers
    def draw_link(k1, k2, labels, offset_y=0):
        x1, y1 = nodes[k1][0], nodes[k1][1]
        x2, y2 = nodes[k2][0], nodes[k2][1]
        ax.plot([x1, x2], [y1, y2], color='#4a779d', lw=1.5)
        mid_x, mid_y = (x1 + x2) / 2, (y1 + y2) / 2 + offset_y
        for i, lbl in enumerate(labels):
            ax.text(mid_x, mid_y + (len(labels)-1-i)*2.5 - 1, lbl, fontsize=7.5,
                    ha='center', va='center', color='#1a202c', fontweight='semibold',
                    bbox=dict(boxstyle="square,pad=0.15", facecolor='white', edgecolor='none', alpha=0.85))

    draw_link("Customer", "CartManager", ["1: addItem()", "2: checkout()"], offset_y=3)
    draw_link("CartManager", "OrderApi", ["3: postOrder(items, address)"], offset_y=3)
    draw_link("OrderApi", "Razorpay", ["4: initiatePayment()", "5: verifySignature()"], offset_y=0)
    draw_link("OrderApi", "Database", ["6: insertOrder()", "7: insertOrderItems()"], offset_y=3)
    draw_link("Database", "InvoiceGen", ["8: fetchOrderDetails()"], offset_y=-3)
    draw_link("InvoiceGen", "Customer", ["9: returnInvoicePDF()"], offset_y=0)

    plt.tight_layout()
    out_path = os.path.join(out_dir, "interaction_collaboration_diagram.png")
    plt.savefig(out_path, format='png', bbox_inches='tight')
    plt.close()
    print("Saved:", out_path)

# ----------------------------------------------------
# 4. Deployment Diagram (4-Tier)
# ----------------------------------------------------
def generate_deployment_diagram():
    fig, ax = plt.subplots(figsize=(11, 8), dpi=300)
    ax.set_xlim(0, 100)
    ax.set_ylim(0, 100)
    ax.axis('off')
    
    ax.text(50, 96, "3.5 UML Deployment Diagram - Sip N Bite (4-Tier Architecture)", 
            fontsize=12, fontweight='bold', ha='center', color='#1A365D')
    
    # 4 Tier Boxes
    tiers = [
        (5, 50, 20, 40, "Client Tier (Mobile & Web)", "#EBF8FF", "#2B6CB0"),
        (28, 50, 22, 40, "App Server Tier (Laravel 11)", "#E6FFFA", "#234E52"),
        (53, 50, 20, 40, "Database Tier (MySQL 8.0)", "#FFFAF0", "#7B341E"),
        (76, 50, 20, 40, "Cloud Services Tier", "#FAF5FF", "#553C9A")
    ]
    
    for x, y, w, h, title, bg, border in tiers:
        rect = patches.FancyBboxPatch((x, y - h/2), w, h, boxstyle="round,pad=0.3,rounding_size=1.5",
                                     edgecolor=border, facecolor=bg, linewidth=1.5, linestyle='--')
        ax.add_patch(rect)
        ax.text(x + w/2, y + h/2 - 3, title, fontsize=9, fontweight='bold', ha='center', color=border)

    # Artifacts inside Tiers
    # Client Tier
    ax.add_patch(patches.Rectangle((7, 56), 16, 12, edgecolor='#3182CE', facecolor='#BEE3F8', lw=1.2))
    ax.text(15, 63, "«device» Android Device\nSipNBite.apk (React Native)\nAxios API Client", 
            fontsize=7, ha='center', va='center', fontweight='medium')
    
    ax.add_patch(patches.Rectangle((7, 34), 16, 12, edgecolor='#3182CE', facecolor='#BEE3F8', lw=1.2))
    ax.text(15, 41, "«device» Admin PC\nWeb Browser (Chrome/Edge)\nBlade Views + Chart.js", 
            fontsize=7, ha='center', va='center', fontweight='medium')

    # App Server Tier
    ax.add_patch(patches.Rectangle((30, 36), 18, 30, edgecolor='#285E61', facecolor='#B2F5EA', lw=1.2))
    ax.text(39, 52, "«execution environment»\nApache/Nginx (Port 8000)\nPHP 8.2+ CLI / FPM\nLaravel 11 Engine\n• routes/api.php\n• OrderController\n• ChatbotController\n• InvoiceController", 
            fontsize=7, ha='center', va='center', fontweight='medium')

    # Database Tier
    ax.add_patch(patches.Rectangle((55, 38), 16, 26, edgecolor='#9C4221', facecolor='#FEEBC8', lw=1.2))
    ax.text(63, 52, "«database» MySQL 8.0\nPort 3306\nsipnbite Schema\n• users, categories\n• menu_items, orders\n• order_items, payments\n• tables, bookings", 
            fontsize=7, ha='center', va='center', fontweight='medium')

    # Cloud Services Tier
    ax.add_patch(patches.Rectangle((78, 60), 16, 10, edgecolor='#6B46C1', facecolor='#E9D8FD', lw=1.2))
    ax.text(86, 65, "Razorpay Cloud\nPayment API Gateway", fontsize=7.5, ha='center', va='center', fontweight='bold')
    
    ax.add_patch(patches.Rectangle((78, 46), 16, 10, edgecolor='#6B46C1', facecolor='#E9D8FD', lw=1.2))
    ax.text(86, 51, "Google Gemini AI\n1.5 Flash Model", fontsize=7.5, ha='center', va='center', fontweight='bold')
    
    ax.add_patch(patches.Rectangle((78, 32), 16, 10, edgecolor='#6B46C1', facecolor='#E9D8FD', lw=1.2))
    ax.text(86, 37, "Distance Matrix API\nDelivery GPS Router", fontsize=7.5, ha='center', va='center', fontweight='bold')

    # Connections
    # Client to App Server
    ax.annotate('', xy=(30, 62), xytext=(23, 62), arrowprops=dict(arrowstyle="<->", color='#2B6CB0', lw=1.5))
    ax.text(26.5, 63.5, "REST JSON\n(8000)", fontsize=6.5, ha='center', color='#2B6CB0', fontweight='bold')
    
    ax.annotate('', xy=(30, 42), xytext=(23, 42), arrowprops=dict(arrowstyle="<->", color='#2B6CB0', lw=1.5))
    ax.text(26.5, 43.5, "HTTP/HTML\n(8000)", fontsize=6.5, ha='center', color='#2B6CB0', fontweight='bold')

    # App Server to Database
    ax.annotate('', xy=(55, 52), xytext=(48, 52), arrowprops=dict(arrowstyle="<->", color='#9C4221', lw=1.5))
    ax.text(51.5, 54, "TCP/IP\n(3306)", fontsize=6.5, ha='center', color='#9C4221', fontweight='bold')

    # App Server to Cloud
    ax.annotate('', xy=(78, 65), xytext=(48, 62), arrowprops=dict(arrowstyle="<->", color='#6B46C1', lw=1.3))
    ax.annotate('', xy=(78, 51), xytext=(48, 52), arrowprops=dict(arrowstyle="<->", color='#6B46C1', lw=1.3))
    ax.annotate('', xy=(78, 37), xytext=(48, 42), arrowprops=dict(arrowstyle="<->", color='#6B46C1', lw=1.3))
    ax.text(63, 67, "HTTPS / TLS 1.3 (Port 443)", fontsize=7, color='#6B46C1', fontweight='bold', ha='center')

    # Mobile directly to Razorpay for SDK Modal
    ax.annotate('', xy=(78, 67), xytext=(23, 65),
                arrowprops=dict(arrowstyle="->", color='#805AD5', lw=1.2, linestyle='dotted'))

    plt.tight_layout()
    out_path = os.path.join(out_dir, "deployment_diagram.png")
    plt.savefig(out_path, format='png', bbox_inches='tight')
    plt.close()
    print("Saved:", out_path)

# ----------------------------------------------------
# 5. Agile Burndown & Burnup & EV Charts
# ----------------------------------------------------
def generate_agile_charts():
    # Burndown Chart
    fig, ax = plt.subplots(figsize=(9, 5), dpi=300)
    days = np.arange(1, 15)
    ideal = np.linspace(80, 0, 14)
    actual = [80, 78, 72, 68, 65, 55, 52, 44, 38, 28, 20, 14, 6, 0]
    
    ax.plot(days, ideal, label='Ideal Burndown (Guideline)', color='#718096', linestyle='--', lw=2)
    ax.plot(days, actual, label='Actual Remaining Story Points', color='#E53E3E', marker='o', lw=2.5)
    ax.fill_between(days, actual, color='#FED7D7', alpha=0.3)
    
    ax.set_title("Sprint 1 Burndown Chart (Story Points vs. Time)", fontsize=11, fontweight='bold', color='#1A365D')
    ax.set_xlabel("Sprint Days (Day 1 - 14)", fontsize=9, fontweight='semibold')
    ax.set_ylabel("Remaining Story Points", fontsize=9, fontweight='semibold')
    ax.set_xticks(days)
    ax.grid(True, linestyle=':', alpha=0.6)
    ax.legend(frameon=True, facecolor='#F7FAFC')
    
    plt.tight_layout()
    out_path1 = os.path.join(out_dir, "sprint_burndown_chart.png")
    plt.savefig(out_path1, format='png', bbox_inches='tight')
    plt.close()
    print("Saved:", out_path1)

    # Burnup Chart
    fig, ax = plt.subplots(figsize=(9, 5), dpi=300)
    total_scope = [80]*14
    completed = [0, 2, 8, 12, 15, 25, 28, 36, 42, 52, 60, 66, 74, 80]
    
    ax.plot(days, total_scope, label='Total Scope (Story Points)', color='#2B6CB0', lw=2.5)
    ax.plot(days, completed, label='Completed Work (Actual)', color='#38A169', marker='s', lw=2.5)
    ax.fill_between(days, completed, color='#C6F6D5', alpha=0.35)
    
    ax.set_title("Sprint 1 Burnup Chart (Scope vs. Completed Story Points)", fontsize=11, fontweight='bold', color='#1A365D')
    ax.set_xlabel("Sprint Days (Day 1 - 14)", fontsize=9, fontweight='semibold')
    ax.set_ylabel("Story Points", fontsize=9, fontweight='semibold')
    ax.set_xticks(days)
    ax.grid(True, linestyle=':', alpha=0.6)
    ax.legend(frameon=True, facecolor='#F7FAFC')
    
    plt.tight_layout()
    out_path2 = os.path.join(out_dir, "sprint_burnup_chart.png")
    plt.savefig(out_path2, format='png', bbox_inches='tight')
    plt.close()
    print("Saved:", out_path2)

    # Earned Value Chart
    fig, ax = plt.subplots(figsize=(9, 5), dpi=300)
    sprints = [1, 2, 3, 4]
    pv = [20000, 45000, 70000, 95000] # Planned Value (INR)
    ev = [19500, 46000, 71500, 95000] # Earned Value (INR)
    ac = [18500, 43000, 68000, 91000] # Actual Cost (INR)
    
    ax.plot(sprints, pv, label='Planned Value (PV)', color='#3182CE', marker='o', lw=2)
    ax.plot(sprints, ev, label='Earned Value (EV)', color='#38A169', marker='^', lw=2.5)
    ax.plot(sprints, ac, label='Actual Cost (AC)', color='#DD6B20', marker='d', lw=2)
    
    ax.set_title("Agile Project Earned Value Analysis (EVA)", fontsize=11, fontweight='bold', color='#1A365D')
    ax.set_xlabel("Project Sprints (Sprint 1 - 4)", fontsize=9, fontweight='semibold')
    ax.set_ylabel("Value / Cost (INR)", fontsize=9, fontweight='semibold')
    ax.set_xticks(sprints)
    ax.grid(True, linestyle=':', alpha=0.6)
    ax.legend(frameon=True, facecolor='#F7FAFC')
    
    # Text annotation for CPI and SPI
    ax.text(1.5, 80000, "Cost Performance Index (CPI) = 1.04 (Under Budget)\nSchedule Performance Index (SPI) = 1.01 (On Schedule)", 
            fontsize=8.5, bbox=dict(boxstyle="round,pad=0.4", facecolor='#EDF2F7', edgecolor='#CBD5E0'))
    
    plt.tight_layout()
    out_path3 = os.path.join(out_dir, "earned_value_chart.png")
    plt.savefig(out_path3, format='png', bbox_inches='tight')
    plt.close()
    print("Saved:", out_path3)

if __name__ == '__main__':
    generate_customer_sequence_diagram()
    generate_admin_sequence_diagram()
    generate_collaboration_diagram()
    generate_deployment_diagram()
    generate_agile_charts()
    print("All additional diagrams generated successfully!")
