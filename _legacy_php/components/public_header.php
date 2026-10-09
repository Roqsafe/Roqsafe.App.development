<?php
/**
 * Roqsafe SVI Public Header
 * Parameters:
 * - $c: Content array from database
 * - $siteNotice: Optional site notice text
 * - $pageTitle: Optional page title suffix
 */
$pageTitle = $pageTitle ?? htmlspecialchars(txt($c, 'page_title', 'SVI — Security and Verification Inspector'), ENT_QUOTES);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>EH.SVI | <?= $pageTitle ?></title>
<meta name="description" content="SVI – Security and Verification Inspector by Roqsafe LTD. Check your security status.">
<link rel="icon" href="/app/img/ehltd_eh.png">
<!-- Critical: All Assets MUST be Absolute paths to fix deep routes (e.g. /Verifi/data/user/{token}) -->
<link rel="stylesheet" href="/ehltd.css">
<!-- Silicon Valley Fonts: Inter for tech feel -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
<!-- Lucide Icons -->
<script src="https://unpkg.com/lucide@latest"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  :root {
    --eh-purple:#4C2D8A; --svi-navy:#2E3A59; --svi-navy-light:#3D4E6B;
    --body-text:#1a1a2e; --muted:#5a6070; --border:#d0d4dc;
    --bg-white:#ffffff; --input-bg:#f1f3f7;
    --status-ok:#1a6b3a;      --status-ok-bg:#eaf6ef;
    --status-warn:#92400e;    --status-warn-bg:#fffbeb;
    --status-danger:#991b1b;  --status-danger-bg:#fff1f2;
    --status-info:#1e40af;    --status-info-bg:#eff6ff;
  }
  /* ── Global page frame: always white ─────────────────────────── */
  html, body { background: var(--bg-white) !important; }
  main, .page-container, .page-wrap {
    background: var(--bg-white);
  }
  body {
    /* Silicon Valley Typography */
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    background:var(--bg-white); color:var(--body-text);
    min-height:100vh; overflow-x:hidden;
    -webkit-font-smoothing: antialiased;
  }

  /* ── Site notice banner ───────────────────────────────── */
  .site-notice {
    background: var(--svi-navy); color:#fff;
    padding: 10px 24px; font-size:12px; text-align:center;
    font-weight:400; letter-spacing:0.02em;
  }

  /* ── Navbar ───────────────────────────────────────────── */
  header {
    position:sticky; top:0; z-index:1000;
    background: #fff url('/Image_od8tnyod8tnyod8t.png') no-repeat center center;
    background-size: cover;
    box-shadow: 0 4px 20px rgba(0,0,0,0.02);
    border-bottom:1px solid var(--border);
    transition: all 0.3s ease;
  }
  nav.main-nav {
    display:flex; align-items:center;
    padding:12px 28px;
  }
  .nav-container {
    max-width:1200px; width:100%; margin:0 auto;
    display:flex; align-items:center; justify-content:space-between;
    gap: 30px;
  }
  .brand { display:flex; align-items:center; gap:16px; text-decoration:none; }
  .brand-logo { height:36px; object-fit:contain; transition: height 0.3s; }
  .brand-divider { width:1px; height:32px; background:#e2e4e8; flex-shrink:0; }
  
  .brand-svi-sub { 
    font-size:11px; 
    font-weight:600; 
    color:var(--svi-navy); 
    letter-spacing: 0.1em;
    text-transform: uppercase;
    display: block; /* Restored */
    opacity: 0.7;
    max-width: 140px;
    line-height: 1.3;
  }

  .header-actions { display: flex; align-items: center; gap: 24px; }
  
  /* ── Tech Icons ───────────────────────────────────────── */
  .tech-icon-group { display: flex; gap: 16px; }
  .tech-icon-btn {
    background: none; border: none; cursor: pointer; color: var(--svi-navy); padding: 8px; border-radius: 4px; transition: all 0.2s; position: relative;
  }
  .tech-icon-btn:hover { background: var(--input-bg); color: var(--eh-purple); }
  .tech-icon-btn.active { color: var(--eh-purple); background: var(--input-bg); }

  /* ── Quick Info Detail Panel ──────────────────────────── */
  #tech-info-panel {
    display: none; position: absolute; top: 110%; right: 0; width: 320px; background: #fff; border: 1px solid var(--border); border-radius: 4px; padding: 24px; box-shadow: 0 15px 30px rgba(0,0,0,0.1); text-align: left; z-index: 100;
  }
  #tech-info-panel h4 { font-size: 15px; margin-bottom: 10px; color: var(--eh-purple); font-weight: 700; }
  #tech-info-panel p { font-size: 13px; margin-bottom: 18px; line-height: 1.6; color: var(--muted); }
  
  /* ── Secondary Navbar (Silicon Valley Search Bar) ─────── */
  .sub-nav {
    background: rgba(255, 255, 255, 0.5); /* Semi-transparent */
    backdrop-filter: blur(5px);
    border-top: 1px solid var(--border);
    padding: 6px 28px;
  }
  .sub-nav-container {
    max-width:1200px; width:100%; margin:0 auto;
    display:flex; align-items: center;
    gap:24px;
  }
  .search-wrapper { position: relative; flex: 1; max-width: 320px; }
  .search-input {
    width: 100%; padding: 7px 12px 7px 34px; border-radius: 4px; border: 1px solid var(--border); background: #fff; font-size: 13px; transition: all 0.2s; color: var(--body-text);
  }
  .search-input:focus { outline: none; border-color: var(--eh-purple); box-shadow: 0 0 0 3px rgba(76, 45, 138, 0.08); }
  .search-icon-fixed { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--muted); stroke-width: 2.5px; }
  .search-shortcut { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); font-size: 9px; color: var(--muted); border: 1px solid var(--border); padding: 1px 4px; border-radius: 3px; font-weight: 600; background: #fff; }
  
  .search-results {
    display: none; position: absolute; top: 110%; left: 0; width: 100%; background: #fff; border: 1px solid var(--border); border-radius: 4px; box-shadow: 0 10px 20px rgba(0,0,0,0.08); z-index: 200; max-height: 300px; overflow-y: auto;
  }
  .search-result-item { padding: 10px 14px; border-bottom: 1px solid #f0f0f0; cursor: pointer; text-decoration: none; display: block; }
  .search-result-item:hover { background: #f8f8fb; }
  .search-result-item .title { font-weight: 600; font-size: 13px; color: var(--svi-navy); display: block; }

  .sub-nav-links-row {
    display: flex;
    align-items: center;
    gap: 16px;
  }

  .sub-nav-link {
    font-size: 12px; color: var(--svi-navy); text-decoration: none; font-weight: 500; transition: color 0.2s;
  }
  .sub-nav-link:hover { color: var(--eh-purple); }

  /* ── Responsive: compact mobile nav (no hamburger, no drawer) ─── */
  @media (max-width: 768px) {
    nav.main-nav { padding: 8px 14px; }

    .nav-container { gap: 12px; }

    .brand-divider { display: none; }
    .brand-svi-sub { display: none; }
    .brand-logo { height: 28px; }

    /* Shrink icon buttons */
    .tech-icon-btn { padding: 6px; }
    .tech-icon-group { gap: 6px; }

    /* Shrink auth buttons */
    #public-auth-buttons a {
      padding: 6px 10px !important;
      font-size: 11px !important;
    }
    #public-auth-buttons { gap: 6px !important; }

    /* Sub-nav */
    .sub-nav { padding: 5px 14px; }
    .sub-nav-container { gap: 10px; }
    .search-wrapper { max-width: 100%; }
    .search-shortcut { display: none; }
    .sub-nav-links-row { gap: 12px; overflow-x: auto; white-space: nowrap; scrollbar-width: none; }
    .sub-nav-links-row::-webkit-scrollbar { display: none; }

    #sso-create-btn { display: none !important; }
    #mobile-user-icon { display: flex !important; }

    #tech-info-panel { width: calc(100vw - 28px); right: -14px; }
  }

  #mobile-user-icon {
    display: none;
    align-items: center; justify-content: center;
    width: 32px; height: 32px; border-radius: 50%;
    background: var(--input-bg); border: 1px solid var(--border);
    color: var(--muted);
  }

  /* Standard Page Container Fix */
  main.page-container {
    max-width: 1100px; margin: 0 auto; padding: 60px 24px; min-height: 60vh;
  }
