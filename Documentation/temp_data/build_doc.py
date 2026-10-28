import os
import shutil
import docx
from docx import Document
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT, WD_ALIGN_VERTICAL
from docx.oxml import OxmlElement, parse_xml
from docx.oxml.ns import nsdecls, qn

def set_cell_background(cell, fill_color):
    """Sets background color for a table cell."""
    tcPr = cell._tc.get_or_add_tcPr()
    shd = parse_xml(f'<w:shd {nsdecls("w")} w:fill="{fill_color}"/>')
    tcPr.append(shd)

def set_cell_margins(cell, top=100, bottom=100, left=150, right=150):
    """Sets cell padding in dxa (1 pt = 20 dxa)."""
    tcPr = cell._tc.get_or_add_tcPr()
    tcMar = OxmlElement('w:tcMar')
    for m, val in [('top', top), ('bottom', bottom), ('left', left), ('right', right)]:
        node = OxmlElement(f'w:{m}')
        node.set(qn('w:w'), str(val))
        node.set(qn('w:type'), 'dxa')
        tcMar.append(node)
    tcPr.append(tcMar)

def set_table_borders(table, color="D3D3D3", sz="4", val="single"):
    """Sets subtle borders for a table."""
    tblPr = table._tbl.tblPr
    borders = parse_xml(
        f'<w:tblBorders {nsdecls("w")}>\n'
        f'  <w:top w:val="{val}" w:sz="{sz}" w:space="0" w:color="{color}"/>\n'
        f'  <w:bottom w:val="{val}" w:sz="{sz}" w:space="0" w:color="{color}"/>\n'
        f'  <w:left w:val="none"/>\n'
        f'  <w:right w:val="none"/>\n'
        f'  <w:insideH w:val="{val}" w:sz="{sz}" w:space="0" w:color="{color}"/>\n'
        f'  <w:insideV w:val="none"/>\n'
        f'</w:tblBorders>'
    )
    tblPr.append(borders)

def style_table(table, header_bg="1A365D", alt_bg="F7FAFC", col_widths=None):
    table.alignment = WD_TABLE_ALIGNMENT.CENTER
    set_table_borders(table)
    
    # Format Header Row
    hdr_cells = table.rows[0].cells
    for i, cell in enumerate(hdr_cells):
        set_cell_background(cell, header_bg)
        set_cell_margins(cell, top=140, bottom=140, left=150, right=150)
        for p in cell.paragraphs:
            p.alignment = WD_ALIGN_PARAGRAPH.LEFT
            for r in p.runs:
                r.font.name = 'Calibri'
                r.font.size = Pt(10)
                r.font.bold = True
                r.font.color.rgb = RGBColor(255, 255, 255)
                
    # Repeat header row across pages
    trPr = table.rows[0]._tr.get_or_add_trPr()
    trPr.append(parse_xml(f'<w:tblHeader {nsdecls("w")}/>'))

    # Format Data Rows
    for row_idx, row in enumerate(table.rows[1:], start=1):
        bg_color = alt_bg if row_idx % 2 == 1 else "FFFFFF"
        # CantSplit row across pages
        trPr = row._tr.get_or_add_trPr()
        trPr.append(parse_xml(f'<w:cantSplit {nsdecls("w")}/>'))
        
        for i, cell in enumerate(row.cells):
            set_cell_background(cell, bg_color)
            set_cell_margins(cell, top=100, bottom=100, left=150, right=150)
            for p in cell.paragraphs:
                for r in p.runs:
                    r.font.name = 'Calibri'
                    r.font.size = Pt(9.5)
                    r.font.color.rgb = RGBColor(45, 55, 72)
                    
    if col_widths:
        for row in table.rows:
            for i, w in enumerate(col_widths):
                row.cells[i].width = Inches(w)

def add_callout(doc, text, title="NOTE:"):
    tbl = doc.add_table(rows=1, cols=1)
    tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
    cell = tbl.cell(0, 0)
    set_cell_background(cell, "EBF8FF")
    set_cell_margins(cell, top=120, bottom=120, left=200, right=200)
    
    # Left border highlight
    tcPr = cell._tc.get_or_add_tcPr()
    borders = parse_xml(
        f'<w:tcBorders {nsdecls("w")}>\n'
        f'  <w:left w:val="single" w:sz="24" w:space="0" w:color="2B6CB0"/>\n'
        f'  <w:top w:val="none"/>\n'
        f'  <w:right w:val="none"/>\n'
        f'  <w:bottom w:val="none"/>\n'
        f'</w:tcBorders>'
    )
    tcPr.append(borders)
    
    p = cell.paragraphs[0]
    p.paragraph_format.space_before = Pt(2)
    p.paragraph_format.space_after = Pt(2)
    
    r_title = p.add_run(f"{title} ")
    r_title.font.name = 'Calibri'
    r_title.font.bold = True
    r_title.font.size = Pt(10)
    r_title.font.color.rgb = RGBColor(43, 108, 176)
    
    r_text = p.add_run(text)
    r_text.font.name = 'Calibri'
    r_text.font.size = Pt(9.5)
    r_text.font.italic = True
    r_text.font.color.rgb = RGBColor(45, 55, 72)
    
    doc.add_paragraph().paragraph_format.space_after = Pt(6)

