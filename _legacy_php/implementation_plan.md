# SVI Expanded System Plan

## New Files
- setup.sql (UPDATE - add admin users, settings tables)
- .htaccess (protect db.php + admin)
- admin/auth.php (session middleware)
- admin/login.php (login page)
- admin/logout.php
- admin/index.php (dashboard)
- admin/blocks.php (CRUD blocked IPs)
- admin/content.php (edit all text)
- admin/settings.php (site settings)
- admin/ajax_handler.php (all admin AJAX)

## Settings to store
- allow_deblock, deblock_cooldown_hours, max_check_attempts
- maintenance_mode, admin_email
- session_timeout_minutes, login_max_attempts, login_lockout_minutes
- site_notice (banner on main page)
- show_ip_tool (toggle IP form on main page)
