# E-Commerce Website - Technology Stack

## Project Overview
A full-featured e-commerce platform with customer-facing website and admin management system. Built with modern web technologies, cloud services, and best practices for security and scalability.

---

## Backend Technologies

### Server-Side Language
- **PHP 8.3.6** - Server-side scripting language
  - Features: Type declarations, match expressions, named arguments, attributes
  - Used for: Request handling, business logic, database queries, admin operations

### Web Server
- **Apache 2.x** - HTTP server
  - Modules: mod_rewrite (URL routing), mod_ssl (HTTPS)
  - Configuration: Virtual hosts, .htaccess rewrites

### Database
- **PostgreSQL** - Relational database management system
  - Hosted on: **Supabase** (Cloud PostgreSQL with pooling)
  - Connection: Supabase Pooler (aws-1-ap-northeast-1.pooler.supabase.com:5432)
  - Features: ACID transactions, foreign key constraints, JSON support
  - Tables: 9 main tables (login_data, products, cart, orders, order_items, subscribers, contact_submissions, admin_users, home_banners)

### PHP Extensions Used
- **PDO (PHP Data Objects)** - Database abstraction layer
  - Drivers: pdo_pgsql (PostgreSQL), pdo_mysql (for fallback)
- **cURL** - HTTP client for API requests
- **OpenSSL** - SSL/TLS encryption
- **JSON** - JSON encoding/decoding
- **mbstring** - Multi-byte string handling
- **Session** - Session management

---

## Frontend Technologies

### Markup & Styling
- **HTML5** - Semantic markup
- **CSS3** - Styling and animations
  - Features: Flexbox, Grid, Gradients, Transitions, Media Queries
- **SCSS/CSS** - Preprocessor support (potential)

### JavaScript Framework & Library
- **JavaScript (ES6+)** - Client-side scripting
- **jQuery 3.7.1** - DOM manipulation and AJAX
  - CDN: https://code.jquery.com/jquery-3.7.1.min.js

### CSS Framework
- **Bootstrap 5.3.3** - Responsive UI framework
  - CDN: https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css
  - Features: Grid system, components, utilities, modal, dropdown, form validation
  - JS Bundle: https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js

### Icon Library
- **Font Awesome 6.5.0** - Icon font library
  - CDN: https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css
  - Icons used: cart, user, search, check, star, lock, truck, undo, etc.

### Typography
- **Google Fonts**
  - Playfair Display (serif font for headers/branding)
  - Montserrat (sans-serif font for body text)
  - URL: https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&display=swap

---

## Cloud Services & External APIs

### Media Hosting
- **Cloudinary** - Image hosting and management
  - Cloud: dhzk2xuvt
  - Features: Image upload, URL generation, folder organization, signed uploads
  - API Endpoint: https://api.cloudinary.com/v1_1/{cloud}/image/upload
  - Use cases: Product images, banner images, media asset storage

### Database Hosting
- **Supabase** - PostgreSQL as a Service
  - Host: aws-1-ap-northeast-1.pooler.supabase.com
  - Region: AWS ap-northeast-1 (Tokyo)
  - Features: Real-time sync, automatic backups, API layer
  - Authentication: Username/password with connection pooling

### Version Control
- **GitHub** - Source code repository
  - Repository: https://github.com/DARSHAN973/E-commerce-website
  - Branch: main
  - Features: Push/pull, collaboration, version history

---

## Development Tools & Utilities

### Version Control
- **Git** - Distributed version control system
  - Remote: GitHub (HTTPS)
  - Workflow: Commit, push, pull, merge

### Code Editor
- **Visual Studio Code** - Code editor
  - Extensions: PHP support, Git integration, database tools
  - Settings: Workspace configuration in .vscode/settings.json

### API Tools
- **REST API** - HTTP-based communication
  - Method: AJAX (Fetch API, jQuery AJAX)
  - Format: JSON request/response
  - Endpoints: includes/auth.php, includes/orders.php, includes/cart.php

### Database Tools
- **PDO Prepared Statements** - SQL query safety
  - Protection: SQL injection prevention via parameterized queries

---

## System Architecture Components

### Modules & Structure
```
├── Frontend Pages (8 main pages)
│   ├── index.php (Homepage with carousel)
│   ├── men.php (Men's category)
│   ├── women.php (Women's category)
│   ├── collection.php (Special collection)
│   ├── product.php (Product detail page)
│   ├── cart.php (Shopping cart)
│   ├── checkout.php (Order placement)
│   ├── order-confirmation.php (Order confirmation)
│   ├── contact.php (Contact us form)
│   └── search.php (Product search)
│
├── Admin Panel (/admin)
│   ├── index.php (Main admin dashboard)
│   ├── logout.php (Admin sign-out)
│   ├── common.php (Shared functions)
│   └── assets/admin.css (Admin styling)
│
└── Includes (Reusable Components)
    ├── db.php (Database connection)
    ├── auth.php (User authentication API)
    ├── auth-modal.php (Login/register modal)
    ├── navbar.php (Navigation header)
    ├── footer.php (Footer)
    ├── cart.php (Cart operations)
    ├── orders.php (Order processing)
    ├── product.php (Product display)
    ├── product-grid.php (Product grid template)
    ├── profile-update.php (User profile management)
    └── subscribe.php (Newsletter subscription)
```

