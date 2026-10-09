<?php
// ── admin/_nav.php ────────────────────────────────────────────
// Shared navbar + sidebar for all admin pages.
// Requires $activePage variable to be set before including.
$activePage = $activePage ?? '';
?>
<!-- ── Top Navbar ─────────────────────────────────────────────── -->
<nav class="admin-nav">
  <a class="brand" href="/secure-svi-portal/index.php">
    <img src="/app/img/eh_logo_web44.png" alt="Erickson Holding" style="height:32px; object-fit:contain; filter: invert(1);">
    <span class="brand-divider" style="margin-left: 16px;"></span>
    <span class="brand-svi" style="margin-left: 16px;">
      <span class="brand-svi-title">SVI</span>
      <span class="brand-svi-sub">Security and verification inspector</span>
    </span>
  </a>
  <div class="admin-nav-right">
    <span class="admin-badge">Admin Panel</span>
    <span class="admin-user">
      <?= htmlspecialchars(svi_admin_name(), ENT_QUOTES) ?>
    </span>
    <a class="nav-logout" href="logout.php">Sign out</a>
  </div>
</nav>

<div class="admin-layout">
  <!-- ── Sidebar ─────────────────────────────────────────────── -->
  <aside class="sidebar">
    <div class="sidebar-section">
      <div class="sidebar-label">Overview</div>
      <a href="index.php" class="<?= $activePage === 'dashboard' ? 'active' : '' ?>">
        <span class="sidebar-icon">📊</span> Dashboard
      </a>
    </div>

    <div class="sidebar-section">
      <div class="sidebar-label">Security</div>
      <a href="blocks.php" class="<?= $activePage === 'blocks' ? 'active' : '' ?>">
        <span class="sidebar-icon">🚫</span> Blocked IPs
      </a>
      <a href="login_log.php" class="<?= $activePage === 'login_log' ? 'active' : '' ?>">
        <span class="sidebar-icon">🔐</span> Login Audit
      </a>
      <a href="check_log.php" class="<?= $activePage === 'check_log' ? 'active' : '' ?>">
        <span class="sidebar-icon">🔍</span> Check Log
      </a>
    </div>

    <div class="sidebar-section">
      <div class="sidebar-label">Content</div>
      <a href="content.php" class="<?= $activePage === 'content' ? 'active' : '' ?>">
        <span class="sidebar-icon">✏️</span> Page Text
      </a>
    </div>

    <div class="sidebar-section">
      <div class="sidebar-label">Configuration</div>
      <a href="settings.php" class="<?= $activePage === 'settings' ? 'active' : '' ?>">
        <span class="sidebar-icon">⚙️</span> Site Settings
      </a>
      <a href="account.php" class="<?= $activePage === 'account' ? 'active' : '' ?>">
        <span class="sidebar-icon">👤</span> My Account
      </a>
    </div>

    <div class="sidebar-section">
      <div class="sidebar-label">Public Site</div>
      <a href="../index.php" target="_blank">
        <span class="sidebar-icon">🌐</span> View Public Page ↗
      </a>
    </div>
  </aside>

  <!-- ── Page content is injected here ──────────────────────── -->
  <main class="admin-content">
