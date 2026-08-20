# 📸 Lumina Studio & Productions — Luxury Photoshoot Platform

A production-ready, ultra-premium, fully dynamic Photoshoot Studio web application built with **Laravel 11**, **TailwindCSS**, **Alpine.js**, **MySQL**, and **Razorpay Integration**.

---

## 🌟 Key Features & Architectural Highlights

### 1. 🎨 Real-Time Dynamic Theme & Background Engine
- **Full Website Background Control**: Change the entire website canvas background color (`bg_color`) dynamically from the Super Admin Panel.
- **Dynamic Palette Pickers**: Customize Primary Brand Color, Hover Tones, Accent/Neon Glow, Surface Card Backgrounds, and Typography Text colors.
- **6 One-Click Aesthetic Presets**:
  - 🏆 **Luxury Gold** (Default Obsidian & Warm Gold)
  - ⚡ **Obsidian Neon** (Cyberpunk Cyan & Hot Pink)
  - 🌿 **Royal Emerald** (Deep Forest Green & Amber)
  - 🌸 **Rose Champagne** (High Fashion Blush Pink)
  - 🔮 **Cyberpunk Violet** (Electric Violet & Neon Magenta)
  - ⚪ **Clean Light** (Editorial Minimal Monochrome)
- **Zero Hardcoded Themes**: Applied globally using CSS root custom variables (`:root`) and a global Blade View Composer.

---

### 2. 🚀 Fully Dynamic SEO & Google Schema Markup
- **JSON-LD Structured Data**: Implemented schema for `PhotographyStudio` and `LocalBusiness` with business hours, geo address, and price ranges.
- **Social Graph OpenGraph & Twitter Cards**: Dynamic `og:title`, `og:image`, `og:description`, `og:site_name`, and canonical links.
- **Automated XML Sitemap**: Accessible at `/sitemap.xml` listing all pages, signature packages, and blog articles.
- **Dynamic `robots.txt`**: Configured at `/robots.txt` pointing search crawlers to the sitemap.

---

### 3. 💳 Razorpay Payment Integration & Simulation Mode
- **Dual-Mode Razorpay Gateway**:
  - **Live Mode**: Standard Razorpay JavaScript checkout popup with SHA256 signature verification.
  - **Sandbox / 1-Click Simulation Mode**: Enables instant demo testing without failing when live API credentials are not yet configured in `.env`.
- **Webhook Handlers**: Webhooks endpoint `POST /razorpay/webhook` (and `/webhooks/razorpay`) configured with CSRF exclusion in `bootstrap/app.php`.

---

### 4. 👥 3-Tier Multi-Role Architecture
1. **👑 Super Admin (`role: super_admin`)**:
   - Access to the Executive Studio Dashboard (`/admin/dashboard`).
   - Theme customizer & color pickers.
   - Comprehensive CRUD for **Pricing Packages**, **Master Gallery Showcase**, **Journal Articles**, **Client Inquiries**, and **Bookings Lifecycle**.
   - Manage and approve/suspend Photographer partners.
2. **📸 Photographer / Vendor (`role: vendor`)**:
   - Dedicated dashboard (`/vendor/dashboard`).
   - Manage photography packages (`/vendor/packages`).
   - Track assigned client bookings and update session workflow.
3. **👤 Client / Guest (`role: client`)**:
   - Client Portal (`/client/dashboard`).
   - **5-Stage Visual Workflow Tracker**:
     1. `1. Reserved & Confirmed`
     2. `2. Pre-Shoot Briefing`
     3. `3. In-Studio Shoot Active`
     4. `4. Color Grading & Retouching`
     5. `5. Master High-Res Delivery`
   - Printable invoices and booking reference receipts.

---

### 5. 🌐 Dynamic Public Modules
- **Dynamic Landing Page (`/`)**:
  - Super Admin configurable Hero Banner (Title, Subtitle, Backdrop URL, Pill Badge).
  - Live animated statistics counter (Years, Shoots, Awards, Ratings).
  - Specialty Disciplines Showcase (Weddings, Fashion, Portraits).
  - Dynamic Pricing Packages with multi-feature checklists.
  - Master Gallery preview.
  - Interactive Client Testimonials slider (Alpine.js).
  - Collapsible FAQ Accordion.
- **About Studio (`/about`)**: Studio chronicles, manifesto, and team equipment.
- **Master Gallery (`/gallery`)**: Masonry grid with category filter tabs and high-res lightbox popup.
- **Studio Journal (`/blog` & `/blog/{slug}`)**: Editorial masterclasses with rich HTML typography and related articles.
- **Concierge Desk (`/contact`)**: Dynamic contact form connected to database messages inbox and embedded Google Maps.
- **Legal Compliance (`/terms` & `/disclaimer`)**: Comprehensive policies for studio bookings, cancellation terms, and image licensing.

---

## 🔑 Default Seed Credentials

After running `php artisan db:seed`, use the following credentials:

| Role | Email | Password | Dashboard URL |
|---|---|---|---|
| **Super Admin** | `admin@studio.test` | `password` | `/admin/dashboard` |
| **Photographer / Vendor** | `vendor@studio.test` | `password` | `/vendor/dashboard` |
| **Client** | `client@studio.test` | `password` | `/client/dashboard` |

---

## 🛠️ Installation & Setup Instructions

### Step 1: Clone or Open Project Directory
```bash
cd c:\xampp\htdocs\vk\Studio
```

### Step 2: Configure Environment (`.env`)
Ensure database credentials are set:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306 (or 3307)
DB_DATABASE=your_database_name
DB_USERNAME=root
DB_PASSWORD=

# Razorpay Keys (Optional for live payments; simulation mode works automatically)
RAZORPAY_KEY_ID=rzp_test_xxxxxx
RAZORPAY_KEY_SECRET=xxxxxx
```

### Step 3: Run Database Migrations & Seeds
```bash
php artisan migrate --seed
```

### Step 4: Compile Frontend Assets
```bash
npm install
npm run build
```

### Step 5: Start Development Server
```bash
php artisan serve
```
Visit: `http://127.0.0.1:8000`

---

## 🗄️ Database Tables Overview

- `settings`: Key-value configuration for theme colors, background colors, studio identity, and SEO tags.
- `users`: Authentication records with role column (`super_admin`, `vendor`, `client`).
- `packages`: Studio pricing plans with price range, description, feature lists (JSON), and cover images.
- `bookings`: Session reservations with date, amount, status, and Razorpay payment identifiers.
- `payments`: Transaction ledger with captured amounts and payload logs.
- `gallery`: Portfolio photographs with categorized tags.
- `blogs`: Journal articles with SEO meta tags and slugs.
- `contact_messages`: Inquiries sent from the public contact form.
- `visitors`: Unique IP tracker for real analytics.
- `vendors`: Photographer profile partners linked to user accounts.

---

## 🔮 Future Expansion Ideas & Next Steps

When you want to extend this project in the future, you can easily add:
1. **Multi-Image Deliverables Vault**: Enable clients to download ZIP archives of their edited raw and JPEG deliverables directly from their dashboard.
2. **Automated SMS & WhatsApp Alerts**: Integrate Twilio or WhatsApp Business API to send automated appointment reminders and invoice links.
3. **Cal.com / Google Calendar Synchronization**: Sync booked dates directly to studio Google Calendars to prevent double-booking.
4. **Client Review Submission Form**: Allow verified clients to submit star ratings and photo reviews directly into the testimonials feed.