---

## Security Technologies

### Password Management
- **password_hash()** - PHP's bcrypt hashing
- **password_verify()** - Secure password verification

### Database Security
- **PDO Prepared Statements** - SQL injection prevention
- **Parameter Binding** - Query parameterization

### Session Management
- **PHP Sessions** - Server-side session storage
  - Variables: $_SESSION['user_id'], $_SESSION['logged_in'], etc.

### HTTPS/SSL
- **OpenSSL** - TLS encryption for database connections
- **Supabase Connection** - sslmode=require forced SSL

### Input Validation
- **htmlspecialchars()** - XSS prevention
- **filter_var()** - Email validation
- **Type casting** - Data type enforcement

---

## Data Format Standards

### JSON
- Used for: API responses, AJAX data transfer, configuration
- Encoding: json_encode() for responses
- Decoding: json_decode() for request parsing

### SQL
- Dialect: PostgreSQL-specific syntax
  - Features: RETURNING clause, array types, JSON columns
  - Transactions: BEGIN, COMMIT, ROLLBACK

---

## Performance & Caching

### Browser Caching
- CDN resources cached locally
- CSS/JS minification via CDN

### Database Optimization
- Connection pooling via Supabase pooler
- Indexed queries (primary/foreign keys)
- LIMIT clauses for pagination

### Image Optimization
- Cloudinary automatic image optimization
- Responsive image URLs
- Folder organization for media assets

---

## Testing & Quality

### Manual Testing
- Unit tests for critical functions
- Integration tests for workflows (register → login → checkout)
- Browser testing for responsive design

### Code Quality
- PHP syntax validation: `php -l`
- SQL syntax validation via PostgreSQL
- Error handling: try-catch blocks, PDOException

---

## Deployment Requirements

### Hosting Environment
- **OS**: Linux (Ubuntu 24.04 compatible)
- **PHP**: 8.1+ (using 8.3.6)
- **Apache**: 2.4+
- **PostgreSQL Client**: For psql (optional, PDO used instead)

### Required PHP Extensions
```
PDO, pdo_pgsql, pdo_mysql, curl, openssl, json, mbstring, 
session, filter, ctype, fileinfo, zip
```

### Network Requirements
- Outbound HTTPS to Supabase (database)
- Outbound HTTPS to Cloudinary (media API)
- Outbound HTTP/HTTPS to GitHub (version control)

---

## Browser Compatibility

### Supported
- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+
- Mobile browsers (iOS Safari, Chrome Mobile)

### Features Used
- CSS Grid & Flexbox
- ES6 JavaScript
- Fetch API
- LocalStorage (potential)
- Media queries

---

## Database Schema Technologies

### Normalization
- 3NF (Third Normal Form) design
- Foreign key constraints
- NOT NULL constraints
- DEFAULT values

### Data Types
- BIGSERIAL (auto-increment primary keys)
- VARCHAR (text fields)
- DECIMAL (monetary amounts)
- BOOLEAN (flags)
- TIMESTAMP (audit trails)
- TEXT (long content)

---

## Future Technology Additions

### Recommended Additions
- Payment Gateway: Razorpay / Stripe
- Email Service: Sendgrid / AWS SES
- Analytics: Google Analytics / Mixpanel
- Caching: Redis / Memcached
- Queue System: RabbitMQ / AWS SQS
- Container: Docker + Docker Compose

### Optional Enhancements
- Elasticsearch: Full-text search
- GraphQL: Alternative API layer
- Microservices: Function separation
- WebSockets: Real-time notifications
- AI/ML: Product recommendations

---

## Summary Table

| Category | Technology | Version/Details |
|----------|-----------|-----------------|
| **Language** | PHP | 8.3.6 |
| **Database** | PostgreSQL | Supabase Cloud |
| **Server** | Apache | 2.x |
| **Frontend Framework** | Bootstrap | 5.3.3 |
| **JavaScript** | jQuery + Vanilla JS | 3.7.1 |
| **Icons** | Font Awesome | 6.5.0 |
| **API Client** | cURL + Fetch API | Native |
| **Media Storage** | Cloudinary | Cloud-based |
| **Version Control** | Git + GitHub | HTTPS |
| **CSS Preprocessor** | Native CSS | ES3+ |
| **Auth Method** | Session + Password Hash | bcrypt |

---

## Total Technology Count: 25+

**Core Stack**: PHP 8.3 + PostgreSQL + Bootstrap 5 + Cloudinary + Supabase
**Frontend**: HTML5, CSS3, JavaScript (ES6+), jQuery, Font Awesome
**Cloud Services**: Supabase, Cloudinary, GitHub
**DevTools**: Git, VS Code, Apache

All technologies are production-ready, widely supported, and maintainable for long-term operations.
