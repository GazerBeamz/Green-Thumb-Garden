<aside class="sidebar">
    <div class="logo-container text-center py-4">
        <img src="../assets/images/logo.png" alt="Logo" class="logo-img">
        <h4 class="logo-text mt-2">Admin Dashboard</h4>
    </div>
    <nav>
        <ul class="nav flex-column">
            <li><a href="admin_dashboard.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'admin_dashboard.php' ? 'active' : ''; ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="manage_users.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'manage_users.php' ? 'active' : ''; ?>"><i class="fas fa-user-friends"></i> Manage Users</a></li>
            <li><a href="#actions" class="nav-link"><i class="fas fa-tasks"></i> Actions</a></li>
        </ul>
    </nav>
</aside>