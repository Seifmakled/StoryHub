# UGC Platform - User Generated Content Blogging Site

A modern, responsive social publishing/blogging platform built with PHP MVC architecture, featuring user-generated content, social interactions, and a comprehensive admin dashboard.

## 🚀 Features

### User Features
- **User Authentication**
  - Login/Register with email verification
  - Password reset with verification code
  - Social login integration (Google, Facebook)
  
- **Content Management**
  - Create, edit, and publish articles
  - Rich text editor for content
  - Featured images and categories
  - Draft and publish functionality

- **Social Features**
  - Like and comment on articles
  - Follow other users
  - Share articles
  - Save articles for later

- **Discovery**
  - Explore page with filtering (Trending, Featured, Recent, Popular)
  - Category-based browsing
  - Search functionality
  - Tag-based navigation

- **User Profiles**
  - Personal profile pages
  - Profile and cover photo customization
  - Article portfolio
  - Social links
  - Follower/Following system

### Admin Features
- **Dashboard**
  - Overview statistics
  - Traffic analytics
  - User management
  - Article moderation
  - Comment management
  - Reports handling

## 📁 Project Structure

```
Blog-Project/
├── app/
│   ├── controllers/          # Backend logic (to be implemented)
│   ├── partials/            # Reusable components
│   │   ├── header.php       # HTML head section
│   │   ├── navbar.php       # Navigation bar
│   │   └── footer.php       # Footer section
│   └── views/               # Frontend pages
│       ├── landing.php      # Home page
│       ├── login.php        # Login page
│       ├── register.php     # Registration page
│       ├── forgot-password.php  # Password reset
│       ├── verify.php       # Email verification
│       ├── profile.php      # User profile
│       ├── explore.php      # Browse articles
│       ├── admin-dashboard.php  # Admin panel
│       ├── logout.php       # Logout handler
│       └── 404.php          # Error page
├── config/
│   └── db.php              # Database connection (OOP)
├── public/
│   ├── css/                # Stylesheets
│   │   ├── landing.css
│   │   ├── login.css
│   │   ├── profile.css
│   │   ├── explore.css
│   │   └── admin-dashboard.css
│   ├── images/             # Assets
│   │   ├── default-avatar.jpg
│   │   ├── article-placeholder.jpg
│   │   └── cover-placeholder.jpg
│   └── js/                 # JavaScript files
│       ├── landing.js
│       ├── login.js
│       ├── register.js
│       ├── forgot-password.js
│       ├── verify.js
│       ├── profile.js
│       ├── explore.js
│       └── admin-dashboard.js
└── index.php               # Main routing file
```

## 🛠️ Installation

### Prerequisites
- XAMPP (or any PHP development environment)
- PHP 7.4 or higher
- MySQL 5.7 or higher

### Setup Steps

1. **Clone/Copy the project to your XAMPP htdocs folder:**
   ```
   C:\xampp\htdocs\Blog-Project
   ```

2. **Start XAMPP:**
   - Start Apache
   - Start MySQL

3. **Database Setup:**
   - The database will be created automatically when you first run the application
   - Database name: `ugc_platform`
   - Tables will be created automatically:
     - `users`
     - `articles`
     - `likes`
     - `comments`

4. **Access the application:**
   ```
   http://localhost/Blog-Project/
   ```

## 🎨 Design Reference

The design is inspired by:
- **Blogger.com** - Clean, content-focused layout
- **Medium.com** - Modern, minimalist aesthetic

## 📄 Pages Overview

### Public Pages
- **Landing Page** (`/?page=home`) - Homepage with featured articles, trending content, and categories
- **Login** (`/?page=login`) - User authentication with social login options
- **Register** (`/?page=register`) - New user registration with email verification
- **Forgot Password** (`/?page=forgot-password`) - Password reset flow
- **Verify Email** (`/?page=verify`) - Email verification with 6-digit code
- **Explore** (`/?page=explore`) - Browse all articles with filters and search
- **Profile** (`/?page=profile&id=USER_ID`) - User profile page

### Protected Pages
- **Admin Dashboard** (`/?page=admin`) - Admin panel with analytics and management tools

## 🔧 Database Configuration

The database configuration is handled in `config/db.php` using OOP:

```php
class Database {
    private $host = 'localhost';
    private $user = 'root';
    private $pass = '';
    private $dbname = 'ugc_platform';
    
    // Automatic connection and database/table creation
}
```

## 🎯 Routing

All routes are handled in `index.php`:

```php
$routes = [
    'home' => 'app/views/landing.php',
    'login' => 'app/views/login.php',
    'register' => 'app/views/register.php',
    // ... more routes
];
```

## 🎨 Styling

Each page has its own CSS file for easy customization:
- Responsive design (mobile-first approach)
- Modern color scheme with CSS variables
- Smooth animations and transitions
- Custom components (cards, buttons, forms, etc.)

## 📱 Responsive Design

The platform is fully responsive with breakpoints at:
- Desktop: 1024px and above
- Tablet: 768px - 1023px
- Mobile: 767px and below

## 🔐 Security Features (To Be Implemented)

- Password hashing with bcrypt
- SQL injection prevention using PDO prepared statements
- XSS protection
- CSRF tokens
- Session management
- Email verification
- Password reset tokens with expiry

## 🚧 To-Do / Backend Implementation

The following need to be implemented:

1. **Authentication System**
   - Login/Register logic
   - Email verification
   - Password reset
   - Session management

2. **Article Management**
   - CRUD operations
   - Image uploads
   - Rich text editor integration

3. **Social Features**
   - Like/Unlike functionality
   - Comments system
   - Follow/Unfollow
   - Article sharing

4. **Admin Panel Backend**
   - User management
   - Article moderation
   - Analytics data
   - Report handling

5. **Search & Filters**
   - Full-text search
   - Category filters
   - Tag system
   - Trending algorithm

## 📚 Technologies Used

- **Backend:** PHP 7.4+ (OOP)
- **Database:** MySQL with PDO
- **Frontend:** HTML5, CSS3, JavaScript (Vanilla)
- **Icons:** Font Awesome 6.4
- **Fonts:** Google Fonts (Inter, Playfair Display)
- **Charts:** Chart.js (for admin dashboard)

## 🎨 Color Scheme

```css
--primary-color: #6366f1;    /* Indigo */
--secondary-color: #8b5cf6;  /* Purple */
--accent-color: #ec4899;     /* Pink */
--dark-color: #1e293b;       /* Dark Slate */
--light-color: #f8fafc;      /* Light Gray */
--text-color: #334155;       /* Slate */
```

## 📝 Notes for Backend Integration

1. Replace mock data in views with actual database queries
2. Implement form validation on the server side
3. Add AJAX calls for dynamic content loading
4. Implement image upload functionality
5. Add pagination for article lists
6. Implement notification system
7. Add email sending functionality for verification codes

## 🤝 Contributing

This is a front-end template. Backend functionality needs to be implemented.

## 📄 License

Free to use for learning and commercial projects.

## 🆘 Support

For issues or questions, refer to the code comments or PHP/MySQL documentation.

---

**Note:** This is a front-end implementation. All forms and interactive elements currently use JavaScript for demonstration. Backend logic needs to be implemented in the `app/controllers/` directory.
