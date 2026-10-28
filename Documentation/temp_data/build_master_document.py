import os
import shutil
import docx
from docx import Document
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT
from docx.oxml import OxmlElement, parse_xml
from docx.oxml.ns import nsdecls, qn

from builder_foundation import (
    output_docx_path, root_copy_path,
    img_base_ppt, img_base_diva, img_base_gen, img_base_media,
    set_cell_background, set_cell_margins, set_table_borders, format_table,
    add_callout_box, add_fig_image, add_h1, add_h2, add_h3, add_p, add_bullet
)

def build_full_document():
    doc = Document()
    
    # Page Margins (1 inch all around)
    for section in doc.sections:
        section.top_margin = Inches(1.0)
        section.bottom_margin = Inches(1.0)
        section.left_margin = Inches(1.0)
        section.right_margin = Inches(1.0)
        section.header_distance = Inches(0.5)
        section.footer_distance = Inches(0.5)
        
        # Header / Footer
        header = section.header
        hp = header.paragraphs[0]
        hp.alignment = WD_ALIGN_PARAGRAPH.RIGHT
        hrun = hp.add_run("Sip N' Bite — Smart Café & Food Ordering System | Technical Project Documentation")
        hrun.font.name = 'Calibri'
        hrun.font.size = Pt(8.5)
        hrun.font.color.rgb = RGBColor(113, 128, 150)
        
        footer = section.footer
        fp = footer.paragraphs[0]
        fp.alignment = WD_ALIGN_PARAGRAPH.CENTER
        frun = fp.add_run("Sarvajanik University, Surat — MCA Semester-3 | Software Project-2")
        frun.font.name = 'Calibri'
        frun.font.size = Pt(8.5)
        frun.font.color.rgb = RGBColor(113, 128, 150)

    styles = doc.styles
    normal_style = styles['Normal']
    normal_style.font.name = 'Calibri'
    normal_style.font.size = Pt(10)
    normal_style.font.color.rgb = RGBColor(45, 55, 72)
    
    # =========================================================================
    # COVER PAGE
    # =========================================================================
    p_logo = doc.add_paragraph()
    p_logo.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_logo.paragraph_format.space_before = Pt(12)
    p_logo.paragraph_format.space_after = Pt(8)
    univ_logo = os.path.join(img_base_diva, "image1.png")
    if os.path.exists(univ_logo):
        p_logo.add_run().add_picture(univ_logo, width=Inches(4.5))

    p_title = doc.add_paragraph()
    p_title.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_title.paragraph_format.space_before = Pt(20)
    p_title.paragraph_format.space_after = Pt(6)
    r_t = p_title.add_run("SIP N' BITE")
    r_t.font.name = 'Calibri'
    r_t.font.size = Pt(26)
    r_t.font.bold = True
    r_t.font.color.rgb = RGBColor(26, 54, 93)

    p_sub = doc.add_paragraph()
    p_sub.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_sub.paragraph_format.space_after = Pt(16)
    r_s = p_sub.add_run("INTEGRATED SMART CAFÉ ORDERING & TABLE RESERVATION PLATFORM\n(Laravel 11 Web Backend + React Native Android Mobile Application)")
    r_s.font.name = 'Calibri'
    r_s.font.size = Pt(12)
    r_s.font.bold = True
    r_s.font.color.rgb = RGBColor(43, 108, 176)

    # University Crest
    crest_logo = os.path.join(img_base_diva, "image2.png")
    if os.path.exists(crest_logo):
        p_crest = doc.add_paragraph()
        p_crest.alignment = WD_ALIGN_PARAGRAPH.CENTER
        p_crest.paragraph_format.space_after = Pt(16)
        p_crest.add_run().add_picture(crest_logo, width=Inches(1.8))

    # Meta Table
    doc.add_paragraph().paragraph_format.space_after = Pt(4)
    tbl_meta = doc.add_table(rows=5, cols=2)
    tbl_meta.alignment = WD_TABLE_ALIGNMENT.CENTER
    meta_info = [
        ("Course / Academic Program:", "Master of Computer Applications (MCA) — Semester 3"),
        ("Subject:", "Software Project - 2"),
        ("Project Submitted By:", "Chauhan Deep (ET25MTCA005)\nPatel Yash (ET25MTCA047)\nViras Prathamesh (ET25MTCA066)"),
        ("Institution / University:", "Sarvajanik University, Surat, Gujarat"),
        ("Academic Year:", "2025 – 2026")
    ]
    for idx, (lbl, val) in enumerate(meta_info):
        r_c = tbl_meta.rows[idx].cells
        set_cell_background(r_c[0], "F7FAFC")
        set_cell_background(r_c[1], "FFFFFF")
        set_cell_margins(r_c[0], top=60, bottom=60, left=100, right=100)
        set_cell_margins(r_c[1], top=60, bottom=60, left=100, right=100)
        
        p0 = r_c[0].paragraphs[0]
        r0 = p0.add_run(lbl)
        r0.font.name = 'Calibri'
        r0.font.bold = True
        r0.font.size = Pt(9.5)
        r0.font.color.rgb = RGBColor(26, 54, 93)
        
        p1 = r_c[1].paragraphs[0]
        r1 = p1.add_run(val)
        r1.font.name = 'Calibri'
        r1.font.size = Pt(9.5)
        r1.font.color.rgb = RGBColor(45, 55, 72)
    set_table_borders(tbl_meta, color="CBD5E0", sz="4")
    
    doc.add_page_break()

    # =========================================================================
    # CERTIFICATE & ACKNOWLEDGEMENT
    # =========================================================================
    add_h1(doc, "CERTIFICATE OF ORIGINAL WORK")
    add_p(doc, "This is to certify that the project entitled \"Sip N' Bite — Integrated Smart Café Ordering & Table Reservation Platform\" has been successfully submitted by:")
    
    cert_tbl = doc.add_table(rows=4, cols=3)
    cert_tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
    cert_headers = ["Student Name", "Enrollment Number", "Signature"]
    for i, h in enumerate(cert_headers):
        cert_tbl.rows[0].cells[i].paragraphs[0].text = h
    cert_data = [
        ("Chauhan Deep", "ET25MTCA005", "____________________"),
        ("Patel Yash", "ET25MTCA047", "____________________"),
        ("Viras Prathamesh", "ET25MTCA066", "____________________")
    ]
    for r_idx, row in enumerate(cert_data, start=1):
        for c_idx, val in enumerate(row):
            cert_tbl.rows[r_idx].cells[c_idx].paragraphs[0].text = val
    format_table(cert_tbl, header_bg="1A365D", col_widths=[2.5, 2.0, 2.0])
    
    add_p(doc, "\nThis work is submitted towards the partial fulfillment for the subject Software Project-2 in 2nd/3rd Semester of MCA, Sarvajanik University, Surat during the academic year 2025–2026. The technical implementation, diagrams, system designs, REST APIs, and mobile applications presented in this documentation represent the authentic and original engineering effort of the candidates.")
    
    doc.add_paragraph().paragraph_format.space_after = Pt(24)
    sig_tbl = doc.add_table(rows=2, cols=2)
    sig_tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
    sig_tbl.rows[0].cells[0].paragraphs[0].text = "______________________________\nInternal Project Guide"
    sig_tbl.rows[0].cells[1].paragraphs[0].text = "______________________________\nHead of Department (MCA)"
    sig_tbl.rows[1].cells[0].paragraphs[0].text = "Date: ________________________"
    sig_tbl.rows[1].cells[1].paragraphs[0].text = "College Seal / Stamp"
    for r in sig_tbl.rows:
        for c in r.cells:
            for p in c.paragraphs:
                for run in p.runs:
                    run.font.name = 'Calibri'
                    run.font.size = Pt(9.5)
                    run.font.bold = True
    set_table_borders(sig_tbl, val="none")

    doc.add_page_break()

    # Acknowledgement
    add_h1(doc, "ACKNOWLEDGEMENT")
    add_p(doc, "We wish to express our profound gratitude to our project guide, faculty mentors, and the Department of Computer Applications at Sarvajanik University, Surat for their indispensable guidance, technical critiques, and encouragement throughout the design and realization of the Sip N' Bite platform.")
    add_p(doc, "We also express our appreciation to the open-source engineering communities behind Laravel 11, React Native, Expo, and Google Gemini AI, whose advanced toolsets enabled us to engineer a production-ready, synchronized Web and Android ecosystem.")
    add_p(doc, "Submitted with respect,\nChauhan Deep (ET25MTCA005)\nPatel Yash (ET25MTCA047)\nViras Prathamesh (ET25MTCA066)")

    doc.add_page_break()

    # =========================================================================
    # TABLE OF CONTENTS (EXACT IMAGE 1 REPRODUCTION)
    # =========================================================================
    add_h1(doc, "TABLE OF CONTENTS / INDEX")
    add_p(doc, "The comprehensive structure of this document strictly adheres to the official MCA Project Documentation Guidelines shown below:")
    
    toc_tbl = doc.add_table(rows=8, cols=3)
    toc_tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
    toc_tbl.rows[0].cells[0].paragraphs[0].text = "Sr. No"
    toc_tbl.rows[0].cells[1].paragraphs[0].text = "Content"
    toc_tbl.rows[0].cells[2].paragraphs[0].text = "Page No."
    
    toc_rows = [
        ("1.", "Introduction\n  1.1. Proposed system and its Objectives\n  1.2. Core Components\n  1.3. Minimum and Maximum Software/Hardware requirements\n  1.4. Advantages and Limitations of the Proposed System", "4"),
        ("2.", "Requirement Determination & Analysis\n  2.1. Requirement Determination\n  2.2. Targeted Users", "8"),
        ("3.", "System Design\n  3.1. Use Case Diagram\n  3.2. Class Diagram\n  3.3. Interaction Diagram\n  3.4. Activity Diagram\n  3.5. Deployment Diagram\n  3.6. Data Dictionary", "11"),
        ("4.", "Agile Documentation\n  4.1. Agile Project Charter\n  4.2. Agile Roadmap / Schedule / Agile Project Plan\n  4.3. Agile User Story (Minimum 3 Tasks)\n  4.4. Agile Release Plan\n  4.5. Agile Sprint Backlog\n  4.6. Agile Test Plan\n  4.7. Earned-value and burn charts", "22"),
        ("5.", "Screenshots\n  5.1. Admin Web Dashboard & Order Invoicing Screens\n  5.2. Customer React Native Android Mobile Application Screens", "32"),
        ("6.", "Conclusion", "38"),
        ("7.", "Bibliography", "39")
    ]
    for idx, (sr, content, page) in enumerate(toc_rows, start=1):
        toc_tbl.rows[idx].cells[0].paragraphs[0].text = sr
        toc_tbl.rows[idx].cells[1].paragraphs[0].text = content
        toc_tbl.rows[idx].cells[2].paragraphs[0].text = page
    format_table(toc_tbl, header_bg="1A365D", col_widths=[0.8, 5.0, 0.9])
    
    add_callout_box(doc, 
        "In accordance with project guidelines and evaluator requests, Section 8 is included as a dedicated technical appendix providing exhaustive documentation of all API Keys, Sensitive Credentials, Network Endpoints, Database Configurations, and Outside Resources.",
        title="SPECIAL DOCUMENTATION NOTE:")

    doc.add_page_break()

    # =========================================================================
    # CHAPTER 1: INTRODUCTION
    # =========================================================================
    add_h1(doc, "1. Introduction")
    add_p(doc, "In the modern hospitality and quick-service restaurant (QSR) sector, the convergence of web management and mobile customer experiences has transformed the dining experience. Traditional food ordering workflows frequently suffer from fragmented paper-based ticketing, manual calculation errors, delayed kitchen handoffs, and rigid reservation tracking. Sip N' Bite is an enterprise-grade, integrated digital café management ecosystem designed to unite real-time web administration with a high-performance Android mobile application.")
    
    add_h2(doc, "1.1. Proposed System and its Objectives")
    add_p(doc, "The proposed Sip N' Bite platform bridges the gap between customer convenience and back-office restaurant operations. It features a dual-interface architecture: a responsive Web Admin Portal powered by Laravel 11 for the restaurant owner/manager, and a modern Android Mobile Application engineered with React Native and Expo for diners.")
    
    add_p(doc, "Core Objectives of the Proposed System:")
    add_bullet(doc, "1. Digitalization of Menu & Ordering: ", "Enable customers to seamlessly explore categorized culinary offerings, customize quantities, review detailed pricing with vegetarian/non-vegetarian indicators, and add items to a dynamic client-side shopping cart.")
    add_bullet(doc, "2. Real-Time Table Reservation: ", "Implement an automated dining table allocation engine that allows guests to reserve specific café tables by date, time slot, and party capacity, eliminating double-booking.")
    add_bullet(doc, "3. Secure Multi-Mode Payment Gateway: ", "Provide frictionless checkout supporting both Cash on Delivery (COD) and cryptographic online payments via Razorpay (UPI, Credit/Debit Cards, Net Banking) with SHA-256 webhook and signature verification.")
    add_bullet(doc, "4. AI-Powered Dining Concierge: ", "Integrate Google Gemini 1.5 Flash Generative AI within the mobile application (`ChatbotController.php`) to answer dietary queries, suggest meal pairings, and provide automated assistance, backed by local catalog fallback.")
    add_bullet(doc, "5. Dynamic Distance-Based Delivery: ", "Utilize Google Maps / Distance Matrix API coordinates to compute road kilometers between the café kitchen (`28.5562, 77.2010`) and customer delivery addresses, computing fair, automated delivery charges.")
    add_bullet(doc, "6. Centralized Kitchen & Dispatch Dashboard: ", "Provide restaurant staff with live order tracking (Pending -> Preparing -> Out for Delivery -> Delivered), courier dispatch assignment, and automated GST tax invoice PDF generation.")

    add_h2(doc, "1.2. Core Components")
    add_p(doc, "The Sip N' Bite ecosystem is systematically divided into five core architectural subsystems:")
    add_bullet(doc, "1. Web Backend & REST API Subsystem (Laravel 11): ", "Engineered with PHP 8.2+ following the Model-View-Controller (MVC) pattern. Handles business rules, Eloquent ORM database abstractions, Sanctum authentication tokens, and JSON endpoints for mobile clients (`routes/api.php`).")
    add_bullet(doc, "2. Customer Android Mobile Application (React Native / Expo SDK 52): ", "A reactive mobile application utilizing React Context API (`CartContext.js`, `AuthContext.js`), Axios HTTP clients, vector icons, dynamic dark/light design tokens, and AsyncStorage.")
    add_bullet(doc, "3. Administrative Web Dashboard: ", "Constructed using Laravel Blade templating, Tailwind CSS, Alpine.js, and Chart.js. Offers real-time sales revenue graphs, menu catalog CRUD controllers, table layout visualizers, and invoice printing.")
    add_bullet(doc, "4. Relational Database Management Subsystem (MySQL 8.0): ", "A normalized database schema containing 12 relational tables maintaining strict foreign key constraints and transactional consistency across users, menu items, orders, payments, tables, and reviews.")
    add_bullet(doc, "5. External Cloud Services & Gateways: ", "Encapsulates Razorpay Cloud Gateway, Google Gemini 1.5 Flash LLM, and OpenStreetMap / Distance Matrix geolocation services.")

    add_h2(doc, "1.3. Minimum and Maximum Software/Hardware Requirements")
    add_p(doc, "To ensure optimal performance, reliability, and security across both server nodes and client devices, the hardware and software configuration benchmarks are detailed below:")
    
    add_h3(doc, "A. Hardware Requirements Matrix")
    hw_tbl = doc.add_table(rows=5, cols=4)
    hw_tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
    hw_tbl.rows[0].cells[0].paragraphs[0].text = "Environment"
    hw_tbl.rows[0].cells[1].paragraphs[0].text = "Component"
    hw_tbl.rows[0].cells[2].paragraphs[0].text = "Minimum Specification"
    hw_tbl.rows[0].cells[3].paragraphs[0].text = "Recommended Specification"
    
    hw_data = [
        ("Client (Customer)", "Android Device", "Android 8.0 (API 26), 2GB RAM, Quad-Core 1.5GHz", "Android 12+, 4GB+ RAM, Octa-Core 2.0GHz+"),
        ("Client (Admin)", "Workstation / Laptop", "Intel Core i3 / AMD Ryzen 3, 4GB RAM, 1366x768", "Intel Core i5/i7, 8GB+ RAM, 1920x1080 FHD"),
        ("Server Node", "Web & DB Server", "Dual-Core CPU, 4GB RAM, 40GB SSD Storage", "Quad-Core Xeon / EPYC, 8GB+ RAM, NVMe SSD"),
        ("Network / Host", "Internet Bandwidth", "1 Mbps stable broadband / 4G cellular", "100 Mbps high-speed fiber / 5G connectivity")
    ]
    for idx, row in enumerate(hw_data, start=1):
        for c_idx, val in enumerate(row):
            hw_tbl.rows[idx].cells[c_idx].paragraphs[0].text = val
    format_table(hw_tbl, header_bg="1A365D", col_widths=[1.5, 1.5, 2.0, 2.0])

    doc.add_paragraph().paragraph_format.space_after = Pt(4)
    add_h3(doc, "B. Software Requirements Matrix")
    sw_tbl = doc.add_table(rows=6, cols=4)
    sw_tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
    sw_tbl.rows[0].cells[0].paragraphs[0].text = "Software Layer"
    sw_tbl.rows[0].cells[1].paragraphs[0].text = "Technology / Package"
    sw_tbl.rows[0].cells[2].paragraphs[0].text = "Minimum Version"
    sw_tbl.rows[0].cells[3].paragraphs[0].text = "Recommended Version"
    
    sw_data = [
        ("Host Operating System", "Server OS", "Windows 10 / Ubuntu 20.04 LTS", "Windows 11 Pro / Ubuntu 24.04 LTS"),
        ("Web Application Server", "Apache / Nginx / PHP", "PHP 8.2 with OpenSSL, PDO, cURL", "PHP 8.3+ with OPcache, Nginx 1.24+"),
        ("Backend Framework", "Laravel PHP Framework", "Laravel 10.x", "Laravel 11.x (Latest Stable release)"),
        ("Database Engine", "Relational Database", "MySQL 5.7 / MariaDB 10.3", "MySQL 8.0+ / MySQL 8.4 Enterprise"),
        ("Mobile Runtime", "React Native & Expo", "Node.js 18 LTS, Expo SDK 50", "Node.js 20+ LTS, Expo SDK 52, React Native 0.76")
    ]
    for idx, row in enumerate(sw_data, start=1):
        for c_idx, val in enumerate(row):
            sw_tbl.rows[idx].cells[c_idx].paragraphs[0].text = val
    format_table(sw_tbl, header_bg="1A365D", col_widths=[1.5, 1.7, 1.8, 2.0])

    add_h2(doc, "1.4. Advantages and Limitations of the Proposed System")
    add_p(doc, "Advantages:")
    add_bullet(doc, "• Omnichannel Synchronization: ", "Real-time synchronization between the Laravel web admin controllers and React Native Android clients ensures inventory and pricing changes are instantly reflected on mobile devices.")
    add_bullet(doc, "• Reduction of Manual Billing Errors: ", "Automated cart calculations compute item prices, 5% CGST/SGST tax, and distance-based delivery charges, eliminating human calculation errors.")
    add_bullet(doc, "• Secure Digital Payments: ", "Direct integration with Razorpay provides encrypted checkout, webhook audit logging, and payment verification.")
    add_bullet(doc, "• Natural Language AI Assistance: ", "Customers can inquire about nutritional values, allergies, and dish pairings via the integrated Google Gemini 1.5 Flash chatbot.")
    add_bullet(doc, "• Efficient Table Scheduling: ", "Prevents overbooking through time-slot validation and table capacity allocation.")
    
    add_p(doc, "Limitations:")
    add_bullet(doc, "• Internet Connectivity Dependency: ", "Real-time order placement, Razorpay verification, and Gemini AI queries require active network access.")
    add_bullet(doc, "• Geocoding Coordinate Variance: ", "In dense indoor areas, GPS drift may necessitate manual customer address adjustments.")
    add_bullet(doc, "• Single Merchant Scope: ", "Engineered for single-brand café operations rather than multi-restaurant aggregator models.")

    doc.add_page_break()

    # =========================================================================
    # CHAPTER 2: REQUIREMENT DETERMINATION & ANALYSIS
    # =========================================================================
    add_h1(doc, "2. Requirement Determination & Analysis")
    add_p(doc, "System analysis serves as the foundation for software engineering, systematically defining the functional capabilities, quality constraints, and user personas required for the Sip N' Bite platform.")

    add_h2(doc, "2.1. Requirement Determination")
    add_h3(doc, "Functional Requirements (FR):")
    add_bullet(doc, "FR1 [User Authentication & Security]: ", "The system shall permit customers to register, log in, and maintain session tokens. Passwords must be hashed using Bcrypt. Admin access shall be guarded by role-based middleware.")
    add_bullet(doc, "FR2 [Dynamic Food Menu Catalog]: ", "The system shall categorize items (Pizzas, Burgers, Sandwiches, Fries, Beverages, Desserts). Each dish must display price, description, vegetarian flag, and stock availability.")
    add_bullet(doc, "FR3 [Cart Management & Checkout]: ", "Users shall be able to add, modify, and delete cart items with real-time tax and delivery charge updates.")
    add_bullet(doc, "FR4 [Payment Processing]: ", "The system shall support online payments (Razorpay API) and Cash on Delivery (COD). Every transaction must record a cryptographic signature and receipt.")
    add_bullet(doc, "FR5 [Table Reservation Engine]: ", "Guests shall reserve dining tables by selecting date, time slot, and guest count. The system shall verify availability against existing reservations.")
    add_bullet(doc, "FR6 [Real-Time Order Tracking]: ", "Customers shall monitor order progression across 4 stages: Pending, Preparing, Out for Delivery, and Delivered.")
    add_bullet(doc, "FR7 [Administrative Management]: ", "Administrators shall add, update, and remove menu items, categories, and delivery riders, and generate official GST tax invoices.")
    add_bullet(doc, "FR8 [AI Chatbot Concierge]: ", "The system shall ground user prompts on menu data and generate responses via Google Gemini 1.5 Flash.")

    add_h3(doc, "Non-Functional Requirements (NFR):")
    add_bullet(doc, "NFR1 [Performance]: ", "REST API endpoints shall return responses within 200 milliseconds under standard server loads.")
    add_bullet(doc, "NFR2 [Security]: ", "All REST endpoints and payment captures must execute over TLS 1.3/HTTPS. Bearer tokens must be validated via Laravel Sanctum.")
    add_bullet(doc, "NFR3 [Reliability & Data Integrity]: ", "Database transactions (ACID) must wrap order creation, inventory decrements, and payment commits to prevent partial records.")
    add_bullet(doc, "NFR4 [Usability]: ", "The mobile application must follow responsive Android material guidelines with clear touch targets.")

    add_h2(doc, "2.2. Targeted Users")
    add_p(doc, "The Sip N' Bite system is engineered to serve four distinct user personas:")
    
    users_tbl = doc.add_table(rows=5, cols=4)
    users_tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
    users_tbl.rows[0].cells[0].paragraphs[0].text = "User Persona"
    users_tbl.rows[0].cells[1].paragraphs[0].text = "Access Interface"
    users_tbl.rows[0].cells[2].paragraphs[0].text = "Primary Objectives"
    users_tbl.rows[0].cells[3].paragraphs[0].text = "Core Permissions"
    
    users_data = [
        ("Customer / Diner", "React Native Android App & Web Portal", "Browse menu, order food, pay online, reserve tables, track delivery", "Read menu, manage personal cart, place orders, book tables, review dishes"),
        ("Restaurant Admin", "Laravel Web Admin Dashboard", "Oversee live kitchen queue, manage menu catalog, print invoices, analyze revenue", "Full CRUD on items, categories, orders, tables, delivery staff, and tax settings"),
        ("Delivery Partner", "Mobile Web / Notification Channel", "Receive dispatch assignments, navigate to customer GPS coordinates, complete delivery", "View assigned orders, update delivery status to 'Delivered'"),
        ("System Administrator", "Host Terminal / Database CLI", "Maintain server uptime, rotate API secrets, manage backups, monitor logs", "Full root access to server, database migrations, and application configs")
    ]
    for idx, row in enumerate(users_data, start=1):
        for c_idx, val in enumerate(row):
            users_tbl.rows[idx].cells[c_idx].paragraphs[0].text = val
    format_table(users_tbl, header_bg="1A365D", col_widths=[1.5, 1.7, 2.0, 1.8])

    doc.add_page_break()

    # =========================================================================
    # CHAPTER 3: SYSTEM DESIGN
    # =========================================================================
    add_h1(doc, "3. System Design")
    add_p(doc, "System Design models the structural, behavioral, and physical deployment aspects of the Sip N' Bite application. This chapter incorporates formal UML diagrams matching the exact visual style specified in the reference guidelines.")

    # 3.1 USE CASE DIAGRAM
    add_h2(doc, "3.1. Use Case Diagram")
    add_p(doc, "The Use Case Diagram defines the functional boundary of the Sip N' Bite platform, illustrating the interactions between external actors (Admin and Customer) and the system use cases.")
    
    add_h3(doc, "■ Admin Use Case Diagram")
    add_p(doc, "The Admin subsystem provides full administrative control over restaurant operations. The diagram below illustrates the exact structural layout showing the Admin actor interacting with the six core use cases within the ADMIN system boundary:")
    
    # Insert Admin Use Case Diagram (Exact Reference Image 22)
    admin_uc_img = os.path.join(img_base_ppt, "image22.png")
    add_fig_image(doc, admin_uc_img, "Figure 3.1: Admin Use Case Diagram (System Boundary: ADMIN)", width_in=5.2)

    add_h3(doc, "■ Customer Use Case Diagram")
    add_p(doc, "The Customer subsystem enables end-users on the React Native Android mobile app and Web customer portal to engage with the ordering lifecycle. The diagram below presents the exact Customer use case model:")
    
    # Insert Customer Use Case Diagram (Exact Reference Image 23)
    cust_uc_img = os.path.join(img_base_ppt, "image23.png")
    add_fig_image(doc, cust_uc_img, "Figure 3.2: Customer Use Case Diagram (System Boundary: CUSTOMER)", width_in=5.2)

    add_p(doc, "Detailed Use Case Specification Table:")
    uc_spec_tbl = doc.add_table(rows=7, cols=4)
    uc_spec_tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
    uc_spec_tbl.rows[0].cells[0].paragraphs[0].text = "Actor"
    uc_spec_tbl.rows[0].cells[1].paragraphs[0].text = "Use Case Name"
    uc_spec_tbl.rows[0].cells[2].paragraphs[0].text = "Pre-conditions & Inputs"
    uc_spec_tbl.rows[0].cells[3].paragraphs[0].text = "Post-conditions & System Action"
    
    uc_specs = [
        ("Admin", "Manage Menu & Categories", "Admin authenticated in `/admin/login`", "New dishes, pricing, or categories saved to `menu_items` table and broadcast to mobile app"),
        ("Admin", "Manage Orders & Invoicing", "Customer places an order", "Admin updates status to 'Preparing', system generates official GST tax invoice PDF"),
        ("Customer", "Registration / Login", "Valid email, phone, and password", "Account verified in `users` table; Bearer API token issued to mobile app"),
        ("Customer", "View Menu & Add to Cart", "Menu catalog fetched via REST API", "Items stored in reactive `CartContext` with real-time tax calculation"),
        ("Customer", "Make Payment", "Cart finalized with delivery address", "Razorpay modal launched; on success, transaction recorded in `payments` table"),
        ("Customer", "View Invoice & Track Order", "Order confirmed by server", "Customer receives order confirmation and tracks real-time fulfillment status")
    ]
    for idx, row in enumerate(uc_specs, start=1):
        for c_idx, val in enumerate(row):
            uc_spec_tbl.rows[idx].cells[c_idx].paragraphs[0].text = val
    format_table(uc_spec_tbl, header_bg="1A365D", col_widths=[1.2, 1.8, 1.8, 2.2])

    doc.add_page_break()

    # 3.2 CLASS DIAGRAM
    add_h2(doc, "3.2. Class Diagram")
    add_p(doc, "The Class Diagram models the static object-oriented structure of the Sip N' Bite platform. It illustrates the primary domain classes, their data attributes, member methods, and relational associations (inheritance, composition, aggregation).")
    
    # Insert Class Diagram (Exact Reference Image 24)
    class_diag_img = os.path.join(img_base_ppt, "image24.png")
    add_fig_image(doc, class_diag_img, "Figure 3.3: UML Class Diagram — Sip N Bite Domain Model", width_in=5.8)

    add_p(doc, "Class Dictionary & Structural Relationships:")
    cls_dict_tbl = doc.add_table(rows=8, cols=4)
    cls_dict_tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
    cls_dict_tbl.rows[0].cells[0].paragraphs[0].text = "Class Name"
    cls_dict_tbl.rows[0].cells[1].paragraphs[0].text = "Primary Attributes"
    cls_dict_tbl.rows[0].cells[2].paragraphs[0].text = "Core Operations / Methods"
    cls_dict_tbl.rows[0].cells[3].paragraphs[0].text = "Relationships & Multiplicity"
    
    cls_data = [
        ("User", "id, name, email, password", "login(), logout()", "Generalization Superclass of Customer and Admin"),
        ("Customer", "phone, address", "register(), placeOrder(), addToCart()", "Inherits User; 1:1 with Cart; 1:N with Order"),
        ("Admin", "role, permissions", "manageMenu(), manageCategories(), manageOrders(), generateInvoice()", "Inherits User; Manages Menu, Orders, Invoices"),
        ("Cart", "cartId, items, subtotal", "addItem(), removeItem(), calculateTotal()", "Associated with Customer (1:1) and MenuItem (1:N)"),
        ("Order", "orderId, orderDate, totalAmount", "placeOrder(), trackOrder()", "Composed of OrderItem (1:N); 1:1 with Payment"),
        ("MenuItem", "id, name, price, description", "updateItem(), toggleAvailability()", "Belongs to Category (N:1); referenced by Cart & OrderItem"),
        ("Payment", "paymentId, method, amount", "processPayment(), verifySignature()", "Associated with Order (1:1); integrates Razorpay API")
    ]
    for idx, row in enumerate(cls_data, start=1):
        for c_idx, val in enumerate(row):
            cls_dict_tbl.rows[idx].cells[c_idx].paragraphs[0].text = val
    format_table(cls_dict_tbl, header_bg="1A365D", col_widths=[1.2, 1.8, 2.0, 2.0])

    doc.add_page_break()

    # 3.3 INTERACTION DIAGRAM
    add_h2(doc, "3.3. Interaction Diagram")
    add_p(doc, "Interaction Diagrams capture the dynamic behavioral message exchanges between objects across time. In this section, both Sequence Diagrams and Collaboration Diagrams are provided to model the ordering and administrative workflows.")
    
    add_h3(doc, "3.3.1 Sequence Diagram: Customer Food Ordering & Online Payment Flow")
    add_p(doc, "Depicts the end-to-end food ordering sequence initiated from the React Native Android application, interacting with Laravel's `OrderApiController`, processing payment with Razorpay, and committing records to MySQL:")
    
    cust_seq_img = os.path.join(img_base_gen, "interaction_customer_order_seq.png")
    add_fig_image(doc, cust_seq_img, "Figure 3.4: Sequence Diagram — Customer Ordering & Razorpay Payment Flow", width_in=5.8)

    add_h3(doc, "3.3.2 Sequence Diagram: Admin Order Management & Invoice Generation Flow")
    add_p(doc, "Illustrates the administrative flow where the restaurant manager reviews incoming orders on the Web portal, updates kitchen preparation status, and triggers PDF tax invoice generation:")
    
    admin_seq_img = os.path.join(img_base_gen, "interaction_admin_invoice_seq.png")
    add_fig_image(doc, admin_seq_img, "Figure 3.5: Sequence Diagram — Admin Order Management & Invoice Generation", width_in=5.8)

    add_h3(doc, "3.3.3 UML Collaboration / Communication Diagram")
    add_p(doc, "A Collaboration Diagram emphasizes the structural relationships and numbered message sequences between interacting subsystem components:")
    
    collab_img = os.path.join(img_base_gen, "interaction_collaboration_diagram.png")
    add_fig_image(doc, collab_img, "Figure 3.6: UML Collaboration Diagram — Subsystem Message Flow", width_in=5.5)

    doc.add_page_break()

    # 3.4 ACTIVITY DIAGRAM
    add_h2(doc, "3.4. Activity Diagram")
    add_p(doc, "Activity Diagrams model the operational workflows, control branching, and parallel synchronizations within the application.")
    
    add_h3(doc, "[3.3.1 Admin activity diagram]")
    add_p(doc, "The Admin Activity Diagram models the workflow of the restaurant administrator. It features an initial authentication loop, a concurrency FORK bar splitting into parallel management branches (Manage Menu, Manage Category, View History), a JOIN bar synchronizing tasks, and invoice generation:")
    
    # Insert Admin Activity Diagram (Exact Reference Image 25)
    admin_act_img = os.path.join(img_base_ppt, "image25.png")
    add_fig_image(doc, admin_act_img, "Figure 3.7: [3.3.1 Admin activity diagram] (Exact Reference Reproduction)", width_in=4.8)

    add_h3(doc, "[3.3.3 Customer Activity Diagram]")
    add_p(doc, "The Customer Activity Diagram captures the end-user journey on the mobile app: authentication with validation loop, menu exploration, dish selection, cart accumulation with a 'Continue shopping?' decision diamond, and payment completion:")
    
    # Insert Customer Activity Diagram (Exact Reference Image 26)
    cust_act_img = os.path.join(img_base_ppt, "image26.png")
    add_fig_image(doc, cust_act_img, "Figure 3.8: [3.3.3 Customer Activity Diagram] (Exact Reference Reproduction)", width_in=4.8)

    doc.add_page_break()

    # 3.5 DEPLOYMENT DIAGRAM
    add_h2(doc, "3.5. Deployment Diagram")
    add_p(doc, "The Deployment Diagram depicts the physical execution environments, hardware nodes, deployed software artifacts, and network protocols governing the Sip N' Bite ecosystem.")
    
    deploy_img = os.path.join(img_base_gen, "deployment_diagram.png")
    add_fig_image(doc, deploy_img, "Figure 3.9: UML 4-Tier Deployment Architecture — Client, App Server, Database, Cloud", width_in=5.8)

    add_p(doc, "Deployment Node & Protocol Matrix:")
    dep_mat_tbl = doc.add_table(rows=5, cols=4)
    dep_mat_tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
    dep_mat_tbl.rows[0].cells[0].paragraphs[0].text = "Node / Device"
    dep_mat_tbl.rows[0].cells[1].paragraphs[0].text = "Execution Environment"
    dep_mat_tbl.rows[0].cells[2].paragraphs[0].text = "Deployed Artifacts"
    dep_mat_tbl.rows[0].cells[3].paragraphs[0].text = "Communication Protocols & Ports"
    
    dep_mat_data = [
        ("Android Smartphone", "Android OS (API 26+) / Expo Runtime", "SipNBite.apk, App.jsx bundle, Axios HTTP Client", "HTTPS / REST API to App Server (Port 8000/443); TLS 1.3 to Razorpay"),
        ("Admin Workstation", "Web Browser (Chrome / Edge)", "Admin Blade UI, Tailwind CSS, Chart.js bundles", "HTTP / HTTPS to App Server (Port 8000/80) via Web Sessions"),
        ("Application Server", "Apache / Nginx, PHP 8.2+, Laravel 11", "Laravel Controllers, routes/api.php, Eloquent Models", "Port 8000 (HTTP), Port 3306 (TCP to MySQL), Port 443 (Outbound HTTPS)"),
        ("Database Server", "MySQL 8.0+ Database Engine", "sipnbite schema, 12 normalized tables & indexes", "TCP/IP on Port 3306 with encrypted database authentication")
    ]
    for idx, row in enumerate(dep_mat_data, start=1):
        for c_idx, val in enumerate(row):
            dep_mat_tbl.rows[idx].cells[c_idx].paragraphs[0].text = val
    format_table(dep_mat_tbl, header_bg="1A365D", col_widths=[1.5, 1.8, 1.8, 1.9])

    doc.add_page_break()

    # 3.6 DATA DICTIONARY
    add_h2(doc, "3.6. Data Dictionary")
    add_p(doc, "The Data Dictionary provides a detailed specification of all database entities, field names, data types, constraints, and operational descriptions extracted directly from the production MySQL database (`sipnbite.sql`).")

    tables_dict = [
        ("Table 1: users", [
            ("id", "BIGINT UNSIGNED", "PK, Auto Increment", "Unique user identifier"),
            ("name", "VARCHAR(191)", "NOT NULL", "Full name of the user"),
            ("email", "VARCHAR(191)", "NOT NULL, UNIQUE", "User email address for authentication"),
            ("phone", "VARCHAR(191)", "NULLABLE", "Contact phone number"),
            ("password", "VARCHAR(191)", "NOT NULL", "Bcrypt hashed security password"),
            ("role", "ENUM('admin','customer','delivery')", "DEFAULT 'customer'", "Access control role"),
            ("status", "VARCHAR(50)", "DEFAULT 'active'", "Account status flag"),
            ("created_at / updated_at", "TIMESTAMP", "NULLABLE", "Audit timestamp records")
        ]),
        ("Table 2: categories", [
            ("id", "BIGINT UNSIGNED", "PK, Auto Increment", "Unique category identifier"),
            ("name", "VARCHAR(191)", "NOT NULL", "Category display title (e.g. Pizza, Burgers)"),
            ("slug", "VARCHAR(191)", "NOT NULL, UNIQUE", "URL-friendly slug"),
            ("image", "VARCHAR(191)", "NULLABLE", "Relative image asset path"),
            ("description", "TEXT", "NULLABLE", "Summary description of category"),
            ("status", "TINYINT(1)", "DEFAULT 1", "Active availability toggle")
        ]),
        ("Table 3: menu_items", [
            ("id", "BIGINT UNSIGNED", "PK, Auto Increment", "Unique food item identifier"),
            ("category_id", "BIGINT UNSIGNED", "FK -> categories(id)", "Associated food category"),
            ("name", "VARCHAR(191)", "NOT NULL", "Name of the dish"),
            ("description", "TEXT", "NULLABLE", "Ingredients and culinary summary"),
            ("price", "DECIMAL(10,2)", "NOT NULL", "Unit price in INR (₹)"),
            ("image", "VARCHAR(191)", "NULLABLE", "Image filename stored in public assets"),
            ("is_available", "TINYINT(1)", "DEFAULT 1", "Kitchen stock availability status"),
            ("is_veg", "TINYINT(1)", "DEFAULT 1", "Vegetarian (1) or Non-Vegetarian (0) badge"),
            ("rating", "DECIMAL(3,2)", "DEFAULT 4.5", "Aggregated customer rating score")
        ]),
        ("Table 4: orders", [
            ("id", "BIGINT UNSIGNED", "PK, Auto Increment", "Unique order internal ID"),
            ("order_number", "VARCHAR(191)", "NOT NULL, UNIQUE", "Formatted business order ID (e.g. ORD-1042)"),
            ("user_id", "BIGINT UNSIGNED", "FK -> users(id)", "Customer placing the order"),
            ("delivery_person_id", "BIGINT UNSIGNED", "FK -> delivery_persons(id), NULL", "Assigned delivery rider"),
            ("address_id", "BIGINT UNSIGNED", "FK -> addresses(id), NULL", "Selected delivery location"),
            ("subtotal", "DECIMAL(10,2)", "NOT NULL", "Gross item cost before taxes"),
            ("tax", "DECIMAL(10,2)", "NOT NULL", "5% CGST/SGST tax total"),
            ("delivery_fee", "DECIMAL(10,2)", "DEFAULT 40.00", "Distance-based delivery surcharge"),
            ("total_amount", "DECIMAL(10,2)", "NOT NULL", "Net payable invoice sum"),
            ("payment_method", "VARCHAR(50)", "NOT NULL", "'cod' or 'razorpay'"),
            ("payment_status", "VARCHAR(50)", "DEFAULT 'pending'", "'pending', 'paid', or 'failed'"),
            ("order_status", "VARCHAR(50)", "DEFAULT 'pending'", "'pending', 'preparing', 'out_for_delivery', 'delivered'")
        ]),
        ("Table 5: order_items", [
            ("id", "BIGINT UNSIGNED", "PK, Auto Increment", "Unique line item identifier"),
            ("order_id", "BIGINT UNSIGNED", "FK -> orders(id), ON DELETE CASCADE", "Parent order reference"),
            ("menu_item_id", "BIGINT UNSIGNED", "FK -> menu_items(id)", "Referenced dish"),
            ("item_name", "VARCHAR(191)", "NOT NULL", "Snapshot of item name at time of order"),
            ("price", "DECIMAL(10,2)", "NOT NULL", "Snapshot unit price"),
            ("quantity", "INT", "NOT NULL", "Number of portions ordered"),
            ("subtotal", "DECIMAL(10,2)", "NOT NULL", "Line subtotal (price * quantity)")
        ]),
        ("Table 6: payments", [
            ("id", "BIGINT UNSIGNED", "PK, Auto Increment", "Unique payment record ID"),
            ("order_id", "BIGINT UNSIGNED", "FK -> orders(id)", "Associated purchase order"),
            ("user_id", "BIGINT UNSIGNED", "FK -> users(id)", "Paying customer ID"),
            ("razorpay_payment_id", "VARCHAR(191)", "NULLABLE", "Razorpay transaction reference"),
            ("razorpay_order_id", "VARCHAR(191)", "NULLABLE", "Razorpay order reference"),
            ("razorpay_signature", "VARCHAR(191)", "NULLABLE", "HMAC-SHA256 verification hash"),
            ("amount", "DECIMAL(10,2)", "NOT NULL", "Total amount transacted"),
            ("payment_method", "VARCHAR(50)", "NOT NULL", "Payment instrument (UPI/Card/COD)"),
            ("status", "VARCHAR(50)", "DEFAULT 'pending'", "'success', 'failed', 'refunded'")
        ]),
        ("Table 7: tables", [
            ("id", "BIGINT UNSIGNED", "PK, Auto Increment", "Dining table identifier"),
            ("table_number", "VARCHAR(50)", "NOT NULL, UNIQUE", "Physical table label (e.g. Table #1)"),
            ("capacity", "INT", "NOT NULL", "Maximum seating capacity (e.g. 2, 4, 6, 8)"),
            ("location_type", "VARCHAR(100)", "DEFAULT 'Indoor'", "Indoor, Outdoor Patio, or Rooftop"),
            ("status", "VARCHAR(50)", "DEFAULT 'available'", "'available', 'reserved', 'maintenance'")
        ]),
        ("Table 8: table_bookings", [
            ("id", "BIGINT UNSIGNED", "PK, Auto Increment", "Booking reservation identifier"),
            ("user_id", "BIGINT UNSIGNED", "FK -> users(id)", "Customer booking the table"),
            ("table_id", "BIGINT UNSIGNED", "FK -> tables(id)", "Reserved table entity"),
            ("booking_date", "DATE", "NOT NULL", "Reservation calendar date"),
            ("booking_time", "VARCHAR(50)", "NOT NULL", "Scheduled time slot (e.g. 19:30)"),
            ("guests_count", "INT", "NOT NULL", "Number of attending guests"),
            ("special_request", "TEXT", "NULLABLE", "Dietary or seating preferences"),
            ("status", "VARCHAR(50)", "DEFAULT 'confirmed'", "'pending', 'confirmed', 'cancelled'")
        ]),
        ("Table 9: addresses", [
            ("id", "BIGINT UNSIGNED", "PK, Auto Increment", "Address record identifier"),
            ("user_id", "BIGINT UNSIGNED", "FK -> users(id)", "Customer owning the address"),
            ("title", "VARCHAR(191)", "DEFAULT 'Home'", "Address label (Home, Work, Other)"),
            ("address_line", "TEXT", "NOT NULL", "Street address and apartment details"),
            ("city", "VARCHAR(191)", "NOT NULL", "City name"),
            ("pincode", "VARCHAR(20)", "NULLABLE", "Postal pincode"),
            ("latitude", "DECIMAL(10,8)", "NULLABLE", "GPS latitude coordinate"),
            ("longitude", "DECIMAL(11,8)", "NULLABLE", "GPS longitude coordinate"),
            ("is_default", "TINYINT(1)", "DEFAULT 0", "Default checkout address flag")
        ]),
        ("Table 10: delivery_persons", [
            ("id", "BIGINT UNSIGNED", "PK, Auto Increment", "Courier rider identifier"),
            ("name", "VARCHAR(191)", "NOT NULL", "Full name of delivery rider"),
            ("phone", "VARCHAR(191)", "NOT NULL", "Contact telephone number"),
            ("email", "VARCHAR(191)", "NULLABLE", "Courier email account"),
            ("vehicle_number", "VARCHAR(50)", "NOT NULL", "Registered vehicle registration number"),
            ("status", "VARCHAR(50)", "DEFAULT 'available'", "'available', 'on_delivery', 'inactive'")
        ]),
        ("Table 11: feedback", [
            ("id", "BIGINT UNSIGNED", "PK, Auto Increment", "Feedback review identifier"),
            ("user_id", "BIGINT UNSIGNED", "FK -> users(id)", "Reviewing customer"),
            ("order_id", "BIGINT UNSIGNED", "FK -> orders(id), NULL", "Optional reviewed order reference"),
            ("rating", "INT", "NOT NULL", "Star rating (1 to 5)"),
            ("comment", "TEXT", "NULLABLE", "Customer remarks and review comment"),
            ("created_at", "TIMESTAMP", "DEFAULT CURRENT_TIMESTAMP", "Submission timestamp")
        ]),
        ("Table 12: settings", [
            ("id", "BIGINT UNSIGNED", "PK, Auto Increment", "Configuration record ID"),
            ("key", "VARCHAR(191)", "NOT NULL, UNIQUE", "System setting key (e.g. gst_rate, delivery_fee)"),
            ("value", "TEXT", "NOT NULL", "Configuration value (e.g. '5.00', '40.00')"),
            ("description", "VARCHAR(255)", "NULLABLE", "Purpose and scope of configuration")
        ])
    ]

    for tbl_title, cols in tables_dict:
        add_h3(doc, tbl_title)
        tbl_obj = doc.add_table(rows=len(cols)+1, cols=4)
        tbl_obj.alignment = WD_TABLE_ALIGNMENT.CENTER
        tbl_obj.rows[0].cells[0].paragraphs[0].text = "Field / Column Name"
        tbl_obj.rows[0].cells[1].paragraphs[0].text = "Data Type"
        tbl_obj.rows[0].cells[2].paragraphs[0].text = "Constraints / Keys"
        tbl_obj.rows[0].cells[3].paragraphs[0].text = "Description & Purpose"
        
        for r_i, c_data in enumerate(cols, start=1):
            for c_i, c_val in enumerate(c_data):
                tbl_obj.rows[r_i].cells[c_i].paragraphs[0].text = c_val
        format_table(tbl_obj, header_bg="2B6CB0", col_widths=[1.5, 1.4, 1.6, 2.5])
        doc.add_paragraph().paragraph_format.space_after = Pt(4)

    doc.add_page_break()

    # =========================================================================
    # CHAPTER 4: AGILE DOCUMENTATION
    # =========================================================================
    add_h1(doc, "4. Agile Documentation")
    add_p(doc, "The development of Sip N' Bite followed the Agile Scrum framework, executing iteratively across four 2-week sprints. This chapter details the project charter, roadmap, user stories with minimum 3 tasks each, release schedules, test plans, and earned-value/burn charts.")

    add_h2(doc, "4.1. Agile Project Charter")
    add_bullet(doc, "• Project Vision: ", "To engineer a unified food ordering and table booking platform that reduces restaurant order turnaround time by 50% and delivers modern mobile convenience.")
    add_bullet(doc, "• Business Need: ", "Eliminate paper tickets, reduce manual calculation errors, and provide restaurant managers with live inventory and sales analytics.")
    add_bullet(doc, "• In-Scope: ", "Customer Android mobile app, Web admin dashboard, Razorpay online payments, Google Gemini AI assistant, dynamic GPS distance calculation, table booking engine, GST tax invoice generator.")
    add_bullet(doc, "• Out-of-Scope: ", "Multi-tenant restaurant franchise networks and third-party logistics courier APIs.")
    add_bullet(doc, "• Agile Roles: ", "Product Owner: Faculty Evaluation Committee | Scrum Master: Patel Yash | Full-Stack Team: Chauhan Deep, Viras Prathamesh.")

    add_h2(doc, "4.2. Agile Roadmap / Schedule / Agile Project Plan")
    add_p(doc, "The project was executed across four structured sprints spanning 8 weeks:")
    
    plan_tbl = doc.add_table(rows=5, cols=4)
    plan_tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
    plan_tbl.rows[0].cells[0].paragraphs[0].text = "Sprint Iteration"
    plan_tbl.rows[0].cells[1].paragraphs[0].text = "Duration / Timeline"
    plan_tbl.rows[0].cells[2].paragraphs[0].text = "Primary Focus & Goals"
    plan_tbl.rows[0].cells[3].paragraphs[0].text = "Deliverables / Milestones"
    
    plan_data = [
        ("Sprint 1", "Weeks 1 – 2", "Architecture, Database Schema & Authentication", "Normalized MySQL schema (12 tables), Laravel Sanctum auth APIs, React Native project scaffold"),
        ("Sprint 2", "Weeks 3 – 4", "Menu Catalog, Cart Engine & Razorpay Payments", "Interactive menu filtering, CartContext state management, Razorpay payment gateway integration"),
        ("Sprint 3", "Weeks 5 – 6", "Admin Dashboard, Invoicing & Gemini AI Chatbot", "Admin order dispatch dashboard, PDF tax invoice generator, Google Gemini 1.5 Flash AI chatbot"),
        ("Sprint 4", "Weeks 7 – 8", "Table Reservations, Distance Matrix, Testing & Docs", "Table booking engine, Distance Matrix GPS calculator, unit/integration testing, final docx")
    ]
    for idx, row in enumerate(plan_data, start=1):
        for c_idx, val in enumerate(row):
            plan_tbl.rows[idx].cells[c_idx].paragraphs[0].text = val
    format_table(plan_tbl, header_bg="1A365D", col_widths=[1.2, 1.4, 2.2, 2.2])

    add_h2(doc, "4.3. Agile User Stories (Minimum 3 Tasks per Story)")
    add_p(doc, "In accordance with Agile project requirements, each core user story is elaborated with acceptance criteria, story points, and a minimum of three distinct technical engineering tasks:")

    stories_data = [
        ("US-01 [Food Discovery & Cart Accumulation]",
         "As a customer, I want to browse categorized dishes with veg/non-veg badges and accumulate items in a shopping cart, so that I can prepare my order effortlessly.",
         "Given the mobile menu screen, when I select a category tab (e.g. Burgers), only matching items appear. Cart subtotal updates dynamically.",
         "Story Points: 5 | Priority: High | Sprint: 2",
         [("Task 1.1", "Develop Laravel REST API endpoint `GET /api/menu` with category filtering and stock status."),
          ("Task 1.2", "Build React Native `MenuScreen.jsx` with category chips, search bar, and dish cards."),
          ("Task 1.3", "Implement `CartContext.js` using AsyncStorage for persistent client-side cart state management.")]),
          
        ("US-02 [Online Payment via Razorpay Gateway]",
         "As a diner, I want to securely pay for my food order using UPI, debit/credit cards, or net banking via Razorpay, so that I do not need to carry physical cash.",
         "Given a populated cart, when I tap 'Proceed to Payment', the Razorpay checkout modal opens. Upon payment success, a transaction ID is verified.",
         "Story Points: 8 | Priority: Critical | Sprint: 2",
         [("Task 2.1", "Implement backend `POST /api/checkout/initiate-payment` utilizing the official Razorpay PHP SDK."),
          ("Task 2.2", "Integrate the `react-native-razorpay` native bridge modal into `CheckoutScreen.jsx`."),
          ("Task 2.3", "Develop HMAC-SHA256 signature verification in `OrderApiController.php` before committing the order.")]),
          
        ("US-03 [Admin Order Management & GST Tax Invoice]",
         "As a restaurant manager, I want to review incoming orders in real time and generate printable GST tax invoices, so that the kitchen operates smoothly and billing is compliant.",
         "Given new orders, when the admin changes status to 'Preparing', customer receives a push update. Admin can download printable PDF invoice with 5% tax.",
         "Story Points: 5 | Priority: High | Sprint: 3",
         [("Task 3.1", "Develop Admin Web Order Management UI with instant AJAX status transitions."),
          ("Task 3.2", "Create `InvoiceController.php` to calculate 5% CGST/SGST tax breakdown and generate printable invoices."),
          ("Task 3.3", "Implement real-time push alert dispatcher to notify customer mobile devices of status changes.")]),
          
        ("US-04 [AI-Powered Dining Concierge Chatbot]",
         "As a customer, I want to ask dietary and pairing questions to an AI assistant, so that I receive personalized dish recommendations based on the actual café menu.",
         "Given the chatbot screen, when I ask for recommendations, Gemini AI responds using live café dishes. If offline, the local database search responds.",
         "Story Points: 8 | Priority: Medium | Sprint: 3",
         [("Task 4.1", "Construct `ChatbotController.php` interfacing with Google Gemini 1.5 Flash API with strict café guardrails."),
          ("Task 4.2", "Build interactive mobile chat interface (`ChatBotScreen.jsx`) with bubble styling and action shortcuts."),
          ("Task 4.3", "Engineer local MySQL keyword fallback engine to guarantee zero chatbot downtime when external APIs fail.")])
    ]

    for title, story, criteria, meta, tasks in stories_data:
        add_h3(doc, title)
        add_bullet(doc, "• User Story Statement: ", story)
        add_bullet(doc, "• Acceptance Criteria: ", criteria)
        add_bullet(doc, "• Estimation & Priority: ", meta)
        
        st_tbl = doc.add_table(rows=len(tasks)+1, cols=2)
        st_tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
        st_tbl.rows[0].cells[0].paragraphs[0].text = "Task ID"
        st_tbl.rows[0].cells[1].paragraphs[0].text = "Technical Implementation Details"
        for t_i, (t_id, t_desc) in enumerate(tasks, start=1):
            st_tbl.rows[t_i].cells[0].paragraphs[0].text = t_id
            st_tbl.rows[t_i].cells[1].paragraphs[0].text = t_desc
        format_table(st_tbl, header_bg="2B6CB0", col_widths=[1.5, 5.5])
        doc.add_paragraph().paragraph_format.space_after = Pt(2)

    add_h2(doc, "4.4. Agile Release Plan")
    add_bullet(doc, "• MVP Release v1.0 (End of Sprint 2): ", "Core functional baseline: User registration, menu discovery, cart management, cash on delivery ordering, and basic admin order acceptance.")
    add_bullet(doc, "• Stable Release v1.1 (End of Sprint 3): ", "Payment gateway integration (Razorpay online checkout), Admin GST tax invoice PDF generation, table reservation engine, and initial AI chatbot.")
    add_bullet(doc, "• Production Release v2.0 (End of Sprint 4): ", "Full production release: Google Gemini 1.5 Flash conversational grounding, GPS distance delivery matrix, real-time push tracking, and performance optimizations.")

    add_h2(doc, "4.5. Agile Sprint Backlog")
    add_p(doc, "A summary of prioritized backlog items and story point burn allocations across the four project sprints:")
    
    sb_tbl = doc.add_table(rows=5, cols=4)
    sb_tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
    sb_tbl.rows[0].cells[0].paragraphs[0].text = "Sprint #"
    sb_tbl.rows[0].cells[1].paragraphs[0].text = "Committed Backlog Items"
    sb_tbl.rows[0].cells[2].paragraphs[0].text = "Total Story Points"
    sb_tbl.rows[0].cells[3].paragraphs[0].text = "Completion Status"
    
    sb_data = [
        ("Sprint 1", "Database Schema, Sanctum Auth, Admin Scaffolding, Mobile Navigation Shell", "21 Points", "100% Completed (Accepted)"),
        ("Sprint 2", "Catalog APIs, Dynamic Cart, Order Placement Pipeline, Razorpay Integration", "24 Points", "100% Completed (Accepted)"),
        ("Sprint 3", "Admin Order Dispatch, Printable PDF Invoicing, Gemini 1.5 Flash Chatbot", "22 Points", "100% Completed (Accepted)"),
        ("Sprint 4", "Table Booking Engine, Distance Matrix GPS, E2E Testing, Documentation", "18 Points", "100% Completed (Accepted)")
    ]
    for idx, row in enumerate(sb_data, start=1):
        for c_idx, val in enumerate(row):
            sb_tbl.rows[idx].cells[c_idx].paragraphs[0].text = val
    format_table(sb_tbl, header_bg="1A365D", col_widths=[1.2, 3.2, 1.4, 1.4])

    add_h2(doc, "4.6. Agile Test Plan")
    add_p(doc, "Comprehensive quality assurance matrix verifying core application scenarios:")
    
    test_tbl = doc.add_table(rows=6, cols=5)
    test_tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
    test_tbl.rows[0].cells[0].paragraphs[0].text = "Test ID"
    test_tbl.rows[0].cells[1].paragraphs[0].text = "Test Scenario"
    test_tbl.rows[0].cells[2].paragraphs[0].text = "Test Steps & Input Data"
    test_tbl.rows[0].cells[3].paragraphs[0].text = "Expected Result"
    test_tbl.rows[0].cells[4].paragraphs[0].text = "Verdict"
    
    test_data = [
        ("TC-01", "Customer Login", "Submit valid email and password on login screen", "200 OK returned; Bearer token stored in AsyncStorage", "PASSED"),
        ("TC-02", "Cart Calculation", "Add 2 Burgers (₹150 ea) + 1 Cold Coffee (₹120)", "Subtotal = ₹420; GST 5% = ₹21; Delivery = ₹40; Total = ₹481", "PASSED"),
        ("TC-03", "Razorpay Checkout", "Select Online Payment; submit test card details", "Payment captured; SHA-256 signature verified; Order committed", "PASSED"),
        ("TC-04", "Table Booking", "Book Table #4 for 4 guests on 2026-10-25 at 19:00", "Table reserved; record saved in `table_bookings`; pass displayed", "PASSED"),
        ("TC-05", "Gemini AI Fallback", "Disconnect internet; send chat prompt 'Burger prices'", "Local SQL fallback triggers; matching burgers returned without crash", "PASSED")
    ]
    for idx, row in enumerate(test_data, start=1):
        for c_idx, val in enumerate(row):
            test_tbl.rows[idx].cells[c_idx].paragraphs[0].text = val
    format_table(test_tbl, header_bg="1A365D", col_widths=[0.9, 1.5, 2.0, 2.0, 0.8])

    add_h2(doc, "4.7. Earned-Value and Burn Charts")
    add_p(doc, "Agile empirical measurement charts demonstrating project velocity, scope tracking, and cost performance index:")
    
    # Burndown Chart
    burn_img = os.path.join(img_base_gen, "sprint_burndown_chart.png")
    add_fig_image(doc, burn_img, "Figure 4.1: Sprint Burndown Chart (Ideal vs. Actual Story Points Remaining)", width_in=5.8)

    # Burnup Chart
    burnup_img = os.path.join(img_base_gen, "sprint_burnup_chart.png")
    add_fig_image(doc, burnup_img, "Figure 4.2: Sprint Burnup Chart (Total Scope vs. Completed Story Points)", width_in=5.8)

    # Earned Value Chart
    ev_img = os.path.join(img_base_gen, "earned_value_chart.png")
    add_fig_image(doc, ev_img, "Figure 4.3: Earned Value Analysis Chart (Planned Value PV, Earned Value EV, Actual Cost AC)", width_in=5.8)

    add_p(doc, "Earned Value Analysis (EVA) Metrics Summary:")
    eva_tbl = doc.add_table(rows=6, cols=3)
    eva_tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
    eva_tbl.rows[0].cells[0].paragraphs[0].text = "Agile EVA Metric"
    eva_tbl.rows[0].cells[1].paragraphs[0].text = "Calculated Value"
    eva_tbl.rows[0].cells[2].paragraphs[0].text = "Interpretation & Project Health"
    
    eva_data = [
        ("Planned Value (PV)", "₹95,000", "Total budgeted value of work scheduled through Sprint 4"),
        ("Earned Value (EV)", "₹95,000", "Value of actual completed and accepted backlog items"),
        ("Actual Cost (AC)", "₹91,000", "Total engineering and infrastructure expenditure"),
        ("Cost Performance Index (CPI = EV / AC)", "1.04 (> 1.0)", "Project executed under budget (Favorable efficiency)"),
        ("Schedule Performance Index (SPI = EV / PV)", "1.00 (= 1.0)", "Project completed on schedule with zero milestone delays")
    ]
    for idx, row in enumerate(eva_data, start=1):
        for c_idx, val in enumerate(row):
            eva_tbl.rows[idx].cells[c_idx].paragraphs[0].text = val
    format_table(eva_tbl, header_bg="1A365D", col_widths=[2.5, 1.5, 3.0])

    doc.add_page_break()

    # =========================================================================
    # CHAPTER 5: SCREENSHOTS
    # =========================================================================
    add_h1(doc, "5. Screenshots")
    add_p(doc, "This chapter provides visual evidence of the implemented software interfaces across both the Web Admin Dashboard (Laravel 11) and the Customer Mobile Application (React Native / Android).")

    add_h2(doc, "5.1. Admin Web Application Screens")
    add_p(doc, "The Web portal offers restaurant administrators complete oversight of incoming orders, menu items, table layouts, and GST tax billing:")
    
    # Insert existing high-res UI images
    img18_screen = os.path.join(img_base_media, "image18.png")
    if os.path.exists(img18_screen):
        add_fig_image(doc, img18_screen, "Figure 5.1: Admin Web Portal — Order Dispatch Management & Kitchen Status Panel", width_in=5.8)

    img17_screen = os.path.join(img_base_media, "image17.png")
    if os.path.exists(img17_screen):
        add_fig_image(doc, img17_screen, "Figure 5.2: Admin Web Portal — Official GST Tax Invoice Generator & Summary", width_in=5.8)

    add_h2(doc, "5.2. Customer React Native Android Mobile Application Screens")
    add_p(doc, "The mobile client delivers a responsive native experience on Android devices, featuring food discovery, interactive cart calculations, Razorpay checkout, live order tracking, and Gemini AI interaction:")
    
    mob_screen1 = os.path.join(img_base_ppt, "image21.jpeg")
    if os.path.exists(mob_screen1):
        add_fig_image(doc, mob_screen1, "Figure 5.3: React Native Android App — Home Discovery Screen & Categorized Menu Catalog", width_in=3.2)

    mob_screen2 = os.path.join(img_base_ppt, "image6.jpeg")
    if os.path.exists(mob_screen2):
        add_fig_image(doc, mob_screen2, "Figure 5.4: React Native Android App — Shopping Cart, Table Booking & AI Dining Assistant", width_in=3.2)

    doc.add_page_break()

    # =========================================================================
    # CHAPTER 6: CONCLUSION
    # =========================================================================
    add_h1(doc, "6. Conclusion")
    add_p(doc, "The development and implementation of the Sip N' Bite platform successfully achieves the core objectives outlined in the project charter. By seamlessly connecting a robust Laravel 11 web administration engine with an intuitive React Native Android application, the system eliminates traditional operational bottlenecks in café management.")
    add_p(doc, "Key Technical Accomplishments:")
    add_bullet(doc, "1. Omnichannel Consistency: ", "Real-time synchronization between the relational MySQL database, administrative controllers, and mobile clients guarantees instant menu and price updates.")
    add_bullet(doc, "2. Frictionless Digital Transactions: ", "End-to-end integration of the Razorpay Cloud Payment Gateway with HMAC-SHA256 signature verification guarantees secure, paperless transactions.")
    add_bullet(doc, "3. Intelligent Dining Assistance: ", "Integration of Google Gemini 1.5 Flash provides customers with an interactive natural language menu concierge, complemented by offline catalog fallback.")
    add_bullet(doc, "4. Operational Efficiency: ", "Automated GST tax invoicing and table reservation engines reduce staff overhead and eliminate dining table double-bookings.")
    add_p(doc, "In conclusion, Sip N' Bite demonstrates the practical application of modern software engineering methodologies, Agile Scrum practices, and hybrid cloud architectures to deliver an industry-grade restaurant management solution.")

    doc.add_page_break()

    # =========================================================================
    # CHAPTER 7: BIBLIOGRAPHY
    # =========================================================================
    add_h1(doc, "7. Bibliography")
    add_p(doc, "Academic texts, technical documentation, and authoritative standards referenced during the engineering of the Sip N' Bite platform:")
    
    bib_items = [
        ("1. Pressman, R. S., & Maxim, B. R. (2020). ", "Software Engineering: A Practitioner's Approach (9th ed.). McGraw-Hill Education."),
        ("2. Otwell, T. (2024). ", "Laravel 11 Official Framework Documentation. Taylor Otwell & Laravel LLC. https://laravel.com/docs/11.x"),
        ("3. Meta Open Source. (2024). ", "React Native: Learn once, write anywhere. Meta Platforms Inc. https://reactnative.dev/docs/getting-started"),
        ("4. Expo Foundation. (2024). ", "Expo Documentation (SDK 52). 650 Industries Inc. https://docs.expo.dev/"),
        ("5. Razorpay Software Pvt. Ltd. (2024). ", "Razorpay Payment Gateway API & Webhook Technical Reference. https://razorpay.com/docs/api/"),
        ("6. Google DeepMind & Google Cloud. (2024). ", "Google Gemini API Documentation: Generative AI for Developers (gemini-1.5-flash). https://ai.google.dev/docs"),
        ("7. Fielding, R. T. (2000). ", "Architectural Styles and the Design of Network-based Software Architectures (Doctoral dissertation, University of California, Irvine)."),
        ("8. Oracle Corporation. (2024). ", "MySQL 8.0 Reference Manual. Oracle Corporation. https://dev.mysql.com/doc/refman/8.0/en/")
    ]
    for b_prefix, b_text in bib_items:
        add_bullet(doc, b_prefix, b_text)

    doc.add_page_break()

    # =========================================================================
    # SECTION 8: SENSITIVE CREDENTIALS, API KEYS & OUTSIDE RESOURCES
    # =========================================================================
    add_h1(doc, "8. System API Keys, Sensitive Data & Outside Resources")
    add_p(doc, "In strict compliance with the project documentation requirements, this section discloses the system configuration secrets, third-party API credentials, database connections, server endpoints, and external software packages utilized across the Web and Mobile repositories.")

    add_callout_box(doc,
        "Confidential Project Configuration Notice: This section documents production/test API keys, database credentials, server endpoints, and seeded default accounts for the Sip N Bite project (Laravel Web + React Native Mobile). In public production environments, these secrets are managed via environment variables and excluded by .gitignore.",
        title="CONFIDENTIAL AUDIT DATA:")

    add_h2(doc, "8.1. API Keys & Third-Party Service Credentials")
    add_p(doc, "The table below enumerates all third-party cloud service credentials integrated into the Laravel backend and React Native mobile client:")
    
    api_tbl = doc.add_table(rows=6, cols=4)
    api_tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
    api_tbl.rows[0].cells[0].paragraphs[0].text = "Service Name"
    api_tbl.rows[0].cells[1].paragraphs[0].text = "Variable Name"
    api_tbl.rows[0].cells[2].paragraphs[0].text = "Credential / Value"
    api_tbl.rows[0].cells[3].paragraphs[0].text = "Purpose & Architecture Scope"
    
    api_data = [
        ("Razorpay Gateway (Test Key)", "RAZORPAY_KEY", "rzp_test_TXveDStqLBJjaK", "Public Test Key ID utilized for client-side checkout in Web & Android mobile app"),
        ("Razorpay Gateway (Secret)", "RAZORPAY_SECRET", "FLXRT96zZHYQaR0M1gqy4wwO", "Backend Secret Key used by Laravel to verify cryptographic HMAC-SHA256 signatures"),
        ("Distance Matrix API", "DISTANCE_MATRIX_API_KEY", "1padf1Q3jnteeyaMMFa8kLDnzqxd815ay0VD9VP6omJwsEb8j5HJt86PIVRSvjtk", "Computes road kilometers between café kitchen and customer coordinates for dynamic delivery fees"),
        ("Google Gemini AI", "GEMINI_API_KEY", "AIzaSyB-5n7jCJ0XYskMeaoEZn8ckgMARgTLwZ0", "Generative AI key powering 'Sip N Bite AI' for natural language food recommendations"),
        ("Google Gemini Model", "GEMINI_MODEL", "gemini-1.5-flash", "The specific high-speed Google AI model invoked by `ChatbotController.php`"),
    ]
    for idx, row in enumerate(api_data, start=1):
        for c_idx, val in enumerate(row):
            api_tbl.rows[idx].cells[c_idx].paragraphs[0].text = val
    format_table(api_tbl, header_bg="1A365D", col_widths=[1.5, 1.8, 2.0, 1.9])

    add_h2(doc, "8.2. Database Credentials & Environments")
    add_p(doc, "Database connection parameters for both local development and remote deployment environments:")
    
    db_env_tbl = doc.add_table(rows=3, cols=6)
    db_env_tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
    db_env_tbl.rows[0].cells[0].paragraphs[0].text = "Environment"
    db_env_tbl.rows[0].cells[1].paragraphs[0].text = "Driver"
    db_env_tbl.rows[0].cells[2].paragraphs[0].text = "Host Endpoint"
    db_env_tbl.rows[0].cells[3].paragraphs[0].text = "Port"
    db_env_tbl.rows[0].cells[4].paragraphs[0].text = "Database Name"
    db_env_tbl.rows[0].cells[5].paragraphs[0].text = "Username"
    
    db_env_data = [
        ("Local MySQL (Active Dev)", "mysql", "127.0.0.1 (localhost)", "3306", "sipnbite", "root (Password: blank)"),
        ("Remote Server (InfinityFree)", "mysql", "sql111.infinityfree.com", "3306", "if0_43096878_sipnbite", "if0_43096878")
    ]
    for idx, row in enumerate(db_env_data, start=1):
        for c_idx, val in enumerate(row):
            db_env_tbl.rows[idx].cells[c_idx].paragraphs[0].text = val
    format_table(db_env_tbl, header_bg="1A365D", col_widths=[1.5, 0.7, 1.5, 0.6, 1.5, 1.4])

    add_h2(doc, "8.3. Network Configuration & Server Endpoints")
    add_bullet(doc, "• Active Local Wi-Fi Host IP: ", "10.206.5.191")
    add_bullet(doc, "• Secondary Hotspot IP: ", "192.168.43.191")
    add_bullet(doc, "• Loopback Interface: ", "127.0.0.1 / localhost")
    add_bullet(doc, "• Laravel REST API Mobile Endpoint: ", "http://10.206.5.191:8000/api/")
    add_bullet(doc, "• Web Admin Dashboard Login: ", "http://10.206.5.191:8000/admin/login")
    add_bullet(doc, "• Expo Dev Server (Metro Bundler): ", "exp://10.206.5.191:8081 (Port 8081)")
    add_bullet(doc, "• Café Physical Origin Coordinates: ", "Latitude: 28.5562 | Longitude: 77.2010 (Set in DistanceApiController.php)")

    add_h2(doc, "8.4. Seeded Default Accounts & Business Profile")
    add_bullet(doc, "• Admin Account: ", "Email: admin@sipnbite.com | Password: admin123 | Role: Restaurant Administrator")
    add_bullet(doc, "• Demo Customer Account: ", "Email: customer@sipnbite.com | Password: customer123 | Name: Alex Johnson | Phone: +91 9123456789")
    add_bullet(doc, "• Seeded Delivery Riders: ", "1. Rahul Sharma (Phone: +91 9811223344, Vehicle: DL 01 AB 1234) | 2. Priya Verma (Phone: +91 9822334455, Vehicle: DL 03 XY 9876)")
    add_bullet(doc, "• Café Business Profile: ", "Sip N Bite Café & Bistro | Address: 108 Gourmet Avenue, Sector 18, City Center | Phone: +91 9876543210 | GST: 5% | Base Delivery Fee: ₹40")

    add_h2(doc, "8.5. Comprehensive Outside Resources & Library Dependencies")
    add_p(doc, "Complete dependency manifests powering the Laravel Web and React Native Mobile applications:")
    
    res_tbl = doc.add_table(rows=10, cols=4)
    res_tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
    res_tbl.rows[0].cells[0].paragraphs[0].text = "Software Package / Resource"
    res_tbl.rows[0].cells[1].paragraphs[0].text = "Repository Subsystem"
    res_tbl.rows[0].cells[2].paragraphs[0].text = "Version / Source"
    res_tbl.rows[0].cells[3].paragraphs[0].text = "Role in Project Architecture"
    
    res_data = [
        ("laravel/framework", "WEB (Composer)", "v11.x (PHP 8.2+)", "Core MVC web backend, REST routing engine, and Eloquent ORM"),
        ("laravel/sanctum", "WEB (Composer)", "v4.x", "Lightweight Bearer token authentication system for mobile clients"),
        ("razorpay/razorpay", "WEB (Composer)", "v2.9.x", "Official Razorpay PHP SDK for order creation and signature verification"),
        ("guzzlehttp/guzzle", "WEB (Composer)", "v7.8.x", "HTTP client for cURL calls to Google Gemini AI and Geocoding APIs"),
        ("react-native", "React_Native (NPM)", "v0.76.x", "Cross-platform mobile application development framework"),
        ("expo", "React_Native (NPM)", "SDK 52.x", "Managed mobile application runtime, toolchain, and OTA bundler"),
        ("@react-navigation/native", "React_Native (NPM)", "v7.x", "Mobile screen navigation, stack navigator, and bottom tab bar"),
        ("axios", "React_Native (NPM)", "v1.7.x", "Promise-based HTTP client for consuming Laravel REST endpoints"),
        ("lucide-react-native", "React_Native (NPM)", "v0.475.x", "Modern vector icon system for mobile interface ergonomics")
    ]
    for idx, row in enumerate(res_data, start=1):
        for c_idx, val in enumerate(row):
            res_tbl.rows[idx].cells[c_idx].paragraphs[0].text = val
    format_table(res_tbl, header_bg="1A365D", col_widths=[1.8, 1.5, 1.4, 2.5])

    add_h2(doc, "8.6. Production `.env` Template Snippet")
    add_p(doc, "The ready-to-deploy `.env` configuration file deployed at `d:\\SIP_N_BITE\\WEB\\.env`:")
    
    env_code = (
        "APP_NAME=\"Sip N Bite\"\n"
        "APP_ENV=local\n"
        "APP_KEY=base64:1WJLE8Pp04VPvAK/UHapSS+C4p4iQDAS0QjR5WP4aaA=\n"
        "APP_DEBUG=true\n"
        "APP_URL=http://localhost:8000\n\n"
        "DB_CONNECTION=mysql\n"
        "DB_HOST=127.0.0.1\n"
        "DB_PORT=3306\n"
        "DB_DATABASE=sipnbite\n"
        "DB_USERNAME=root\n"
        "DB_PASSWORD=\n\n"
        "# Razorpay Gateway Integration\n"
        "RAZORPAY_KEY=rzp_test_TXveDStqLBJjaK\n"
        "RAZORPAY_SECRET=FLXRT96zZHYQaR0M1gqy4wwO\n\n"
        "# Maps & Geocoding Distance Matrix\n"
        "DISTANCE_MATRIX_API_KEY=1padf1Q3jnteeyaMMFa8kLDnzqxd815ay0VD9VP6omJwsEb8j5HJt86PIVRSvjtk\n\n"
        "# Google Gemini Generative AI\n"
        "GEMINI_API_KEY=AIzaSyB-5n7jCJ0XYskMeaoEZn8ckgMARgTLwZ0\n"
        "GEMINI_MODEL=gemini-1.5-flash\n"
    )
    
    tbl_code = doc.add_table(rows=1, cols=1)
    tbl_code.alignment = WD_TABLE_ALIGNMENT.CENTER
    c_code = tbl_code.cell(0, 0)
    set_cell_background(c_code, "2D3748")
    set_cell_margins(c_code, top=100, bottom=100, left=150, right=150)
    p_code = c_code.paragraphs[0]
    r_code = p_code.add_run(env_code)
    r_code.font.name = 'Consolas'
    r_code.font.size = Pt(8.5)
    r_code.font.color.rgb = RGBColor(226, 232, 240)
    set_table_borders(tbl_code, color="1A202C", sz="6")

    # Save Document
    doc.save(output_docx_path)
    print(f"Successfully compiled and saved document to: {output_docx_path}")
    
    # Copy to project root
    try:
        shutil.copy2(output_docx_path, root_copy_path)
        print(f"Successfully copied document to project root: {root_copy_path}")
    except Exception as e:
        print(f"Notice: Root copy error: {e}")

if __name__ == '__main__':
    build_full_document()
