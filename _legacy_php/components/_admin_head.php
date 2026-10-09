<?php
// ── admin/_head.php ───────────────────────────────────────────
// Shared HTML <head> + CSS for all admin pages.
// Usage: include __DIR__ . '/../../components/_admin_head.php'; — pass $pageTitle before including.

$pageTitle = $pageTitle ?? 'SVI Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>EH.SVI | <?= htmlspecialchars($pageTitle, ENT_QUOTES) ?></title>
<meta name="robots" content="noindex, nofollow">
<link rel="icon" href="/app/img/ehltd_eh.png">
<link rel="stylesheet" href="/ehltd.css">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Source+Serif+4:wght@300;400;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --eh-purple:      #4C2D8A;
    --eh-purple-d:    #3a1f6e;
    --svi-navy:       #2E3A59;
    --svi-navy-light: #3D4E6B;
    --svi-navy-xl:    #4e6080;
    --body-text:      #1a1a2e;
    --muted:          #5a6070;
    --border:         #d0d4dc;
    --bg:             #f3f4f7;
    --bg-white:       #ffffff;
    --card-bg:        #ffffff;
    --input-bg:       #ecedf0;

    --soft-bg:        #fff4e0;
    --soft-col:       #7a4600;
    --perm-bg:        #fceaea;
    --perm-col:       #7a1515;
    --ok-bg:          #eaf6ef;
    --ok-col:         #1a6b3a;
    --info-bg:        #eaf0fb;
    --info-col:       #1a3a6b;

    --nav-h: 64px;
    --sidebar-w: 220px;
  }

  body {
    font-family: 'Source Serif 4', Georgia, serif;
    background: var(--bg);
    color: var(--body-text);
    min-height: 100vh;
    display: flex;
    flex-direction: column;
  }

  /* ── Top navbar ─────────────────────────────────────────────── */
  .admin-nav {
    height: var(--nav-h);
    background: var(--bg-white);
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    padding: 0 28px;
    gap: 20px;
    position: fixed;
    top: 0; left: 0; right: 0;
    z-index: 100;
    background-image: url('../data:image/jpeg;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
  }

  .admin-nav .brand {
    display: flex; align-items: center; gap: 14px;
    text-decoration: none; flex-shrink: 0;
  }
  .brand-eh {
    font-family: 'Playfair Display', serif;
    font-weight: 700; font-size: 14px; line-height: 1.2;
    color: var(--eh-purple);
  }
  .brand-divider { width:1px; height:36px; background:#b0b4c0; }
  .brand-svi { display:flex; flex-direction:column; gap:1px; }
  .brand-svi-title {
    font-family: 'Playfair Display', serif;
    font-size: 22px; color: var(--svi-navy); line-height:1;
  }
  .brand-svi-sub {
    font-size: 10px; color: var(--svi-navy-light);
    letter-spacing: 0.06em; font-weight: 300;
  }

  .admin-nav-right {
    margin-left: auto;
    display: flex; align-items: center; gap: 16px;
  }
  .admin-badge {
    background: var(--eh-purple);
    color: #fff;
    font-size: 11px;
    font-weight: 600;
    padding: 3px 10px;
    border-radius: 20px;
    letter-spacing: 0.04em;
    font-family: 'Source Serif 4', serif;
  }
  .admin-user {
    font-size: 13px; color: var(--muted); font-weight: 300;
  }
  .nav-logout {
    font-size: 12px; color: var(--perm-col); text-decoration: none;
    border: 1px solid currentColor; padding: 4px 12px; border-radius: 5px;
    transition: background 0.15s, color 0.15s;
  }
  .nav-logout:hover { background: var(--perm-col); color:#fff; }

  /* ── Layout ─────────────────────────────────────────────────── */
  .admin-layout {
    display: flex;
    flex: 1;
    margin-top: var(--nav-h);
  }

  /* ── Sidebar ─────────────────────────────────────────────────── */
  .sidebar {
    width: var(--sidebar-w);
    background: var(--bg-white);
    border-right: 1px solid var(--border);
    padding: 28px 0;
    position: fixed;
    top: var(--nav-h); left: 0; bottom: 0;
    overflow-y: auto;
  }

  .sidebar-section {
    margin-bottom: 24px;
  }
  .sidebar-label {
    font-size: 10px; font-weight: 600; letter-spacing: 0.1em;
    color: var(--muted); text-transform: uppercase;
    padding: 0 20px 8px;
  }

  .sidebar a {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 20px;
    font-size: 14px; font-weight: 300; color: var(--body-text);
    text-decoration: none;
    border-right: 3px solid transparent;
    transition: background 0.15s, color 0.15s, border-color 0.15s;
  }
  .sidebar a:hover { background: var(--bg); }
  .sidebar a.active {
    background: var(--info-bg);
    color: var(--svi-navy);
    border-right-color: var(--svi-navy);
    font-weight: 600;
  }
  .sidebar-icon { font-size: 16px; line-height:1; }

  /* ── Page content ────────────────────────────────────────────── */
  .admin-content {
    margin-left: var(--sidebar-w);
    flex: 1;
    padding: 36px 36px 80px;
    min-width: 0;
  }

  /* ── Page heading ────────────────────────────────────────────── */
  .page-heading {
    margin-bottom: 28px;
  }
  .page-heading h1 {
    font-family: 'Playfair Display', serif;
    font-size: 28px; font-weight: 400; color: var(--svi-navy);
    line-height: 1.1;
  }
  .page-heading p {
    font-size: 14px; color: var(--muted); font-weight: 300; margin-top: 4px;
  }

  /* ── Cards ───────────────────────────────────────────────────── */
  .card {
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 24px;
    margin-bottom: 24px;
  }
  .card-title {
    font-family: 'Playfair Display', serif;
    font-size: 18px; font-weight: 400; color: var(--svi-navy);
    margin-bottom: 16px;
  }

  /* ── Stat cards row ──────────────────────────────────────────── */
  .stat-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: 16px;
    margin-bottom: 28px;
  }
  .stat-card {
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 20px 20px 16px;
    display: flex; flex-direction: column; gap: 6px;
  }
  .stat-card .stat-label {
    font-size: 11px; color: var(--muted); letter-spacing: 0.06em;
    text-transform: uppercase; font-weight: 600;
  }
  .stat-card .stat-value {
    font-family: 'Playfair Display', serif;
    font-size: 36px; color: var(--svi-navy); line-height: 1;
  }
  .stat-card.soft  .stat-value { color: var(--soft-col); }
  .stat-card.perm  .stat-value { color: var(--perm-col); }
  .stat-card.ok    .stat-value { color: var(--ok-col); }
  .stat-card.info  .stat-value { color: var(--info-col); }

  /* ── Table ───────────────────────────────────────────────────── */
  .tbl-wrap { overflow-x: auto; }
  table.svi-table {
    width: 100%; border-collapse: collapse; font-size: 14px;
  }
  .svi-table th {
    background: var(--bg); border-bottom: 2px solid var(--border);
    padding: 10px 14px; text-align: left; font-size: 11px;
    font-weight: 600; letter-spacing: 0.07em; text-transform: uppercase;
    color: var(--muted);
  }
  .svi-table td {
    padding: 12px 14px; border-bottom: 1px solid var(--border);
    vertical-align: middle; font-weight: 300;
  }
  .svi-table tr:last-child td { border-bottom: none; }
  .svi-table tr:hover td { background: #f8f9fb; }

  /* ── Badges ──────────────────────────────────────────────────── */
  .badge {
    display: inline-block; padding: 3px 10px; border-radius: 20px;
    font-size: 11px; font-weight: 600; letter-spacing: 0.04em;
  }
  .badge-soft     { background: var(--soft-bg);  color: var(--soft-col); }
  .badge-perm     { background: var(--perm-bg);  color: var(--perm-col); }
  .badge-ok       { background: var(--ok-bg);    color: var(--ok-col); }
  .badge-info     { background: var(--info-bg);  color: var(--info-col); }
  .badge-muted    { background: var(--bg);       color: var(--muted); }

  /* ── Forms ───────────────────────────────────────────────────── */
  .form-group { margin-bottom: 18px; }
  .form-group label {
    display: block; font-size: 13px; font-weight: 600;
    color: var(--svi-navy); margin-bottom: 6px;
  }
  .form-group .form-desc {
    font-size: 12px; color: var(--muted); margin-bottom: 6px; font-weight: 300;
  }
  .form-input, .form-select, .form-textarea {
    width: 100%; background: var(--input-bg);
    border: 1px solid transparent; border-radius: 6px;
    padding: 11px 14px;
    font-family: 'Source Serif 4', Georgia, serif;
    font-size: 14px; color: var(--body-text);
    outline: none; transition: background 0.15s, border-color 0.15s;
  }
  .form-input:focus, .form-select:focus, .form-textarea:focus {
    background: #e2e4e8; border-color: var(--svi-navy-light);
  }
  .form-textarea { min-height: 90px; resize: vertical; }
  .form-input.mono { font-family: 'JetBrains Mono', monospace; font-size: 13px; }

  .toggle-wrap {
    display: flex; align-items: center; gap: 12px;
  }
  .toggle {
    position: relative; display: inline-block;
    width: 42px; height: 24px; flex-shrink: 0;
  }
  .toggle input { opacity:0; width:0; height:0; }
  .slider {
    position: absolute; inset:0;
    background: var(--border); border-radius: 24px;
    cursor: pointer; transition: background 0.2s;
  }
  .slider::before {
    content:''; position:absolute;
    width:18px; height:18px; border-radius:50%;
    left:3px; top:3px; background:#fff;
    transition: transform 0.2s;
  }
  .toggle input:checked + .slider { background: var(--svi-navy); }
  .toggle input:checked + .slider::before { transform: translateX(18px); }

  /* ── Buttons ─────────────────────────────────────────────────── */
  .btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 9px 20px; border-radius: 6px; border: none;
    font-family: 'Source Serif 4', Georgia, serif;
    font-size: 13px; font-weight: 600; cursor: pointer;
    text-decoration: none; transition: opacity 0.15s, transform 0.1s;
  }
  .btn:active { transform: scale(0.97); }
  .btn:disabled { opacity: 0.5; cursor: not-allowed; }
  .btn-primary   { background: var(--svi-navy);  color:#fff; }
  .btn-primary:hover { opacity:0.87; }
  .btn-danger    { background: var(--perm-col);  color:#fff; }
  .btn-danger:hover { opacity:0.87; }
  .btn-warn      { background: var(--soft-col);  color:#fff; }
  .btn-warn:hover { opacity:0.87; }
  .btn-ok        { background: var(--ok-col);    color:#fff; }
  .btn-ok:hover { opacity:0.87; }
  .btn-ghost {
    background: transparent; color: var(--svi-navy);
    border: 1px solid var(--border);
  }
  .btn-ghost:hover { background: var(--bg); }
  .btn-sm { padding: 5px 12px; font-size: 12px; }

  /* ── Alert box ───────────────────────────────────────────────── */
  .alert {
    padding: 12px 16px; border-radius: 7px;
    font-size: 14px; margin-bottom: 16px;
  }
  .alert-ok     { background: var(--ok-bg);   color: var(--ok-col); }
  .alert-danger { background: var(--perm-bg); color: var(--perm-col); }
  .alert-warn   { background: var(--soft-bg); color: var(--soft-col); }
  .alert-info   { background: var(--info-bg); color: var(--info-col); }

  /* ── Spinner ─────────────────────────────────────────────────── */
  .spinner {
    display: inline-block; width:14px; height:14px;
    border: 2px solid currentColor; border-top-color:transparent;
    border-radius:50%; animation: spin 0.7s linear infinite;
    vertical-align: middle;
  }
  @keyframes spin { to { transform: rotate(360deg); } }

  /* ── Modal ───────────────────────────────────────────────────── */
  .modal-overlay {
    display: none; position:fixed; inset:0;
    background: rgba(30,35,55,0.45); z-index:200;
    align-items: center; justify-content: center;
  }
  .modal-overlay.open { display:flex; }
  .modal {
    background: var(--bg-white); border-radius: 12px;
    padding: 28px; max-width: 480px; width:90%;
    box-shadow: 0 8px 40px rgba(0,0,0,0.18);
    animation: modal-in 0.18s ease;
  }
  @keyframes modal-in { from { transform:scale(0.93); opacity:0; } }
  .modal-title {
    font-family:'Playfair Display',serif;
    font-size:20px; color: var(--svi-navy); margin-bottom:14px;
  }
  .modal-footer { margin-top:20px; display:flex; gap:10px; justify-content:flex-end; }

  /* ── Responsive ──────────────────────────────────────────────── */
  @media (max-width: 768px) {
    .sidebar { display:none; }
    .admin-content { margin-left:0; padding: 24px 16px 60px; }
    .stat-grid { grid-template-columns: repeat(2, 1fr); }
  }
</style>
