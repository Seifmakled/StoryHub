# 🚀 Quick Start Guide - UGC Platform

## Installation Steps

### 1. Prerequisites
- XAMPP installed on your computer
- Modern web browser (Chrome, Firefox, Edge)

### 2. Installation

#### Option A: Fresh Installation
1. Copy the entire `Blog-Project` folder to:
   ```
   C:\xampp\htdocs\
   ```

2. Start XAMPP:
   - Open XAMPP Control Panel
   - Click "Start" for Apache
   - Click "Start" for MySQL

3. Open your browser and visit:
   ```
   http://localhost/Blog-Project/
   ```

4. The database will be created automatically on first run!

#### Option B: If you already have the files
1. Make sure files are in: `C:\xampp\htdocs\Blog-Project\`
2. Start Apache and MySQL in XAMPP
3. Visit: `http://localhost/Blog-Project/`

### 3. Add Placeholder Images (Optional but recommended)

Download or create these images and save them in `public/images/`:

1. **default-avatar.jpg** (300x300px)
   - Quick placeholder: https://via.placeholder.com/300x300/6366f1/ffffff?text=Avatar
   - Or download from: https://unsplash.com/s/photos/avatar

2. **article-placeholder.jpg** (800x600px)
   - Quick placeholder: https://via.placeholder.com/800x600/e2e8f0/334155?text=Article
   - Or download from: https://unsplash.com/s/photos/blog

3. **cover-placeholder.jpg** (1600x400px)
   - Quick placeholder: https://via.placeholder.com/1600x400/667eea/ffffff?text=Cover
   - Or download from: https://unsplash.com/s/photos/banner

4. **hero-illustration.svg** (any size)
   - Use from: https://undraw.co/illustrations
   - Or create a simple gradient background

### 4. Test the Pages

Visit these URLs to explore:

- **Home:** http://localhost/Blog-Project/
- **Login:** http://localhost/Blog-Project/?page=login
- **Register:** http://localhost/Blog-Project/?page=register
- **Explore:** http://localhost/Blog-Project/?page=explore
- **Admin Dashboard:** http://localhost/Blog-Project/?page=admin

## 🎯 Default Routes

| Page | URL |
|------|-----|
| Landing/Home | `/?page=home` or just `/` |
| Login | `/?page=login` |
| Register | `/?page=register` |
| Forgot Password | `/?page=forgot-password` |
| Email Verification | `/?page=verify` |
| Profile | `/?page=profile&id=USER_ID` |
| Explore Articles | `/?page=explore` |
| Admin Dashboard | `/?page=admin` |
| Logout | `/?page=logout` |

## 📊 Database Info

**Database Name:** `ugc_platform`

**Tables Created Automatically:**
- `users` - User accounts and profiles
- `articles` - Blog posts/articles
- `likes` - Article likes
- `comments` - Article comments

**Default Connection:**
- Host: `localhost`
- User: `root`
- Password: `` (empty)
- Port: `3306` (default MySQL port)

## 🎨 Customization

### Change Colors
Edit the CSS variables in any CSS file:
```css
:root {
    --primary-color: #6366f1;    /* Change this */
    --secondary-color: #8b5cf6;  /* And this */
    --accent-color: #ec4899;     /* And this */
}
```

### Change Site Name
Edit in `app/partials/navbar.php` and `app/partials/footer.php`:
```php
<span>StoryHub</span>  <!-- Change to your site name -->
```

### Modify Database Settings
Edit `config/db.php`:
```php
private $host = 'localhost';
private $user = 'root';
private $pass = '';  // Add password if needed
private $dbname = 'ugc_platform';  // Change database name
```

## 🐛 Troubleshooting

### Problem: "Database connection failed"
**Solution:**
- Make sure MySQL is running in XAMPP
- Check database credentials in `config/db.php`

### Problem: "Page not found" or 404 errors
**Solution:**
- Check that Apache is running in XAMPP
- Verify the URL includes `Blog-Project` in the path
- Clear browser cache

### Problem: Images not showing
**Solution:**
- Add placeholder images to `public/images/` folder
- Or the page will show broken image icons (still works)

### Problem: CSS not loading
**Solution:**
- Check that the file path is correct in the URL
- Clear browser cache (Ctrl + Shift + Delete)
- Check browser console for errors (F12)

### Problem: "Access denied" to admin dashboard
**Solution:**
- This is normal - admin check is in place
- To test admin features, comment out the check in `app/views/admin-dashboard.php`:
```php
// Comment these lines temporarily:
// if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
//     header('Location: index.php?page=home');
//     exit();
// }
```

## 🔧 Backend Implementation (Next Steps)

This is a **front-end template**. To make it fully functional:

1. **Create Controllers** in `app/controllers/`:
   - `AuthController.php` - Handle login/register
   - `ArticleController.php` - Handle article CRUD
   - `UserController.php` - Handle user profiles
   - `AdminController.php` - Handle admin functions

2. **Implement Authentication**:
   - Hash passwords with `password_hash()`
   - Verify with `password_verify()`
   - Set session variables on login

3. **Connect Forms to Backend**:
   - Change form actions to point to controllers
   - Process form data with PHP
   - Validate input server-side

4. **Add Dynamic Content**:
   - Query database for articles
   - Display user-specific content
   - Implement search functionality

## 📚 Learning Resources

- **PHP:** https://www.php.net/manual/en/
- **MySQL:** https://dev.mysql.com/doc/
- **PDO Tutorial:** https://www.php.net/manual/en/book.pdo.php
- **Sessions:** https://www.php.net/manual/en/book.session.php

## ✅ Checklist

- [ ] XAMPP installed and running
- [ ] Project copied to htdocs
- [ ] Accessed http://localhost/Blog-Project/
- [ ] Database created automatically
- [ ] Placeholder images added (optional)
- [ ] All pages loading correctly
- [ ] Ready to implement backend!

---

**Need Help?**
- Check error logs: `C:\xampp\apache\logs\error.log`
- Enable PHP errors in development (already done in XAMPP)
- Check browser console for JavaScript errors (F12)

**Enjoy building your UGC Platform! 🎉**
