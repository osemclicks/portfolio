# Photography Portfolio Website - Setup Guide

## Quick Setup Instructions

### Step 1: Database Setup

1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Create a new database named `portfolio_db`
3. Import the schema:
   - Click on `portfolio_db` database
   - Go to "SQL" tab
   - Copy and paste contents from `database/schema.sql`
   - Click "Go" to execute
4. Import seed data:
   - Go to "SQL" tab again
   - Copy and paste contents from `database/seed.sql`
   - Click "Go" to execute
5. Seed admin accounts:
   - Open browser and visit: `http://localhost/projects/portfolio/database/seed_admins.php`
   - This will properly hash your admin passwords

### Step 2: Create Upload Directories

The upload directories should be created automatically, but if you encounter any issues, manually create:
- `uploads/portfolio/`
- `uploads/blogs/`

### Step 3: Access the Website

**User-Facing Site:**
- Homepage: http://localhost/projects/portfolio/index.php
- About: http://localhost/projects/portfolio/about.php
- Services: http://localhost/projects/portfolio/services.php
- Portfolio: http://localhost/projects/portfolio/portfolio.php
- Blogs: http://localhost/projects/portfolio/blogs.php
- Contact: http://localhost/projects/portfolio/contact.php
- FAQs: http://localhost/projects/portfolio/faqs.php

**Admin Panel:**
- Login: http://localhost/projects/portfolio/admin/login.php
- Dashboard: http://localhost/projects/portfolio/admin/dashboard.php

### Step 4: Admin Login Credentials

**Admin 1:**
- Email: osemclicks@gmail.com
- Password: heroes.verse57

**Admin 2:**
- Email: karthikbillava1107@gmail.com
- Password: heroes.verse58

## Features Implemented

### User Side
✅ Dynamic homepage with database-driven portfolio
✅ About Us page with CMS-editable content
✅ Services page with 6 service categories
✅ Portfolio gallery with search and category filters
✅ Portfolio detail pages
✅ Blog listing and detail pages
✅ Contact form with AJAX submission
✅ FAQs page with expandable answers
✅ Responsive design across all pages

### Admin Panel
✅ Secure login system with password hashing
✅ Dashboard with analytics (visitors, portfolio, blogs, submissions)
✅ Portfolio management (add/edit/delete)
✅ Category management
✅ Blog management with rich text editor
✅ Notifications system (contact form submissions)
✅ Page content management (CMS)
✅ FAQ management
✅ Gear management

## Security Features
✅ Password hashing with bcrypt
✅ SQL injection prevention (PDO prepared statements)
✅ XSS protection (htmlspecialchars)
✅ CSRF token protection
✅ File upload validation
✅ Session timeout
✅ Input sanitization

## Troubleshooting

### Database Connection Error
- Check that XAMPP MySQL is running
- Verify database name is `portfolio_db`
- Check username/password in `config/database.php` (default: root/no password)

### Upload Directory Permissions
- Ensure uploads directory has write permissions (755 or 777)
- On Windows, this is usually automatic

### Admin Can't Login
- Make sure you ran `database/seed_admins.php` to hash passwords
- Check that `admins` table has data
- Clear browser cookies/cache

## Next Steps

1. Customize page content via Admin Panel → Page Content
2. Add your portfolio items via Admin Panel → Portfolio
3. Write blog posts via Admin Panel → Blogs
4. Modify FAQs via Admin Panel → FAQs
5. Update gear information via Admin Panel → Gears

## File Structure

```
portfolio/
├── admin/                    # Admin panel
│   ├── blogs/               # Blog management
│   ├── categories/          # Category management
│   ├── content/             # CMS pages
│   ├── includes/            # Admin header/sidebar
│   ├── notifications/       # Contact submissions
│   ├── portfolio/           # Portfolio management
│   ├── dashboard.php        # Admin dashboard
│   ├── login.php            # Admin login
│   └── logout.php           # Admin logout
├── api/                     # AJAX endpoints
│   └── contact-submit.php   # Contact form handler
├── config/                  # Configuration files
│   ├── config.php           # Global settings
│   └── database.php         # Database connection
├── css/                     # Stylesheets
│   ├── admin.css            # Admin panel styles
│   └── style.css            # User-facing styles
├── database/                # Database files
│   ├── schema.sql           # Database structure
│   ├── seed.sql             # Initial data
│   └── seed_admins.php      # Admin password hasher
├── images/                  # Static images
├── includes/                # Shared components
│   ├── footer.php           # Site footer
│   ├── functions.php        # Utility functions
│   └── header.php           # Site header
├── js/                      # JavaScript files
│   ├── contact.js           # Contact form AJAX
│   └── script.js            # Main scripts
├── uploads/                 # Uploaded files
│   ├── blogs/               # Blog images
│   └── portfolio/           # Portfolio images
├── about.php                # About Us page
├── blogs.php                # Blog listing
├── blog-detail.php          # Single blog view
├── contact.php              # Contact page
├── faqs.php                 # FAQs page
├── index.php                # Homepage
├── portfolio.php            # Portfolio gallery
├── portfolio-detail.php     # Single portfolio view
└── services.php             # Services page
```

## Support

For issues or questions, refer to the implementation_plan.md document for detailed technical information.
