<?php
/**
 * Roqsafe SVI Public Footer
 */
?>
<footer style="border-top:1px solid var(--border); padding:32px 28px; margin-top: 60px; background: #fff;">
  <div style="max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
    
    <!-- Left: Copyright -->
    <div style="font-size:12px; font-weight:400; color:var(--muted); letter-spacing:0.02em; line-height: 1.5;">
      © 2021 - <?= date('Y') ?> Roqsafe LTD . All rights reserved.
    </div>

    <!-- Right: Links -->
    <div style="display: flex; gap: 24px; align-items: center;">
      <span style="color: var(--muted); font-size: 13px; font-weight: 400; padding-right: 12px; border-right: 1px solid var(--border);">IP: <?= htmlspecialchars($_SERVER['REMOTE_ADDR'] ?? 'Unknown', ENT_QUOTES) ?></span>
      <a href="/About" style="color: var(--svi-navy); text-decoration: none; font-size: 13px; font-weight: 500; transition: color 0.2s;">About Us</a>
      <a href="/TorContribution" style="color: var(--svi-navy); text-decoration: none; font-size: 13px; font-weight: 500; transition: color 0.2s;">Tor Contribution</a>
      <a href="/ServerStatus" style="color: var(--svi-navy); text-decoration: none; font-size: 13px; font-weight: 500; transition: color 0.2s;">Server Status</a>
    </div>

  </div>
</footer>

<!-- SSO and Common Scripts: Using absolute paths for deep route compatibility -->
<script src="/public/js/erickson-sso-client.js"></script>
<script>
(function() {
  'use strict';
  function checkExistingSSO() {
      try {
          const stored = sessionStorage.getItem("eh_user");
          if (stored) {
              applySSOUser(JSON.parse(stored));
          }
      } catch (e) {}
  }

  function applySSOUser(user) {
      const authBtns = document.getElementById('public-auth-buttons');
      const userProf = document.getElementById('public-user-profile');
      if (authBtns) authBtns.style.display = 'none';
      if (userProf) {
        userProf.style.display = 'flex';
        document.getElementById('user-profile-name').textContent = user.displayName || user.email;
        if (user.photoURL) document.getElementById('user-profile-img').src = user.photoURL;
      }
  }

  function handlePublicSSO(e) {
      e.preventDefault();
      if (typeof triggerEricksonSSO === 'function') {
        triggerEricksonSSO({
            apiToken: 'YOUR_SVI_TOKEN',
            onSuccess: function(user) {
                applySSOUser(user);
                sessionStorage.setItem("eh_user", JSON.stringify(user));
            },
            onError: function(err) {
                console.error("SSO Error:", err);
            }
        });
      }
  }

  function handleLogout(e) {
      if (e) e.preventDefault();
      sessionStorage.removeItem("eh_user");
      const authBtns = document.getElementById('public-auth-buttons');
      const userProf = document.getElementById('public-user-profile');
      if (authBtns) authBtns.style.display = 'flex';
      if (userProf) userProf.style.display = 'none';
  }

  const loginBtn = document.getElementById('sso-login-btn');
  const createBtn = document.getElementById('sso-create-btn');
  const logoutLnk = document.getElementById('sso-logout-link');

  if (loginBtn) loginBtn.addEventListener('click', handlePublicSSO);
  if (createBtn) createBtn.addEventListener('click', handlePublicSSO);
  if (logoutLnk) logoutLnk.addEventListener('click', handleLogout);

  checkExistingSSO();
})();
</script>
</body>
</html>