def build_document():
    doc = Document()
    
    # Set Margins (1 inch all around)
    for section in doc.sections:
        section.top_margin = Inches(1.0)
        section.bottom_margin = Inches(1.0)
        section.left_margin = Inches(1.0)
        section.right_margin = Inches(1.0)
        section.header_distance = Inches(0.5)
        section.footer_distance = Inches(0.5)
        
        # Add Header / Footer
        header = section.header
        hp = header.paragraphs[0]
        hp.alignment = WD_ALIGN_PARAGRAPH.RIGHT
        hrun = hp.add_run("Sip & Bite System Documentation | Technical Specification")
        hrun.font.name = 'Calibri'
        hrun.font.size = Pt(8.5)
        hrun.font.color.rgb = RGBColor(113, 128, 150)
        
        footer = section.footer
        fp = footer.paragraphs[0]
        fp.alignment = WD_ALIGN_PARAGRAPH.CENTER
        frun = fp.add_run("Page Documentation File - Sip & Bite System")
        frun.font.name = 'Calibri'
        frun.font.size = Pt(8.5)
        frun.font.color.rgb = RGBColor(113, 128, 150)

    # Styles setup
    styles = doc.styles
    normal_style = styles['Normal']
    normal_style.font.name = 'Calibri'
    normal_style.font.size = Pt(11)
    normal_style.font.color.rgb = RGBColor(45, 55, 72)
    
    # Title Block
    title_p = doc.add_paragraph()
    title_p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    title_p.paragraph_format.space_before = Pt(36)
    title_p.paragraph_format.space_after = Pt(8)
    r_title = title_p.add_run("SOFTWARE REQUIREMENT & SYSTEM DESIGN SPECIFICATION")
    r_title.font.name = 'Calibri'
    r_title.font.size = Pt(22)
    r_title.font.bold = True
    r_title.font.color.rgb = RGBColor(26, 54, 93)

    sub_p = doc.add_paragraph()
    sub_p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    sub_p.paragraph_format.space_after = Pt(24)
    r_sub = sub_p.add_run("Sip & Bite - Smart Food Ordering & Table Reservation System")
    r_sub.font.name = 'Calibri'
    r_sub.font.size = Pt(14)
    r_sub.font.italic = True
    r_sub.font.color.rgb = RGBColor(43, 108, 176)

    # Metadata Card Table
    meta_tbl = doc.add_table(rows=4, cols=2)
    meta_tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
    meta_data = [
        ("Project Name:", "Sip & Bite (Food Ordering, Delivery & Table Booking Ecosystem)"),
        ("System Platforms:", "Web Application (Laravel PHP) & Android Mobile Application"),
        ("Document Version:", "1.0 Final Technical Specification"),
        ("Date of Generation:", "August 2026")
    ]
    for idx, (label, val) in enumerate(meta_data):
        row_cells = meta_tbl.rows[idx].cells
        set_cell_background(row_cells[0], "F7FAFC")
        set_cell_background(row_cells[1], "FFFFFF")
        set_cell_margins(row_cells[0], top=80, bottom=80, left=120, right=120)
        set_cell_margins(row_cells[1], top=80, bottom=80, left=120, right=120)
        
        p0 = row_cells[0].paragraphs[0]
        r0 = p0.add_run(label)
        r0.font.name = 'Calibri'
        r0.font.bold = True
        r0.font.size = Pt(10)
        r0.font.color.rgb = RGBColor(26, 54, 93)
        
        p1 = row_cells[1].paragraphs[0]
        r1 = p1.add_run(val)
        r1.font.name = 'Calibri'
        r1.font.size = Pt(10)
        r1.font.color.rgb = RGBColor(45, 55, 72)
    set_table_borders(meta_tbl, color="CBD5E0", sz="4")
    
    doc.add_paragraph().paragraph_format.space_after = Pt(24)

    # Helper for Headings
    def add_h1(text):
        p = doc.add_paragraph()
        p.paragraph_format.space_before = Pt(18)
        p.paragraph_format.space_after = Pt(8)
        p.paragraph_format.keep_with_next = True
        r = p.add_run(text)
        r.font.name = 'Calibri'
        r.font.size = Pt(16)
        r.font.bold = True
        r.font.color.rgb = RGBColor(26, 54, 93)
        return p

    def add_h2(text):
        p = doc.add_paragraph()
        p.paragraph_format.space_before = Pt(14)
        p.paragraph_format.space_after = Pt(6)
        p.paragraph_format.keep_with_next = True
        r = p.add_run(text)
        r.font.name = 'Calibri'
        r.font.size = Pt(13)
        r.font.bold = True
        r.font.color.rgb = RGBColor(43, 108, 176)
        return p

    def add_p(text, space_after=6):
        p = doc.add_paragraph()
        p.paragraph_format.space_after = Pt(space_after)
        p.paragraph_format.line_spacing = 1.15
        r = p.add_run(text)
        r.font.name = 'Calibri'
        r.font.size = Pt(10.5)
        r.font.color.rgb = RGBColor(45, 55, 72)
        return p

    def add_bullet(bold_prefix, text):
        p = doc.add_paragraph(style='List Bullet')
        p.paragraph_format.space_after = Pt(4)
        p.paragraph_format.line_spacing = 1.15
        r_bold = p.add_run(bold_prefix)
        r_bold.font.name = 'Calibri'
        r_bold.font.bold = True
        r_bold.font.size = Pt(10.5)
        r_bold.font.color.rgb = RGBColor(26, 54, 93)
        
        r_text = p.add_run(text)
        r_text.font.name = 'Calibri'
        r_text.font.size = Pt(10.5)
        r_text.font.color.rgb = RGBColor(45, 55, 72)
        return p

    # -------------------------------------------------------------
    # 1 INTRODUCTION
    # -------------------------------------------------------------
    add_h1("1 Introduction")
    add_p(
        "The Sip & Bite platform is an advanced, integrated digital solution designed to transform food ordering, "
        "table reservation, and delivery management for contemporary food service businesses. Combining a robust web application "
        "powered by Laravel PHP with a high-performance Android mobile application, Sip & Bite streamlines the dining and delivery "
        "workflow for customers, administrative management, kitchen staff, and delivery personnel."
    )
    
    add_callout(doc, "Sip & Bite integrates web administration, mobile ordering, table reservations, live distance tracking, and payment processing into a single cohesive platform.", "PROJECT OVERVIEW:")

    # 1.1 Proposed System and its Objectives
    add_h2("1.1 Proposed System and its Objectives")
    add_p(
        "Traditional restaurant operations often suffer from fragmented manual systems—such as paper-based menu ordering, telephone table bookings, "
        "undocumented cash handling, and lack of real-time delivery visibility. The proposed Sip & Bite system automates the entire restaurant ecosystem, "
        "providing a digital menu, automated cart calculations, instantaneous table reservation validation, online payments, and geospatial distance tracking."
    )
    add_p("The primary objectives of the Sip & Bite system are as follows:")
    
    add_bullet("1. Digitalization of Menu & Ordering: ", "Enable customers to seamlessly explore categorized food & beverage items, filter by dietary preferences (vegetarian vs. non-vegetarian), customize item quantities, and place orders digitally.")
    add_bullet("2. Real-Time Table Reservation System: ", "Provide customers with an intuitive table booking interface to reserve dining tables in advance, specifying date, time slots, guest capacity, and special dining requests, thereby preventing double bookings and reducing table idle time.")
    add_bullet("3. Geospatial Distance & Delivery Tracking: ", "Integrate location services and distance APIs to calculate precise delivery charges, estimate delivery lead times, and track delivery personnel dispatch in real time.")
    add_bullet("4. Multi-Role Operating Environment: ", "Establish role-based access control (RBAC) accommodating Administrators, Kitchen Staff, Delivery Partners, and Customers with tailored dashboards and functional permissions.")
    add_bullet("5. Seamless Payment Gateway Integration: ", "Support multi-channel payment flows, including Razorpay online transactions (Credit/Debit Card, UPI, Net Banking) and Cash on Delivery (COD), with robust status synchronization.")
    add_bullet("6. Quality Assurance & Customer Feedback: ", "Incorporate post-order and post-dining feedback mechanisms to collect ratings and qualitative reviews, assisting management in continuous service enhancement.")

    # 1.2 Core Components
    add_h2("1.2 Core Components")
    add_p("The Sip & Bite system architecture is built around five interconnected core modules:")
    
    add_bullet("1. Web Backend & Admin Subsystem (Laravel Framework): ", "Functions as the central engine managing business logic, routing, authentication, ORM database transactions, and RESTful API endpoints. The web portal features an intuitive Admin Dashboard for managing categories, menu items, table layouts, orders, delivery staff assignments, and financial reports.")
    add_bullet("2. Customer Android Mobile Application: ", "A native Android application offering an engaging user interface for end customers to register, browse rich food catalogs, manage shopping cart items, complete checkout, book tables, and track active order statuses live.")
    add_bullet("3. Delivery Partner Subsystem: ", "Integrated interface within the mobile application enabling delivery personnel to receive real-time order assignments, view customer delivery addresses with latitude/longitude coordinates, update delivery stages ('Out for Delivery' -> 'Delivered'), and mark payments collected.")
    add_bullet("4. Database Subsystem (MySQL): ", "A structured relational database storing system users, menu structures, table availability schedules, orders, order items, delivery profiles, transaction logs, and feedback ratings with strict foreign key constraints.")
    add_bullet("5. Geolocation & Payment API Integration Subsystems: ", "External service integrations including Razorpay API for cryptographic payment verifications and Distance Matrix APIs for accurate geographical distance computation.")

    # 1.3 Minimum and Maximum Software/Hardware Requirements
    add_h2("1.3 Minimum and Maximum Software/Hardware Requirements")
    add_p("To ensure optimal performance, stability, and responsiveness across web and mobile platforms, the system hardware and software specifications are categorized below into minimum operational and maximum recommended configurations.")

    req_table = doc.add_table(rows=1, cols=4)
    hdr_cells = req_table.rows[0].cells
    hdr_cells[0].text = "Component Layer"
    hdr_cells[1].text = "Specification Parameter"
    hdr_cells[2].text = "Minimum Requirement"
    hdr_cells[3].text = "Maximum / Recommended"

    req_data = [
        ("Server Side (Web Backend)", "Operating System", "Ubuntu Linux 20.04 LTS", "Ubuntu Linux 24.04 LTS / Cloud Server"),
        ("Server Side (Web Backend)", "CPU Processor", "Dual-Core 2.0 GHz", "Octa-Core 3.5 GHz+ (Intel Xeon / AMD EPYC)"),
        ("Server Side (Web Backend)", "System Memory (RAM)", "4 GB DDR4", "16 GB+ DDR5"),
        ("Server Side (Web Backend)", "Storage Capacity", "30 GB SSD", "250 GB+ NVMe SSD"),
        ("Server Side (Web Backend)", "Software Stack", "PHP 8.2, MySQL 8.0, Apache/NGINX", "PHP 8.3+, MySQL 8.0+, NGINX, Redis Cache"),
        ("Client Web Interface", "Device & Display", "Desktop/Laptop (1024x768 display)", "Full HD 1080p Desktop/Laptop/Tablet"),
        ("Client Web Interface", "Browser Engine", "Google Chrome 90+, Firefox 88+", "Latest Google Chrome / Safari / Edge"),
        ("Client Web Interface", "Network Bandwidth", "2 Mbps Broadband", "100 Mbps+ High-Speed Fiber"),
        ("Client Mobile App (Android)", "Mobile Device OS", "Android 8.0 (Oreo / API Level 26)", "Android 14 / 15 (Latest API)"),
        ("Client Mobile App (Android)", "Device Memory & Storage", "2 GB RAM, 100 MB free storage", "6 GB+ RAM, 500 MB free storage"),
        ("Client Mobile App (Android)", "Connectivity & GPS", "3G Mobile Data, Basic GPS", "4G LTE / 5G Network, Precision Dual-GPS")
    ]

    for layer, param, min_req, rec_req in req_data:
        row_cells = req_table.add_row().cells
        row_cells[0].text = layer
        row_cells[1].text = param
        row_cells[2].text = min_req
        row_cells[3].text = rec_req

    style_table(req_table, header_bg="1A365D", alt_bg="F7FAFC", col_widths=[1.5, 1.5, 1.7, 1.8])
    doc.add_paragraph().paragraph_format.space_after = Pt(12)

    # 1.4 Advantages and Limitations of the Proposed System
    add_h2("1.4 Advantages and Limitations of the Proposed System")
    
    add_p("Advantages of the Proposed System:")
    add_bullet("• Automated Workflow & Error Reduction: ", "Eliminates manual pen-and-paper order processing, preventing order misinterpretations, incorrect billing, and table booking overlaps.")
    add_bullet("• Enhanced Customer Convenience: ", "Allows customers to place delivery orders or reserve dining tables anywhere, anytime via mobile app or web portal without waiting in long queues.")
    add_bullet("• Real-Time Order & Location Visibility: ", "Provides step-by-step transparency from order placement to kitchen preparation, delivery dispatch, and real-time destination arrival.")
    add_bullet("• Integrated Payment Security: ", "Eliminates cash dependency while guaranteeing PCI-DSS compliant online payment transactions powered by Razorpay signature verification.")
    add_bullet("• Centralized Business Intelligence: ", "Equips restaurant managers with real-time revenue analytics, popular menu trends, active delivery person availability, and feedback metrics.")

    add_p("Limitations of the Proposed System:")
    add_bullet("• Internet Connectivity Reliance: ", "Both web backend and mobile app interfaces require active internet connectivity; offline operation is limited to local cache viewing.")
    add_bullet("• Device Hardware Dependency: ", "Delivery personnel and mobile app users require GPS-enabled Android smartphones with functional camera sensors for scanning/navigation.")
    add_bullet("• Initial Deployment & Training Effort: ", "Requires initial configuration of server infrastructure, database migrations, and staff onboarding for kitchen and delivery workflows.")

    # -------------------------------------------------------------
    # 2 REQUIREMENT DETERMINATION & ANALYSIS
    # -------------------------------------------------------------
    add_h1("2 Requirement Determination & Analysis")
    add_p(
        "Requirement determination and analysis establish the foundational specifications of the Sip & Bite system. "
        "This phase involves defining explicit functional interactions, operational constraints, performance criteria, "
        "and categorizing the target user groups."
    )

    # 2.1 Requirement Determination
    add_h2("2.1 Requirement Determination")
    add_p("The system requirements are split into Functional Requirements (FR) and Non-Functional Requirements (NFR).")

    add_p("Functional Requirements (FR):", space_after=4)
    fr_list = [
        ("FR-01 User Authentication & Authorization: ", "The system shall allow users to register, log in, and manage profiles with role-based permissions (Customer, Admin, Delivery Person)."),
        ("FR-02 Category & Menu Management: ", "Administrators shall be able to create, update, activate/deactivate menu categories and individual menu items with pricing, imagery, vegetarian flags, and availability status."),
        ("FR-03 Cart & Checkout Processing: ", "Customers shall be able to add menu items to a persistent shopping cart, modify item quantities, enter delivery addresses, and calculate taxes and delivery fees dynamically."),
        ("FR-04 Table Reservation Management: ", "Customers shall be able to book dining tables by selecting date, time slot, and guest count. The system shall validate table capacity and prevent conflicting reservations."),
        ("FR-05 Order Processing & Tracking: ", "The system shall process orders through distinct operational statuses ('Pending', 'Preparing', 'Out for Delivery', 'Delivered', 'Cancelled') and reflect changes across customer and admin dashboards."),
        ("FR-06 Delivery Partner Assignment & Distance Computation: ", "Administrators shall assign available delivery personnel to pending orders. The system shall calculate geographical distance to compute delivery fees and estimated transit times."),
        ("FR-07 Payment Processing: ", "The system shall support Cash on Delivery (COD) and online payments via Razorpay API, verifying transaction IDs cryptographically before order completion."),
        ("FR-08 Feedback & Rating Mechanism: ", "Customers shall be able to submit star ratings (1 to 5) and textual reviews for completed orders and dining experiences.")
    ]
    for code, desc in fr_list:
        add_bullet(code, desc)

    add_p("Non-Functional Requirements (NFR):", space_after=4)
    nfr_list = [
        ("NFR-01 Performance & Response Time: ", "API endpoints shall respond within 300 milliseconds under standard load conditions. Web application pages shall load in under 2 seconds."),
        ("NFR-02 Security & Data Protection: ", "All HTTP communications shall enforce HTTPS/TLS encryption. Sensitive credentials and passwords must be hashed using bcrypt before database storage."),
        ("NFR-03 Reliability & Uptime: ", "The system shall maintain 99.9% uptime. Database transactions must adhere to ACID properties to guarantee data consistency during concurrent ordering."),
        ("NFR-04 Scalability: ", "The architecture shall support horizontal scaling of web servers and database read-replicas to handle peak dining and holiday traffic spikes."),
        ("NFR-05 Usability & Accessibility: ", "Mobile and web interfaces shall follow responsive, ergonomic design principles with intuitive navigation suitable for diverse user demographics.")
    ]
    for code, desc in nfr_list:
        add_bullet(code, desc)

    # 2.2 Targeted Users
    add_h2("2.2 Targeted Users")
    add_p("The Sip & Bite system caters to three primary user personas, each interacting with specific system components tailored to their operational goals.")

    user_tbl = doc.add_table(rows=1, cols=4)
    u_hdr = user_tbl.rows[0].cells
    u_hdr[0].text = "User Category"
    u_hdr[1].text = "Target Roles"
    u_hdr[2].text = "Primary System Objectives"
    u_hdr[3].text = "Key System Functions Utilized"

    user_matrix = [
        ("System Administrator", "Restaurant Owner, Managers, Head Chefs", "Oversee restaurant business, manage menu offerings, monitor table bookings, assign delivery staff, view financial analytics.", "Admin Web Dashboard, Menu CRUD, Table Booking Approval, Delivery Assignment, Revenue Analytics."),
        ("End Customer", "Dine-in Guests, Takeaway & Delivery Clients", "Browse food options, place food delivery orders, reserve dining tables, pay online, track live orders, provide feedback.", "Android Mobile App, Web Client, Cart & Checkout, Table Booking, Razorpay Gateway, Order Tracking, Feedback."),
        ("Delivery Personnel", "In-house Drivers, Courier Partners", "Receive delivery assignments, view customer locations, navigate transit routes, deliver orders, update fulfillment status.", "Android Mobile App (Delivery Module), Order Accept/Status Toggle, Distance Tracking, COD Payment Collection.")
    ]

    for cat, roles, obj, funcs in user_matrix:
        r_cells = user_tbl.add_row().cells
        r_cells[0].text = cat
        r_cells[1].text = roles
        r_cells[2].text = obj
        r_cells[3].text = funcs

    style_table(user_tbl, header_bg="1A365D", alt_bg="F7FAFC", col_widths=[1.3, 1.4, 1.9, 1.9])
    doc.add_paragraph().paragraph_format.space_after = Pt(12)

    # -------------------------------------------------------------
    # 3 SYSTEM DESIGN
    # -------------------------------------------------------------
    add_h1("3 System Design")
    add_p(
        "System design translates functional requirements into visual structural and behavioral models. "
        "This section presents Unified Modeling Language (UML) diagrams—including Use Case Diagram, Class Diagram, "
        "and Activity Diagram—followed by a comprehensive Data Dictionary."
    )

    # 3.1 Use Case Diagram
    add_h2("3.1 Use Case Diagram")
    add_p(
        "The Use Case Diagram illustrates the interactions between primary system actors (Customer, Admin/Staff, Delivery Person) "
        "and the core functional modules within the Sip & Bite system boundary."
    )
    
    if os.path.exists("use_case_diagram.png"):
        p_img = doc.add_paragraph()
        p_img.alignment = WD_ALIGN_PARAGRAPH.CENTER
        p_img.paragraph_format.space_before = Pt(8)
        p_img.paragraph_format.space_after = Pt(4)
        run_img = p_img.add_run()
        run_img.add_picture("use_case_diagram.png", width=Inches(6.0))
        
        p_cap = doc.add_paragraph()
        p_cap.alignment = WD_ALIGN_PARAGRAPH.CENTER
        p_cap.paragraph_format.space_after = Pt(12)
        r_cap = p_cap.add_run("Figure 3.1: UML Use Case Diagram for Sip & Bite System")
        r_cap.font.name = 'Calibri'
        r_cap.font.size = Pt(9.5)
        r_cap.font.italic = True
        r_cap.font.color.rgb = RGBColor(113, 128, 150)

    add_p("Use Case Descriptions & Actor Interactions:")
    add_bullet("• UC1 Auth & Profile Management: ", "Enables Customers, Admins, and Delivery Personnel to authenticate securely, reset credentials, and maintain profile information.")
    add_bullet("• UC2 Browse Menu & Search: ", "Allows Customers to view categorized food items, filter by veg/non-veg tags, and search by dish name.")
    add_bullet("• UC3 Cart Management & Checkout: ", "Enables Customers to accumulate items, adjust quantities, specify delivery addresses, and compute total fees.")
    add_bullet("• UC4 Online Payment & COD: ", "Supports payment execution via Razorpay gateway signature verification or Cash on Delivery selection.")
    add_bullet("• UC5 Reserve Dining Table: ", "Allows Customers to select table dates, times, and party sizes, storing reservations in the database.")
    add_bullet("• UC6 Live Order & Distance Tracking: ", "Provides real-time visibility into order status transitions and spatial distance calculation for transit routes.")
    add_bullet("• UC7 Manage Categories & Menu: ", "Empowers Admins to add, edit, or disable menu items and categories dynamically.")
    add_bullet("• UC8 Assign Delivery & Update Status: ", "Allows Admins to assign delivery staff to orders and enables Delivery Personnel to update delivery progress.")

    # 3.2 Class Diagram
    add_h2("3.2 Class Diagram")
    add_p(
        "The Class Diagram models the static structure of the Sip & Bite system, depicting core domain entities, "
        "their key attributes, member methods, and relational associations (one-to-many, one-to-one)."
    )

    if os.path.exists("class_diagram.png"):
        p_img = doc.add_paragraph()
        p_img.alignment = WD_ALIGN_PARAGRAPH.CENTER
        p_img.paragraph_format.space_before = Pt(8)
        p_img.paragraph_format.space_after = Pt(4)
        run_img = p_img.add_run()
        run_img.add_picture("class_diagram.png", width=Inches(6.0))
        
        p_cap = doc.add_paragraph()
        p_cap.alignment = WD_ALIGN_PARAGRAPH.CENTER
        p_cap.paragraph_format.space_after = Pt(12)
        r_cap = p_cap.add_run("Figure 3.2: UML Class Diagram depicting Entity Relationships")
        r_cap.font.name = 'Calibri'
        r_cap.font.size = Pt(9.5)
        r_cap.font.italic = True
        r_cap.font.color.rgb = RGBColor(113, 128, 150)

    add_p("Key Domain Entities and Structural Associations:")
    add_bullet("• User Entity: ", "Base entity storing user credentials, phone numbers, and roles. Associated with Order (1 to Many), TableBooking (1 to Many), Address (1 to Many), and Feedback (1 to Many).")
    add_bullet("• Category & MenuItem Entities: ", "Category maintains a 1-to-Many relationship with MenuItem. Each MenuItem belongs to one Category and is referenced by multiple OrderItems.")
    add_bullet("• Order & OrderItem Entities: ", "Order represents a customer purchase transaction (1-to-Many with OrderItem, 1-to-1 with Payment). It links to User, DeliveryPerson, and Address foreign keys.")
    add_bullet("• TableBooking & Table Entities: ", "TableBooking records dining reservations for a specific User, optionally linked to a physical Table entity.")
    add_bullet("• DeliveryPerson Entity: ", "Extends user profile data with vehicle attributes, availability toggles, and geolocation lat/lng parameters.")

    # 3.3 Activity Diagram
    add_h2("3.3 Activity Diagram")
    add_p(
        "The Activity Diagram specifies the dynamic execution flow of the food ordering, payment verification, "
        "kitchen fulfillment, and delivery dispatch process."
    )

    if os.path.exists("activity_diagram.png"):
        p_img = doc.add_paragraph()
        p_img.alignment = WD_ALIGN_PARAGRAPH.CENTER
        p_img.paragraph_format.space_before = Pt(8)
        p_img.paragraph_format.space_after = Pt(4)
        run_img = p_img.add_run()
        run_img.add_picture("activity_diagram.png", width=Inches(5.5))
        
        p_cap = doc.add_paragraph()
        p_cap.alignment = WD_ALIGN_PARAGRAPH.CENTER
        p_cap.paragraph_format.space_after = Pt(12)
        r_cap = p_cap.add_run("Figure 3.3: UML Activity Diagram for Order Placement & Delivery Workflow")
        r_cap.font.name = 'Calibri'
        r_cap.font.size = Pt(9.5)
        r_cap.font.italic = True
        r_cap.font.color.rgb = RGBColor(113, 128, 150)

    add_p("Step-by-Step Activity Sequence:")
    add_bullet("1. Order Initiation: ", "Customer browses menu categories, adds desired dishes to the cart, and proceeds to checkout.")
    add_bullet("2. Delivery Address & Fee Computation: ", "Customer selects a saved address. The system computes taxes and distance-based delivery fees.")
    add_bullet("3. Payment Decision Node: ", "Customer selects Razorpay or Cash on Delivery. For online payments, Razorpay SDK executes cryptographic payment validation.")
    add_bullet("4. Order Confirmation & Kitchen Dispatch: ", "Upon payment success or COD selection, the order status is set to 'Pending'. Admin/Kitchen approves the order and sets status to 'Preparing'.")
    add_bullet("5. Delivery Assignment & Transit: ", "Admin assigns a delivery partner. Status transitions to 'Out for Delivery'. Delivery partner navigates to customer coordinates.")
    add_bullet("6. Order Completion: ", "Delivery partner hands over the food, collects COD payment (if applicable), and marks order 'Delivered'. Customer submits feedback.")

    # 3.4 Data Dictionary (table format)
    add_h2("3.4 Data Dictionary (table format)")
    add_p(
        "The Data Dictionary defines the precise relational database schema implemented in MySQL for the Sip & Bite application. "
        "It outlines table structures, field definitions, data types, key constraints, nullability, and field descriptions."
    )

    data_dicts = {
        "1. Table Name: users (User Accounts & Authentication)": [
            ("id", "BIGINT UNSIGNED", "PRIMARY KEY, AUTO_INC", "No", "Unique user account identifier"),
            ("name", "VARCHAR(255)", "None", "No", "Full name of the user"),
            ("email", "VARCHAR(255)", "UNIQUE", "No", "User email address for login"),
            ("email_verified_at", "TIMESTAMP", "None", "Yes", "Timestamp when email was verified"),
            ("password", "VARCHAR(255)", "None", "No", "Bcrypt hashed account password"),
            ("phone", "VARCHAR(20)", "None", "Yes", "Contact phone number"),
            ("role", "ENUM('admin','customer','delivery')", "DEFAULT 'customer'", "No", "System access role"),
            ("remember_token", "VARCHAR(100)", "None", "Yes", "Session remember token"),
            ("created_at / updated_at", "TIMESTAMP", "None", "Yes", "Record creation and modification timestamps")
        ],
        "2. Table Name: categories (Food Menu Categories)": [
            ("id", "BIGINT UNSIGNED", "PRIMARY KEY, AUTO_INC", "No", "Unique category identifier"),
            ("name", "VARCHAR(255)", "None", "No", "Category display name (e.g., Beverages)"),
            ("slug", "VARCHAR(255)", "UNIQUE", "No", "URL friendly category slug"),
            ("image", "VARCHAR(255)", "None", "Yes", "Path to category cover image"),
            ("description", "TEXT", "None", "Yes", "Detailed category description"),
            ("status", "ENUM('active','inactive')", "DEFAULT 'active'", "No", "Category visibility state"),
            ("created_at / updated_at", "TIMESTAMP", "None", "Yes", "Audit timestamps")
        ],
        "3. Table Name: menu_items (Food & Drink Catalog)": [
            ("id", "BIGINT UNSIGNED", "PRIMARY KEY, AUTO_INC", "No", "Unique menu item identifier"),
            ("category_id", "BIGINT UNSIGNED", "FOREIGN KEY (categories.id)", "No", "Parent category reference"),
            ("name", "VARCHAR(255)", "None", "No", "Name of the dish or drink"),
            ("description", "TEXT", "None", "Yes", "Ingredients and dish details"),
            ("price", "DECIMAL(10,2)", "None", "No", "Item unit price in local currency"),
            ("image", "VARCHAR(255)", "None", "Yes", "Path to item photo asset"),
            ("is_available", "BOOLEAN", "DEFAULT TRUE", "No", "Current kitchen availability flag"),
            ("is_veg", "BOOLEAN", "DEFAULT TRUE", "No", "Vegetarian (True) vs Non-Veg (False)"),
            ("rating", "DECIMAL(3,2)", "DEFAULT 4.50", "No", "Aggregated star rating score"),
            ("created_at / updated_at", "TIMESTAMP", "None", "Yes", "Audit timestamps")
        ],
        "4. Table Name: tables (Dining Table Information)": [
            ("id", "BIGINT UNSIGNED", "PRIMARY KEY, AUTO_INC", "No", "Unique table identifier"),
            ("table_number", "VARCHAR(50)", "UNIQUE", "No", "Table identifier number or code"),
            ("capacity", "INT", "None", "No", "Maximum seating capacity"),
            ("location", "VARCHAR(100)", "None", "Yes", "Table location (Indoor, Terrace, VIP)"),
            ("status", "ENUM('available','booked','maintenance')", "DEFAULT 'available'", "No", "Current operational table status"),
            ("created_at / updated_at", "TIMESTAMP", "None", "Yes", "Audit timestamps")
        ],
        "5. Table Name: table_bookings (Customer Table Reservations)": [
            ("id", "BIGINT UNSIGNED", "PRIMARY KEY, AUTO_INC", "No", "Unique booking reservation ID"),
            ("user_id", "BIGINT UNSIGNED", "FOREIGN KEY (users.id)", "No", "Customer who made reservation"),
            ("table_id", "BIGINT UNSIGNED", "FOREIGN KEY (tables.id)", "Yes", "Assigned physical table reference"),
            ("booking_date", "DATE", "None", "No", "Reserved dining date"),
            ("booking_time", "VARCHAR(50)", "None", "No", "Reserved dining time slot"),
            ("guests_count", "INT", "None", "No", "Number of guests attending"),
            ("special_request", "TEXT", "None", "Yes", "Dietary/seating preferences"),
            ("status", "ENUM('pending','confirmed','completed','cancelled')", "DEFAULT 'pending'", "No", "Booking fulfillment status"),
            ("created_at / updated_at", "TIMESTAMP", "None", "Yes", "Audit timestamps")
        ],
        "6. Table Name: delivery_persons (Delivery Staff Profiles)": [
            ("id", "BIGINT UNSIGNED", "PRIMARY KEY, AUTO_INC", "No", "Unique delivery staff profile ID"),
            ("user_id", "BIGINT UNSIGNED", "FOREIGN KEY (users.id)", "No", "Linked user account ID"),
            ("vehicle_type", "VARCHAR(50)", "None", "No", "Type of vehicle (Bike, Scooter)"),
            ("vehicle_number", "VARCHAR(50)", "None", "No", "Vehicle license plate number"),
            ("current_lat", "DECIMAL(10,8)", "None", "Yes", "Live GPS latitude coordinate"),
            ("current_lng", "DECIMAL(11,8)", "None", "Yes", "Live GPS longitude coordinate"),
            ("is_available", "BOOLEAN", "DEFAULT TRUE", "No", "Staff active duty status"),
            ("created_at / updated_at", "TIMESTAMP", "None", "Yes", "Audit timestamps")
        ],
        "7. Table Name: addresses (Customer Delivery Addresses)": [
            ("id", "BIGINT UNSIGNED", "PRIMARY KEY, AUTO_INC", "No", "Unique address identifier"),
            ("user_id", "BIGINT UNSIGNED", "FOREIGN KEY (users.id)", "No", "Customer owning address"),
            ("address_line", "TEXT", "None", "No", "Street address and landmark"),
            ("city", "VARCHAR(100)", "None", "No", "City name"),
            ("state", "VARCHAR(100)", "None", "No", "State / Province name"),
            ("pincode", "VARCHAR(20)", "None", "No", "Postal code / Zip code"),
            ("lat", "DECIMAL(10,8)", "None", "Yes", "Geocoded latitude coordinate"),
            ("lng", "DECIMAL(11,8)", "None", "Yes", "Geocoded longitude coordinate"),
            ("is_default", "BOOLEAN", "DEFAULT FALSE", "No", "Primary address indicator"),
            ("created_at / updated_at", "TIMESTAMP", "None", "Yes", "Audit timestamps")
        ],
        "8. Table Name: orders (Food Delivery & Takeaway Orders)": [
            ("id", "BIGINT UNSIGNED", "PRIMARY KEY, AUTO_INC", "No", "Unique order record identifier"),
            ("order_number", "VARCHAR(255)", "UNIQUE", "No", "Human readable order number (e.g., ORD1001)"),
            ("user_id", "BIGINT UNSIGNED", "FOREIGN KEY (users.id)", "No", "Customer placing order"),
            ("delivery_person_id", "BIGINT UNSIGNED", "FOREIGN KEY (delivery_persons.id)", "Yes", "Assigned delivery agent ID"),
            ("address_id", "BIGINT UNSIGNED", "FOREIGN KEY (addresses.id)", "Yes", "Target delivery destination"),
            ("subtotal", "DECIMAL(10,2)", "None", "No", "Sum of menu item prices"),
            ("tax", "DECIMAL(10,2)", "DEFAULT 0.00", "No", "Applied tax amount"),
            ("delivery_fee", "DECIMAL(10,2)", "DEFAULT 0.00", "No", "Distance computed delivery fee"),
            ("total_amount", "DECIMAL(10,2)", "None", "No", "Final order grand total"),
            ("payment_method", "ENUM('COD','Razorpay')", "DEFAULT 'COD'", "No", "Selected payment channel"),
            ("payment_status", "ENUM('pending','paid','failed')", "DEFAULT 'pending'", "No", "Payment settlement status"),
            ("razorpay_order_id", "VARCHAR(255)", "None", "Yes", "External Razorpay order ID"),
            ("razorpay_payment_id", "VARCHAR(255)", "None", "Yes", "External Razorpay transaction reference"),
            ("order_status", "ENUM('Pending','Preparing','Out for Delivery','Delivered','Cancelled')", "DEFAULT 'Pending'", "No", "Current fulfillment stage"),
            ("notes", "TEXT", "None", "Yes", "Customer cooking/delivery instructions"),
            ("created_at / updated_at", "TIMESTAMP", "None", "Yes", "Audit timestamps")
        ],
        "9. Table Name: order_items (Individual Order Line Items)": [
            ("id", "BIGINT UNSIGNED", "PRIMARY KEY, AUTO_INC", "No", "Unique order item line ID"),
            ("order_id", "BIGINT UNSIGNED", "FOREIGN KEY (orders.id)", "No", "Parent order reference"),
            ("menu_item_id", "BIGINT UNSIGNED", "FOREIGN KEY (menu_items.id)", "No", "Ordered menu item reference"),
            ("quantity", "INT", "None", "No", "Quantity of dish ordered"),
            ("unit_price", "DECIMAL(10,2)", "None", "No", "Price per unit at order time"),
            ("total_price", "DECIMAL(10,2)", "None", "No", "Total line cost (quantity * unit_price)"),
            ("created_at / updated_at", "TIMESTAMP", "None", "Yes", "Audit timestamps")
        ],
        "10. Table Name: payments (Transaction Settlement Records)": [
            ("id", "BIGINT UNSIGNED", "PRIMARY KEY, AUTO_INC", "No", "Unique payment record ID"),
            ("order_id", "BIGINT UNSIGNED", "FOREIGN KEY (orders.id)", "No", "Associated order ID"),
            ("transaction_id", "VARCHAR(255)", "UNIQUE", "Yes", "Gateway payment transaction code"),
            ("payment_method", "VARCHAR(50)", "None", "No", "Payment channel (COD / UPI / Card)"),
            ("amount", "DECIMAL(10,2)", "None", "No", "Total monetary amount processed"),
            ("status", "ENUM('pending','success','failed')", "DEFAULT 'pending'", "No", "Final payment gateway verification state"),
            ("created_at / updated_at", "TIMESTAMP", "None", "Yes", "Audit timestamps")
        ],
        "11. Table Name: feedback (Customer Ratings & Reviews)": [
            ("id", "BIGINT UNSIGNED", "PRIMARY KEY, AUTO_INC", "No", "Unique feedback record ID"),
            ("user_id", "BIGINT UNSIGNED", "FOREIGN KEY (users.id)", "No", "Customer submitting review"),
            ("order_id", "BIGINT UNSIGNED", "FOREIGN KEY (orders.id)", "Yes", "Associated order ID"),
            ("rating", "INT", "Check (1 to 5)", "No", "Star rating given (1 to 5)"),
            ("comments", "TEXT", "None", "Yes", "Qualitative review comment text"),
            ("created_at / updated_at", "TIMESTAMP", "None", "Yes", "Audit timestamps")
        ]
    }

    for t_title, fields in data_dicts.items():
        add_h3_p = doc.add_paragraph()
        add_h3_p.paragraph_format.space_before = Pt(10)
        add_h3_p.paragraph_format.space_after = Pt(4)
        add_h3_p.paragraph_format.keep_with_next = True
        r3 = add_h3_p.add_run(t_title)
        r3.font.name = 'Calibri'
        r3.font.size = Pt(11)
        r3.font.bold = True
        r3.font.color.rgb = RGBColor(26, 54, 93)

        dict_tbl = doc.add_table(rows=1, cols=5)
        d_hdr = dict_tbl.rows[0].cells
        d_hdr[0].text = "Field Name"
        d_hdr[1].text = "Data Type"
        d_hdr[2].text = "Key / Constraints"
        d_hdr[3].text = "Nullable"
        d_hdr[4].text = "Description & Purpose"

        for field, dtype, key, null_flag, desc in fields:
            r_cells = dict_tbl.add_row().cells
            r_cells[0].text = field
            r_cells[1].text = dtype
            r_cells[2].text = key
            r_cells[3].text = null_flag
            r_cells[4].text = desc

        style_table(dict_tbl, header_bg="1A365D", alt_bg="F7FAFC", col_widths=[1.4, 1.3, 1.4, 0.7, 1.7])
        doc.add_paragraph().paragraph_format.space_after = Pt(8)

    # -------------------------------------------------------------
    # 7 CONCLUSION
    # -------------------------------------------------------------
    add_h1("7 CONCLUSION")
    add_p(
        "The Sip & Bite Smart Food Ordering and Table Reservation System presents a complete, modern, and scalable "
        "technological foundation for restaurant management and food delivery operations. By harmonizing a responsive Laravel PHP "
        "web backend with an intuitive native Android application, Sip & Bite replaces cumbersome paper workflows with real-time digital automation."
    )
    add_p(
        "Key achievements and architectural strengths demonstrated in this system design include:"
    )
    add_bullet("1. End-to-End Operational Integration: ", "Seamless synchronization across Customer, Administrative, Kitchen, and Delivery modules ensures real-time status visibility and zero operational lag.")
    add_bullet("2. Robust Data Model & Integrity: ", "The relational database schema, documented comprehensively in the Data Dictionary, enforces foreign key integrity, ACID-compliant order transactions, and optimized spatial queries.")
    add_bullet("3. Geolocation & Payment Efficiency: ", "Distance-based automated fee calculations and Razorpay payment verification provide financial accuracy and transparent customer pricing.")
    add_bullet("4. Scalable Architecture: ", "The modular separation of web APIs, database layers, and mobile clients allows easy future enhancements—such as AI-powered dish recommendations, automated kitchen display systems (KDS), and customer loyalty rewards.")

    add_p(
        "In conclusion, the Sip & Bite system successfully fulfills all functional, technical, and operational objectives, "
        "delivering superior dining convenience to customers and driving business growth for restaurant operators."
    )

    # Save documents
    docx_file = "Sip_N_Bite_System_Documentation.docx"
    doc.save(docx_file)
    print(f"Successfully saved {docx_file}")

    # Make a copy as .doc file
    doc_file = "Sip_N_Bite_System_Documentation.doc"
    shutil.copyfile(docx_file, doc_file)
    print(f"Successfully created copy {doc_file}")

if __name__ == "__main__":
    build_document()
