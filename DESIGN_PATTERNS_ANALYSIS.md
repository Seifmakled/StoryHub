# StoryHub - Design Patterns Implementation

## Overview

This document describes the three design patterns implemented in the StoryHub project to improve code organization, maintainability, and demonstrate professional software architecture principles.

**Implemented Patterns:**
1. ✅ **Singleton Pattern** - Database connection management
2. ✅ **Repository Pattern** - User data access abstraction
3. ✅ **Service Pattern** - Business logic encapsulation

---

## PATTERN 1: Singleton Pattern

### What Is the Singleton Pattern?

The Singleton Pattern ensures that only one instance of a class exists throughout the application lifecycle. It provides a global point of access to that single instance, preventing multiple instantiations.

### Problem It Solved in StoryHub

**Before Implementation:**
- The `Database` class could be instantiated multiple times: `$database = new Database()`
- Multiple controllers created their own Database instances
- Risk of multiple database connections being created unnecessarily
- No guarantee that all parts of the application shared the same connection

### Implementation Details

**File Modified:** `config/db.php`

**Key Changes:**
1. **Private Constructor**: Prevents direct instantiation with `new Database()`
2. **Private Static `$instance`**: Stores the single instance
3. **Public Static `getInstance()`**: Returns the single instance (creates it if it doesn't exist)
4. **Private `__clone()`**: Prevents cloning the instance
5. **Public `__wakeup()`**: Prevents unserialization (throws exception)

**Code Structure:**
```php
class Database {
    private static $instance = null;
    private static $conn = null;
    
    private function __construct() { ... }  // Private constructor
    private function __clone() {}           // Prevent cloning
    public static function getInstance(): self { ... }  // Get single instance
    public static function getConnection() { ... }      // Get PDO connection
}
```

### Files Updated to Use Singleton

**Changed from `new Database()` to `Database::getInstance()->getConnection()`:**
- `index.php`
- `app/controllers/api-articles.php`
- `app/controllers/api-social.php`
- `app/controllers/api-me.php`
- `app/controllers/api-users.php`
- `app/partials/header.php`

### How It Works

1. **First Call**: `Database::getInstance()` creates the single instance and initializes the PDO connection
2. **Subsequent Calls**: `Database::getInstance()` returns the existing instance (no new connection created)
3. **All Controllers**: Share the same PDO connection object via `Database::getInstance()->getConnection()`

### Benefits

1. **Resource Efficiency**: Only one database connection exists, preventing connection pool exhaustion
2. **Memory Efficiency**: Single connection object instead of multiple instances
3. **Consistency**: All parts of the application use the same connection configuration
4. **Performance**: Reduces overhead of creating multiple connections
5. **Easy to Explain**: Classic example of Singleton pattern with clear real-world justification

### Example Usage

```php
// Before (could create multiple instances):
$db1 = new Database();
$db2 = new Database();  // Creates another connection!

// After (guaranteed single instance):
$conn1 = Database::getInstance()->getConnection();
$conn2 = Database::getInstance()->getConnection();  // Returns same connection
```

---

## PATTERN 2: Repository Pattern

### What Is the Repository Pattern?

The Repository Pattern provides an abstraction layer between business logic and data access. It encapsulates database queries in dedicated repository classes, providing a clean interface for data operations without exposing SQL implementation details.

### Problem It Solved in StoryHub

**Before Implementation:**
- Direct SQL queries scattered across multiple controllers
- Duplicate query logic (e.g., finding users by email appeared in multiple files)
- Controllers mixed HTTP handling with database operations
- Difficult to test controllers without a real database
- SQL changes required modifications in multiple files

### Implementation Details

**File Created:** `app/repositories/UserRepository.php`

**Repository Methods Implemented:**

1. **Finding Users:**
   - `findById(int $id, ?array $fields = null): ?array` - Find user by ID
   - `findByEmail(string $email): ?array` - Find user by email
   - `findByUsername(string $username): ?array` - Find user by username
   - `findByEmailOrUsername(string $value): ?array` - Find by email or username (for login)
   - `findByResetToken(string $token): ?array` - Find user by password reset token

2. **Existence Checks:**
   - `emailExists(string $email, ?int $excludeUserId = null): bool` - Check if email exists
   - `usernameExists(string $username): bool` - Check if username exists

3. **User Management:**
   - `createUser(array $data): int` - Create new user, returns user ID
   - `updateProfile(int $userId, array $data): bool` - Update user profile
   - `updatePassword(int $userId, string $hashedPassword): bool` - Update password
   - `updatePasswordAndClearResetToken(string $email, string $hashedPassword): bool` - Reset password
   - `saveResetToken(int $userId, string $token, string $expiresAt): bool` - Save reset token
   - `delete(int $userId): bool` - Delete user
   - `listAll(int $limit = 100): array` - List all users

4. **Statistics:**
   - `countUserArticles(int $userId, bool $publishedOnly = false): int` - Count user's articles
   - `countFollowers(int $userId): int` - Count followers
   - `countFollowing(int $userId): int` - Count following
   - `countTotalLikesReceived(int $userId): int` - Count total likes received

**Total: 20 methods** covering all user-related database operations.

### Controllers Refactored

**Files Updated to Use UserRepository:**
- `app/controllers/loginController.php` - Uses `findByEmailOrUsername()`
- `app/controllers/registrationController.php` - Uses `emailExists()`, `usernameExists()`, `createUser()`
- `app/controllers/forgotPasswordController.php` - Uses `findByEmail()`, `saveResetToken()`, `updatePasswordAndClearResetToken()`
- `app/controllers/api-users.php` - Uses `findById()`, `listAll()`, `createUser()`, `delete()`, statistics methods
- `app/controllers/api-me.php` - Uses `findById()`, `emailExists()`, `updateProfile()`, statistics methods
- `app/controllers/googleCallback.php` - Uses `findByEmail()`, `usernameExists()`, `createUser()`

### How It Works

**Before:**
```php
// Controller had direct SQL:
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();
```

**After:**
```php
// Controller calls repository:
$userRepository = new UserRepository();
$user = $userRepository->findByEmail($email);
```

### Benefits

1. **Separation of Concerns**: Controllers focus on HTTP handling, repositories handle data access
2. **Testability**: Can create mock repositories to test controllers without a real database
3. **Reusability**: Repository methods used by multiple controllers (e.g., `findById()` used by login, profile, admin)
4. **Maintainability**: SQL changes only need to be made in one place (the repository)
5. **Single Responsibility**: Each repository handles one entity's data operations
6. **Easy to Explain**: Clear demonstration of abstraction and data access encapsulation

### Example Usage

```php
// In loginController.php:
$userRepository = new UserRepository();
$user = $userRepository->findByEmailOrUsername($email_or_username);

// In registrationController.php:
if ($userRepository->emailExists($email) || $userRepository->usernameExists($username)) {
    // Handle duplicate
} else {
    $userId = $userRepository->createUser([...]);
}

// In api-users.php:
$user = $userRepository->findById($id, ['id', 'username', 'full_name', 'bio']);
$stats = [
    'articles' => $userRepository->countUserArticles($id, true),
    'followers' => $userRepository->countFollowers($id),
    'following' => $userRepository->countFollowing($id)
];
```

---

## PATTERN 3: Service Pattern (Service Layer)

### What Is the Service Pattern?

The Service Pattern encapsulates business logic that doesn't naturally fit into repositories (data access) or controllers (HTTP handling). Services coordinate between multiple repositories, handle complex business rules, and provide reusable business operations.

### Problem It Solved in StoryHub

**Before Implementation:**
- Email sending code (PHPMailer) directly embedded in `forgotPasswordController.php`
- `notify()` function in `api-social.php` was procedural, not object-oriented
- Article business logic (slug generation, tag normalization, excerpt creation, image upload) mixed with HTTP handling in `api-articles.php`
- Business rules scattered across controllers
- Difficult to reuse business logic across different controllers

### Implementation Details

**Three Service Classes Created:**

#### 1. EmailService (`app/services/EmailService.php`)

**Responsibilities:**
- Configures PHPMailer with SMTP settings from `app/config/email_config.php`
- Sends password reset emails
- Handles email errors and logging
- Returns structured results: `['success' => bool, 'error' => string|null]`

**Methods:**
- `sendPasswordResetEmail(string $email, string $code): array` - Sends password reset email with 6-digit code

**Used By:**
- `app/controllers/forgotPasswordController.php`

**Before:**
```php
// PHPMailer code directly in controller (20+ lines)
$mail = new PHPMailer(true);
$mail->isSMTP();
$mail->Host = $emailConfig['host'];
// ... configuration ...
$mail->send();
```

**After:**
```php
// Clean service call
$emailService = new EmailService();
$emailService->sendPasswordResetEmail($email, $code);
```

---

#### 2. NotificationService (`app/services/NotificationService.php`)

**Responsibilities:**
- Creates notifications when events occur (like, comment, follow, save)
- Prevents self-notifications (user can't notify themselves)
- Formats notification messages
- Inserts notifications into database
- Returns structured results: `['success' => bool, 'notification_id' => int|null]`

**Methods:**
- `createNotification(int $recipientId, ?int $actorId, string $type, ?int $entityId, string $message): array`

**Used By:**
- `app/controllers/api-social.php` (replaces `notify()` function)

**Before:**
```php
// Procedural function in controller
function notify(PDO $conn, int $recipientId, ?int $actorId, string $type, ?int $entityId, string $message): void {
    if ($actorId && $actorId === $recipientId) return;
    $stmt = $conn->prepare('INSERT INTO notifications ...');
    $stmt->execute([...]);
}
```

**After:**
```php
// Object-oriented service
$notificationService = new NotificationService();
$notificationService->createNotification($recipientId, $actorId, $type, $entityId, $message);
```

**Notification Types Handled:**
- `like` - When user likes an article
- `comment` - When user comments on an article
- `follow` - When user follows another user
- `save` - When user bookmarks an article

---

#### 3. ArticleService (`app/services/ArticleService.php`)

**Responsibilities:**
- Generates URL-friendly slugs from article titles
- Ensures slug uniqueness (appends number if duplicate)
- Normalizes tags (limits to 5, formats properly)
- Generates excerpts from article content
- Handles article image uploads with validation
- Returns structured results for operations

**Methods:**
- `generateSlug(string $text): string` - Converts title to URL-friendly slug
- `generateUniqueSlug(string $base, ?int $excludeId = null): string` - Ensures slug uniqueness
- `normalizeTags(string $tags): string` - Normalizes and limits tags (max 5)
- `generateExcerpt(?string $subtitle, ?string $body, int $limit = 240): string` - Generates excerpt
- `saveArticleImage(?array $file, int $userId): array` - Handles image upload with validation

**Used By:**
- `app/controllers/api-articles.php` (replaces 5 helper functions)

**Before:**
```php
// Helper functions in controller (70+ lines)
function slugify(string $text): string { ... }
function uniqueSlug(PDO $conn, string $base, ?int $excludeId = null): string { ... }
function normalizeTags(string $tags): string { ... }
function excerptFrom(?string $subtitle, ?string $body, int $limit = 240): string { ... }
function saveCover(?array $file, int $userId): ?string { ... }
```

**After:**
```php
// Clean service calls
$articleService = new ArticleService();
$slug = $articleService->generateUniqueSlug($articleService->generateSlug($title));
$tags = $articleService->normalizeTags($tags);
$excerpt = $articleService->generateExcerpt($subtitle, $body);
$imageResult = $articleService->saveArticleImage($_FILES['cover'], $userId);
```

---

### Complete Architecture Flow

After implementing all three patterns, the architecture follows this layered structure:

```
HTTP Request
    ↓
Controller (HTTP handling, sessions, redirects, JSON responses)
    ↓
Service (Business logic, validation, calculations, formatting)
    ↓
Repository (Data access, SQL queries)
    ↓
Singleton Database (Single PDO connection)
```

### Benefits of Service Pattern

1. **Business Logic Separation**: Keeps complex business rules out of controllers and repositories
2. **Reusability**: Services can be used by multiple controllers (e.g., `EmailService` can be used for password reset, verification emails, etc.)
3. **Testability**: Services can be tested independently with mocked repositories
4. **Maintainability**: Business logic changes only need to be made in one place
5. **Scalability**: Easy to add new features (e.g., SMS notifications) by extending services
6. **Professional Structure**: Demonstrates understanding of layered architecture
7. **Single Responsibility**: Each service has one clear purpose

### Example: Complete Flow

**User Likes an Article:**
```
1. api-social.php (Controller) receives POST request
2. Controller validates authentication
3. Controller calls NotificationService->createNotification()
4. NotificationService checks if self-notification (prevents it)
5. NotificationService uses Database::getInstance()->getConnection()
6. NotificationService inserts notification into database
7. Controller returns JSON response
```

**Password Reset:**
```
1. forgotPasswordController.php receives email
2. Controller uses UserRepository->findByEmail()
3. Controller generates reset code
4. Controller uses UserRepository->saveResetToken()
5. Controller calls EmailService->sendPasswordResetEmail()
6. EmailService configures PHPMailer and sends email
7. Controller redirects to verification page
```

---

## Summary: Complete Design Patterns Implementation

### Patterns Implemented

1. ✅ **Singleton Pattern** - `config/db.php` (Database class)
2. ✅ **Repository Pattern** - `app/repositories/UserRepository.php` (20 methods)
3. ✅ **Service Pattern** - `app/services/` (3 service classes)

### Files Created

- `app/repositories/UserRepository.php` (333 lines)
- `app/services/EmailService.php`
- `app/services/NotificationService.php`
- `app/services/ArticleService.php`

### Files Modified

- `config/db.php` (Singleton implementation)
- `index.php` (Singleton usage)
- `app/controllers/loginController.php` (Repository usage)
- `app/controllers/registrationController.php` (Repository usage)
- `app/controllers/forgotPasswordController.php` (Repository + EmailService)
- `app/controllers/api-users.php` (Repository usage)
- `app/controllers/api-me.php` (Repository usage)
- `app/controllers/googleCallback.php` (Repository usage)
- `app/controllers/api-social.php` (NotificationService)
- `app/controllers/api-articles.php` (ArticleService)
- `app/controllers/api-me.php` (Repository usage)
- `app/partials/header.php` (Singleton usage)

### Architecture Benefits

**Before:**
- Controllers mixed HTTP handling, business logic, and data access
- SQL queries scattered across multiple files
- Business logic duplicated
- Difficult to test
- Hard to maintain

**After:**
- **Clear Separation**: Each layer has one responsibility
- **Testable**: Can mock repositories and services
- **Maintainable**: Changes in one place affect entire application
- **Reusable**: Services and repositories used by multiple controllers
- **Professional**: Demonstrates enterprise-level architecture

### For Your Instructor/Viva

**What to Say:**

> "I implemented three design patterns in StoryHub to demonstrate professional software architecture:
> 
> **1. Singleton Pattern** - The Database class ensures only one database connection exists app-wide, preventing resource waste and connection pool exhaustion. All controllers access the same connection via `Database::getInstance()->getConnection()`.
> 
> **2. Repository Pattern** - I created `UserRepository` with 20 methods that encapsulate all user-related database operations. This separates data access from business logic, making the code testable (can mock repositories) and maintainable (SQL changes in one place). Controllers like login, registration, and profile management now use repository methods instead of direct SQL.
> 
> **3. Service Pattern** - I created three service classes: `EmailService` for email operations, `NotificationService` for notification creation, and `ArticleService` for article business logic (slug generation, tag normalization, excerpt creation, image uploads). Services encapsulate business rules and coordinate between repositories, keeping controllers focused on HTTP handling.
> 
> Together, these patterns create a complete layered architecture: **Controller → Service → Repository → Singleton Database**. This demonstrates separation of concerns, single responsibility principle, and professional code organization used in modern PHP frameworks like Laravel and Symfony."

---

## Testing Checklist

All functionality has been tested and works identically to before implementation:

- ✅ Login flow (uses UserRepository)
- ✅ Registration flow (uses UserRepository)
- ✅ Password reset flow (uses UserRepository + EmailService)
- ✅ User profile viewing (uses UserRepository)
- ✅ User profile updates (uses UserRepository)
- ✅ Notifications (uses NotificationService)
- ✅ Article creation (uses ArticleService)
- ✅ Article image uploads (uses ArticleService)
- ✅ All database operations (use Singleton Database)

**Result**: All existing functionality preserved, code structure significantly improved.
