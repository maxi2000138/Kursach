<nav class="navbar">
    <div class="nav-container">
        <a href="/" class="nav-logo">User Management</a>
        <ul class="nav-menu">
            <li><a href="/profile">Профиль</a></li>
            <?php if (hasRole('admin')): ?>
                <li><a href="/admin">Админ-панель</a></li>
            <?php endif; ?>
            <li><a href="/logout">Выход</a></li>
        </ul>
    </div>
</nav>