</style>
</head>
<body>

<?php if (!empty($siteNotice)): ?>
<div class="site-notice"><?= htmlspecialchars($siteNotice, ENT_QUOTES) ?></div>
<?php endif; ?>

<header>
  <nav class="main-nav">
    <div class="nav-container">
      <!-- Logo / Brand -->
      <a class="brand" href="/">
        <img src="/app/img/roqsafe.svg" alt="Roqsafe" class="brand-logo">
        <span class="brand-divider"></span>
        <span class="brand-svi-sub">Security and verification</span>
      </a>

      <!-- Icons + Auth (always visible, compact on mobile) -->
      <div class="header-actions">
        <div class="tech-icon-group">
          <button class="tech-icon-btn" data-type="cyber" title="Cyber Security">
            <i data-lucide="globe" style="width:20px;height:20px;"></i>
          </button>
          <button class="tech-icon-btn" data-type="shield" title="Systems Protection">
            <i data-lucide="shield-check" style="width:20px;height:20px;"></i>
          </button>
          <button class="tech-icon-btn" data-type="verify" title="Verification Identity">
            <i data-lucide="user-check" style="width:20px;height:20px;"></i>
          </button>
        </div>

        <div style="position:relative;">
          <div id="public-auth-buttons" style="display:flex;gap:12px;align-items:center;">
            <a href="#" id="sso-login-btn" style="padding:7px 16px;border:1px solid #e2e4e8;color:#1a1a1a;text-decoration:none;border-radius:4px;font-weight:600;font-size:12px;">Log in</a>
            <div id="mobile-user-icon" onclick="document.getElementById('sso-login-btn').click()">
              <i data-lucide="user" style="width:16px;height:16px;"></i>
            </div>
            <a href="#" id="sso-create-btn" style="padding:7px 16px;background:var(--eh-purple);color:#fff;text-decoration:none;border-radius:4px;font-weight:600;font-size:12px;">Create account</a>
          </div>
          <div id="public-user-profile" style="display:none;align-items:center;gap:12px;">
            <div style="display:flex;flex-direction:column;align-items:flex-end;">
              <span id="user-profile-name" style="font-weight:600;font-size:13px;color:var(--svi-navy);"></span>
              <a href="#" id="sso-logout-link" style="font-size:10px;color:var(--muted);text-decoration:none;text-transform:uppercase;letter-spacing:0.05em;">Log out</a>
            </div>
            <img id="user-profile-img" src="/app/img/ehltd_eh.png" alt="User" style="width:34px;height:34px;border-radius:50%;object-fit:cover;border:1px solid #e2e4e8;">
          </div>
          <!-- Detail Panel -->
          <div id="tech-info-panel">
            <h4 id="panel-title">Cyber Security</h4>
            <p id="panel-desc">Advanced surveillance and real-time network monitoring for global asset protection.</p>
            <div id="panel-actions" style="display:flex;gap:10px;">
              <button onclick="document.getElementById('sso-login-btn').click()" style="flex:1;padding:10px;background:var(--eh-purple);color:#fff;border:none;border-radius:4px;font-size:13px;font-weight:600;cursor:pointer;">Log In Now</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </nav>

  <!-- Sub-nav (search + links) -->
  <div class="sub-nav">
    <div class="sub-nav-container">
      <div class="search-wrapper">
        <i data-lucide="search" class="search-icon-fixed" style="width:14px;height:14px;"></i>
        <input type="text" id="db-search-input" class="search-input" placeholder="Search SVI database..." autocomplete="off">
        <span class="search-shortcut" title="Press Ctrl+K or Cmd+K to search">⌘K / Ctrl+K</span>
        <div id="search-results-list" class="search-results"></div>
      </div>
      <div class="sub-nav-links-row">
        <a href="#" onclick="showProjectToolsPopup(event)" class="sub-nav-link footer-tech-link">Project Tools</a>
        <a href="/Verifi/data/user/default" class="sub-nav-link footer-tech-link" style="color:var(--status-ok);font-weight:600;font-size:12px;display:inline-flex;align-items:center;gap:4px;text-decoration:none;"><i data-lucide="check-circle-2" style="width:14px;height:14px;"></i> Verify</a>
      </div>
    </div>
  </div>
