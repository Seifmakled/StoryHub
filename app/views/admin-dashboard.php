<?php
/*
// Check if user is admin
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: index.php?url=landing');
    exit();
}
 */
$pageTitle = 'Admin Dashboard - StoryHub';
$pageDescription = 'Admin dashboard';
$pageCSS = 'admin-dashboard.css';
$pageJS = 'admin-dashboard.js';

include '../partials/header.php';
?>

<!-- Chart.js for Admin Dashboard -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<div class="admin-wrapper">
    <!-- Admin Sidebar -->
    <aside class="admin-sidebar">
        <div class="sidebar-header">
            <div class="brand-logo">
                <i class="fas fa-feather-alt"></i>
                <span>StoryHub</span>
            </div>
            <span class="admin-badge">ADMIN</span>
        </div>

        <nav class="sidebar-nav">
            <a href="#dashboard" class="nav-item active">
                <i class="fas fa-chart-line"></i>
                <span>Dashboard</span>
            </a>
            <a href="#articles" class="nav-item">
                <i class="fas fa-newspaper"></i>
                <span>Articles</span>
                <span class="badge">142</span>
            </a>
            <a href="#users" class="nav-item">
                <i class="fas fa-users"></i>
                <span>Users</span>
                <span class="badge">1.2K</span>
            </a>
            <a href="#comments" class="nav-item">
                <i class="fas fa-comments"></i>
                <span>Comments</span>
                <span class="badge">456</span>
            </a>
            <a href="#categories" class="nav-item">
                <i class="fas fa-tags"></i>
                <span>Categories</span>
            </a>
            <a href="#reports" class="nav-item">
                <i class="fas fa-flag"></i>
                <span>Reports</span>
                <span class="badge alert">12</span>
            </a>
            <a href="#analytics" class="nav-item">
                <i class="fas fa-chart-bar"></i>
                <span>Analytics</span>
            </a>
            <a href="#settings" class="nav-item">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <a href="index.php?url=landing" class="nav-item">
                <i class="fas fa-home"></i>
                <span>Back to Site</span>
            </a>
            <a href="index.php?url=logout" class="nav-item">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <!-- Admin Content -->
    <main class="admin-content">
        <!-- Top Bar -->
        <header class="admin-header">
            <div class="header-left">
                <button class="btn-toggle-sidebar" id="toggleSidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <h1>Dashboard Overview</h1>
            </div>

            <div class="header-right">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search...">
                </div>

                <button class="icon-btn" title="Notifications">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">5</span>
                </button>

                <div class="admin-user">
                    <img src="public/images/default-avatar.jpg" alt="Admin">
                    <span>Admin User</span>
                </div>
            </div>
        </header>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-label">Total Users</span>
                    <h3 class="stat-value">1,234</h3>
                    <span class="stat-change positive">
                        <i class="fas fa-arrow-up"></i> 12% from last month
                    </span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <i class="fas fa-newspaper"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-label">Total Articles</span>
                    <h3 class="stat-value">5,678</h3>
                    <span class="stat-change positive">
                        <i class="fas fa-arrow-up"></i> 8% from last month
                    </span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <i class="fas fa-eye"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-label">Total Views</span>
                    <h3 class="stat-value">123.5K</h3>
                    <span class="stat-change positive">
                        <i class="fas fa-arrow-up"></i> 23% from last month
                    </span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                    <i class="fas fa-comments"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-label">Total Comments</span>
                    <h3 class="stat-value">8,456</h3>
                    <span class="stat-change negative">
                        <i class="fas fa-arrow-down"></i> 3% from last month
                    </span>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="charts-grid">
            <div class="chart-card">
                <div class="card-header">
                    <h3><i class="fas fa-chart-line"></i> Traffic Overview</h3>
                    <select class="select-period">
                        <option>Last 7 days</option>
                        <option>Last 30 days</option>
                        <option>Last 3 months</option>
                        <option>Last year</option>
                    </select>
                </div>
                <div class="chart-body">
                    <canvas id="trafficChart"></canvas>
                </div>
            </div>

            <div class="chart-card">
                <div class="card-header">
                    <h3><i class="fas fa-chart-pie"></i> Category Distribution</h3>
                </div>
                <div class="chart-body">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Tables Section -->
        <div class="tables-grid">
            <!-- Recent Articles -->
            <div class="table-card">
                <div class="card-header">
                    <h3><i class="fas fa-newspaper"></i> Recent Articles</h3>
                    <a href="#articles" class="btn btn-sm">View All</a>
                </div>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Views</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                            <tr>
                                <td>
                                    <div class="table-title">
                                        <img src="public/images/article-placeholder.jpg" alt="">
                                        <span>Article Title Goes Here</span>
                                    </div>
                                </td>
                                <td>John Doe</td>
                                <td><span class="badge-cat">Technology</span></td>
                                <td><span class="badge-status published">Published</span></td>
                                <td>1,234</td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-icon" title="View"><i class="fas fa-eye"></i></button>
                                        <button class="btn-icon" title="Edit"><i class="fas fa-edit"></i></button>
                                        <button class="btn-icon" title="Delete"><i class="fas fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Users -->
            <div class="table-card">
                <div class="card-header">
                    <h3><i class="fas fa-users"></i> Recent Users</h3>
                    <a href="#users" class="btn btn-sm">View All</a>
                </div>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Email</th>
                                <th>Joined</th>
                                <th>Articles</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                            <tr>
                                <td>
                                    <div class="table-user">
                                        <img src="public/images/default-avatar.jpg" alt="">
                                        <span>User Name</span>
                                    </div>
                                </td>
                                <td>user@example.com</td>
                                <td>May 15, 2024</td>
                                <td>12</td>
                                <td><span class="badge-status active">Active</span></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-icon" title="View"><i class="fas fa-eye"></i></button>
                                        <button class="btn-icon" title="Edit"><i class="fas fa-edit"></i></button>
                                        <button class="btn-icon" title="Ban"><i class="fas fa-ban"></i></button>
                                    </div>
                                </td>
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<?php include '../partials/footer.php'; ?>
