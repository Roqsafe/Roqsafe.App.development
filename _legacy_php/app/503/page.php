<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Unavailable | EH.SVI</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/ehltd.css">
    <style>
        body { background: #fff; margin: 0; font-family: 'Outfit', sans-serif; }
        .error-full {
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            padding: 40px 24px;
        }
        .error-box {
            max-width: 560px; width: 100%;
            text-align: center; padding: 60px 40px;
            border: 1px solid #eee; border-radius: 24px;
            box-shadow: 0 30px 80px rgba(0,0,0,0.04);
            background: #fff;
        }
        .error-code {
            font-size: 11px; font-weight: 800; text-transform: uppercase;
            letter-spacing: 0.2em; color: var(--eh-purple);
            margin-bottom: 16px; display: block;
        }
        .error-box h1 {
            font-size: clamp(28px, 4vw, 36px); font-weight: 800;
            color: #0c1c3f; margin: 0 0 16px; letter-spacing: -0.02em;
        }
        .error-box p {
            font-size: 16px; color: #6b7280; line-height: 1.6;
            margin: 0 0 40px;
        }
        .error-gfx {
            margin-bottom: 40px;
        }
        .error-gfx img {
            width: 280px; height: auto;
            filter: drop-shadow(0 20px 40px rgba(0,0,0,0.05));
        }
        .error-actions {
            display: flex; gap: 12px; justify-content: center;
        }
        .error-btn {
            display: inline-flex; align-items: center; gap: 8px;
            background: var(--eh-purple); color: #fff;
            text-decoration: none; padding: 14px 32px; border-radius: 12px;
            font-size: 14px; font-weight: 700; transition: all 0.25s;
            box-shadow: 0 10px 25px rgba(76,45,138,0.25);
        }
        .error-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(76,45,138,0.35);
        }
        .error-btn.secondary {
            background: #f4f0ff; color: var(--eh-purple);
            box-shadow: none;
        }
        .error-footer {
            margin-top: 48px; padding-top: 32px; border-top: 1px solid #f3f4f6;
            font-size: 13px; color: #9ca3af;
        }
        .error-footer a { color: var(--eh-purple); text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>

<div class="error-full">
    <div class="error-box">
        <span class="error-code">Status 503 • Service Blocked</span>
        <div class="error-gfx">
            <img src="/app/img/svi_503.png" alt="Maintenance">
        </div>
        <h1>Connection Lost</h1>
        <p>Our security core is currently unable to reach the primary database. This usually happens during brief network snapshots or system synchronization.</p>
        
        <div class="error-actions">
            <a href="javascript:window.location.reload()" class="error-btn">
                Retry Connection
            </a>
            <a href="/About" class="error-btn secondary">
                Technical Support
            </a>
        </div>

        <div class="error-footer">
            Critical infrastructure alert? Email <a href="mailto:security@roqsafe.com">security@roqsafe.com</a>
        </div>
    </div>
</div>

<script src="https://unpkg.com/lucide@latest"></script>
<script>lucide.createIcons();</script>
</body>
</html>