</header>

<script>
window.showProjectToolsPopup = function(e) {
    if (e) e.preventDefault();
    Swal.fire({
        title: '<span style="font-family: \'Playfair Display\', serif; font-weight: 700; color: var(--svi-navy); font-size: 26px;">Project Tools</span>',
        html: `
            <style>
                .svi-tool-btn {
                    display: flex; align-items: center; gap: 16px;
                    padding: 18px 20px; border-radius: 12px; border: 1px solid var(--border);
                    background: #fff; cursor: pointer; text-align: left;
                    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); text-decoration: none; color: var(--body-text);
                    position: relative; overflow: hidden;
                }
                .svi-tool-btn:hover {
                    border-color: var(--eh-purple);
                    box-shadow: 0 10px 25px rgba(76, 45, 138, 0.12);
                    transform: translateY(-3px);
                }
                .svi-tool-icon {
                    width: 48px; height: 48px; border-radius: 12px;
                    background: #f4f0ff; color: var(--eh-purple); 
                    display: flex; align-items: center; justify-content: center;
                    flex-shrink: 0;
                }
                .svi-tool-content h4 { margin:0 0 6px; font-size:16px; font-weight:600; color:var(--svi-navy); font-family: 'Inter', sans-serif;}
                .svi-tool-content p { margin:0; font-size:13px; color:var(--muted); line-height:1.5; font-family: 'Inter', sans-serif; }
                
                .svi-tool-btn.disabled {
                    opacity: 0.6; cursor: not-allowed; background: #fafafa; border-style: dashed;
                }
                .svi-tool-btn.disabled:hover {
                    box-shadow: none; transform: none; border-color: var(--border);
                }
                .svi-tool-btn.disabled .svi-tool-icon {
                    background: #e2e4e8; color: var(--muted);
                }
                
                .swal2-popup.svi-sleek-popup {
                    border-radius: 20px; padding: 32px 24px; box-shadow: 0 40px 80px rgba(0,0,0,0.15);
                }
            </style>
            <div style="display:flex; flex-direction:column; gap:16px; margin-top:20px;">
                <a href="/Verifi/data/user/default" class="svi-tool-btn">
                    <div class="svi-tool-icon">
                        <i data-lucide="shield-check" style="width:24px; height:24px;"></i>
                    </div>
                    <div class="svi-tool-content">
                        <h4>Security & Verification Inspector</h4>
                        <p>Analyze IP safety scores, review infrastructure access restrictions, and manage verifications.</p>
                    </div>
                </a>
                
                <a href="/myspeed" class="svi-tool-btn">
                    <div class="svi-tool-icon">
                        <i data-lucide="gauge" style="width:24px; height:24px;"></i>
                    </div>
                    <div class="svi-tool-content">
                        <h4>MySpeed Internet Tracker</h4>
                        <p>Automate speed tests, track real-time analytics, and receive uptime alerts.</p>
                    </div>
                </a>
            </div>
        `,
        showConfirmButton: false,
        showCloseButton: true,
        customClass: { popup: 'svi-sleek-popup' },
        width: '520px',
        padding: '1em',
        didOpen: () => {
            if(typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }
    });
};

document.addEventListener('DOMContentLoaded', () => {
    lucide.createIcons();
    const searchInput = document.getElementById('db-search-input');
    const resultsList = document.getElementById('search-results-list');
    const techBtns = document.querySelectorAll('.tech-icon-btn');
    const infoPanel = document.getElementById('tech-info-panel');
    
    // ── Search Logic ──────────────────────────────────────────
    window.addEventListener('keydown', (e) => {
        if (((e.ctrlKey || e.metaKey) && e.key === 'k') || (e.key === '/' && document.activeElement !== searchInput)) {
            e.preventDefault();
            searchInput.focus();
        }
    });

    let searchTimeout;
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            const q = e.target.value.trim();
            if (q.length < 2) { resultsList.style.display = 'none'; return; }

            searchTimeout = setTimeout(() => {
                fetch(`/api/search?q=${encodeURIComponent(q)}`)
                    .then(r => r.json())
                    .then(data => {
                        resultsList.innerHTML = '';
                        if (data.length > 0) {
                            data.forEach(item => {
                                const link = document.createElement('a');
                                link.className = 'search-result-item';
                                link.href = item.url;
                                link.innerHTML = `<span class="title">${item.title}</span>`;
                                resultsList.appendChild(link);
                            });
                            resultsList.style.display = 'block';
                        } else { resultsList.style.display = 'none'; }
                    });
            }, 300);
        });
    }

    document.addEventListener('click', (e) => {
        if (searchInput && resultsList && !searchInput.contains(e.target) && !resultsList.contains(e.target)) {
            resultsList.style.display = 'none';
        }
    });

    // ── Tech Icons Logic ──────────────────────────────────────
    const iconData = {
        cyber: { title: "Cyber Intelligence", desc: "Global threat intelligence and network integrity monitoring for connected environments." },
        shield: { title: "System Security", desc: "Advanced asset protection, firewalls, and dedicated server room security protocols." },
        verify: { title: "SVI Verification", desc: "Secure token management and user authentication verification tools." }
    };

    techBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const type = btn.dataset.type;
            const data = iconData[type];
            if (btn.classList.contains('active')) {
                btn.classList.remove('active'); infoPanel.style.display = 'none';
            } else {
                techBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                document.getElementById('panel-title').textContent = data.title;
                document.getElementById('panel-desc').textContent = data.desc;
                infoPanel.style.display = 'block';
            }
        });
    });

    document.addEventListener('click', () => {
        if (infoPanel) {
            infoPanel.style.display = 'none';
            techBtns.forEach(b => b.classList.remove('active'));
        }
    });
    if (infoPanel) infoPanel.addEventListener('click', (e) => e.stopPropagation());

    const SSO_CONFIG = {
        url: 'http://localhost:3001/login',
        token: 'eh_live_1m0uo2y0_473s2zxf',
        origin: 'https://roqsafe.com',
        provider: 'http://localhost:3001'
    };

    function openCenteredPopup(url, title, w, h) {
        const left = (screen.width / 2) - (w / 2);
        const top = (screen.height / 2) - (h / 2);
        return window.open(url, title, `toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, copyhistory=no, width=${w}, height=${h}, top=${top}, left=${left}`);
    }

    function initiateSSO(e) {
        if (e) e.preventDefault();
        const ssoUrl = `${SSO_CONFIG.url}?isSSO=true&token=${SSO_CONFIG.token}&returnOrigin=${encodeURIComponent(SSO_CONFIG.origin)}`;
        openCenteredPopup(ssoUrl, 'Erickson Holding SSO', 500, 700);
    }

    function updateAuthUI() {
        const authData = sessionStorage.getItem('roqsafe_user');
        const authButtons = document.getElementById('public-auth-buttons');
        const userProfile = document.getElementById('public-user-profile');
        
        if (authData) {
            const user = JSON.parse(authData);
            document.getElementById('user-profile-name').textContent = user.displayName || user.username || user.email;
            if (user.photoURL) {
                document.getElementById('user-profile-img').src = user.photoURL;
            }
            authButtons.style.display = 'none';
            userProfile.style.display = 'flex';
        } else {
            authButtons.style.display = 'flex';
            userProfile.style.display = 'none';
        }
    }

    window.addEventListener('message', (event) => {
        if (event.origin !== SSO_CONFIG.provider) return;
        
        const data = event.data;
        if (data && data.type === 'SSO_AUTH_SUCCESS') {
            const payload = data.payload;
            sessionStorage.setItem('roqsafe_user', JSON.stringify(payload));
            updateAuthUI();
            
            // Close any open popups if we were monitoring them (though postMessage comes from the popup)
            Swal.fire({
                icon: 'success',
                title: 'Authentication Successful',
                text: `Welcome back, ${payload.displayName || payload.email}`,
                timer: 2000,
                showConfirmButton: false
            });
        }
    });

    document.getElementById('sso-login-btn').addEventListener('click', initiateSSO);
    document.getElementById('sso-create-btn').addEventListener('click', initiateSSO);
    document.getElementById('sso-logout-link').addEventListener('click', (e) => {
        e.preventDefault();
        sessionStorage.removeItem('roqsafe_user');
        updateAuthUI();
        window.location.reload();
    });

    // Mobile buttons (if they exist)
    const mLogin = document.getElementById('m-sso-login');
    const mCreate = document.getElementById('m-sso-create');
    if (mLogin) mLogin.addEventListener('click', initiateSSO);
    if (mCreate) mCreate.addEventListener('click', initiateSSO);

    updateAuthUI();
});
</script>
