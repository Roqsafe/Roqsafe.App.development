'use client';

import { useEffect, useState, useRef } from 'react';
import Link from 'next/link';
import { Search, Globe, ShieldCheck, UserCheck, User, CheckCircle2 } from 'lucide-react';
import Swal from 'sweetalert2';

export default function Header({ siteNotice }: { siteNotice?: string }) {
    const [user, setUser] = useState<{ displayName?: string; username?: string; email?: string; photoURL?: string } | null>(null);
    const [searchQuery, setSearchQuery] = useState('');
    const [searchResults, setSearchResults] = useState<{ title?: string; url: string }[]>([]);
    const [activeTechIcon, setActiveTechIcon] = useState<string | null>(null);
    const searchInputRef = useRef<HTMLInputElement>(null);

    const SSO_CONFIG = {
        url: 'https://accounts.ericksonholding.com/login',
        token: 'eh_live_1m0uo2y0_473s2zxf',
        origin: typeof window !== 'undefined' ? window.location.origin : 'https://roqsafe.com',
        provider: 'https://accounts.ericksonholding.com'
    };

    useEffect(() => {
        const storedUser = sessionStorage.getItem('roqsafe_user') || sessionStorage.getItem('eh_user');
        if (storedUser) {
            try {
                // eslint-disable-next-line
                setUser(JSON.parse(storedUser));
            } catch (e) { }
        }

        const handleMessage = (event: MessageEvent) => {
            if (event.origin !== SSO_CONFIG.provider) return;
            const data = event.data;
            if (data && data.type === 'SSO_AUTH_SUCCESS') {
                const payload = data.payload;
                sessionStorage.setItem('roqsafe_user', JSON.stringify(payload));
                sessionStorage.setItem('eh_user', JSON.stringify(payload));
                setUser(payload);
                Swal.fire({
                    icon: 'success',
                    title: 'Authentication Successful',
                    text: `Welcome back, ${payload.displayName || payload.email}`,
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        };

        window.addEventListener('message', handleMessage);
        return () => window.removeEventListener('message', handleMessage);
    }, []);

    useEffect(() => {
        const handleKeyDown = (e: KeyboardEvent) => {
            if (((e.ctrlKey || e.metaKey) && e.key === 'k') || (e.key === '/' && document.activeElement !== searchInputRef.current)) {
                e.preventDefault();
                searchInputRef.current?.focus();
            }
        };
        window.addEventListener('keydown', handleKeyDown);
        return () => window.removeEventListener('keydown', handleKeyDown);
    }, []);

    useEffect(() => {
        const delayDebounceFn = setTimeout(() => {
            if (searchQuery.length >= 2) {
                fetch(`/api/search?q=${encodeURIComponent(searchQuery)}`)
                    .then(r => r.json())
                    .then(data => {
                        setSearchResults(data);
                    })
                    .catch(() => setSearchResults([]));
            } else {
                setSearchResults([]);
            }
        }, 300);

        return () => clearTimeout(delayDebounceFn);
    }, [searchQuery]);

    const initiateSSO = (e: React.MouseEvent) => {
        e.preventDefault();
        const ssoUrl = `${SSO_CONFIG.url}?isSSO=true&token=${SSO_CONFIG.token}&returnOrigin=${encodeURIComponent(SSO_CONFIG.origin)}`;
        const w = 500, h = 700;
        const left = (window.screen.width / 2) - (w / 2);
        const top = (window.screen.height / 2) - (h / 2);
        window.open(ssoUrl, 'EricksonHoldingSSO', `toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, copyhistory=no, width=${w}, height=${h}, top=${top}, left=${left}`);
    };

    const handleLogout = (e: React.MouseEvent) => {
        e.preventDefault();
        sessionStorage.removeItem('roqsafe_user');
        sessionStorage.removeItem('eh_user');
        setUser(null);
        window.location.reload();
    };

    const iconData: Record<string, { title: string, desc: string }> = {
        cyber: { title: "Cyber Intelligence", desc: "Global threat intelligence and network integrity monitoring for connected environments." },
        shield: { title: "System Security", desc: "Advanced asset protection, firewalls, and dedicated server room security protocols." },
        verify: { title: "SVI Verification", desc: "Secure token management and user authentication verification tools." }
    };

    const showProjectToolsPopup = (e: React.MouseEvent) => {
        e.preventDefault();
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
                    .swal2-popup.svi-sleek-popup { border-radius: 20px; padding: 32px 24px; box-shadow: 0 40px 80px rgba(0,0,0,0.15); }
                </style>
                <div style="display:flex; flex-direction:column; gap:16px; margin-top:20px;">
                    <a href="/Verifi/data/user/default" class="svi-tool-btn">
                        <div class="svi-tool-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield-check"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2-1 4-3 5.99-5.11a2 2 0 0 1 2.72-.28c.49.36 1.07.72 1.63 1.06 1.45 1.05 3.12 2.27 4.66 3.33a1 1 0 0 1 1 1Z"/><path d="m9 12 2 2 4-4"/></svg>
                        </div>
                        <div class="svi-tool-content">
                            <h4>Security & Verification Inspector</h4>
                            <p>Analyze IP safety scores, review infrastructure access restrictions, and manage verifications.</p>
                        </div>
                    </a>
                    
                    <a href="/myspeed" class="svi-tool-btn">
                        <div class="svi-tool-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-gauge"><path d="m12 14 4-4"/><path d="M3.34 19a10 10 0 1 1 17.32 0"/></svg>
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
            padding: '1em'
        });
    };

    return (
        <>
            {siteNotice && <div className="site-notice">{siteNotice}</div>}

            <header>
                <nav className="main-nav">
                    <div className="nav-container">
                        <Link href="/" className="brand">
                            <img src="/app/img/roqsafe.svg" alt="Roqsafe" className="brand-logo" />
                            <span className="brand-divider"></span>
                            <span className="brand-svi-sub">Security and verification</span>
                        </Link>

                        <div className="header-actions">
                            <div className="tech-icon-group">
                                <button className={`tech-icon-btn ${activeTechIcon === 'cyber' ? 'active' : ''}`} onClick={(e) => { e.stopPropagation(); setActiveTechIcon(activeTechIcon === 'cyber' ? null : 'cyber'); }} title="Cyber Security">
                                    <Globe size={20} />
                                </button>
                                <button className={`tech-icon-btn ${activeTechIcon === 'shield' ? 'active' : ''}`} onClick={(e) => { e.stopPropagation(); setActiveTechIcon(activeTechIcon === 'shield' ? null : 'shield'); }} title="Systems Protection">
                                    <ShieldCheck size={20} />
                                </button>
                                <button className={`tech-icon-btn ${activeTechIcon === 'verify' ? 'active' : ''}`} onClick={(e) => { e.stopPropagation(); setActiveTechIcon(activeTechIcon === 'verify' ? null : 'verify'); }} title="Verification Identity">
                                    <UserCheck size={20} />
                                </button>
                            </div>

                            <div style={{ position: 'relative' }}>
                                {!user ? (
                                    <>
                                        <div id="public-auth-buttons" style={{ display: 'flex', gap: '12px', alignItems: 'center' }}>
                                            <a href="#" id="sso-login-btn" onClick={initiateSSO} style={{ padding: '7px 16px', border: '1px solid #e2e4e8', color: '#1a1a1a', textDecoration: 'none', borderRadius: '4px', fontWeight: 600, fontSize: '12px' }}>Log in</a>
                                            <div id="mobile-user-icon" onClick={initiateSSO}>
                                                <User size={16} />
                                            </div>
                                            <a href="#" id="sso-create-btn" onClick={initiateSSO} style={{ padding: '7px 16px', background: 'var(--eh-purple)', color: '#fff', textDecoration: 'none', borderRadius: '4px', fontWeight: 600, fontSize: '12px' }}>Create account</a>
                                        </div>
                                    </>
                                ) : (
                                    <div id="public-user-profile" style={{ display: 'flex', alignItems: 'center', gap: '12px' }}>
                                        <div style={{ display: 'flex', flexDirection: 'column', alignItems: 'flex-end' }}>
                                            <span id="user-profile-name" style={{ fontWeight: 600, fontSize: '13px', color: 'var(--svi-navy)' }}>{user.displayName || user.username || user.email}</span>
                                            <a href="#" id="sso-logout-link" onClick={handleLogout} style={{ fontSize: '10px', color: 'var(--muted)', textDecoration: 'none', textTransform: 'uppercase', letterSpacing: '0.05em' }}>Log out</a>
                                        </div>
                                        <img id="user-profile-img" src={user.photoURL || '/app/img/ehltd_eh.png'} alt="User" style={{ width: '34px', height: '34px', borderRadius: '50%', objectFit: 'cover', border: '1px solid #e2e4e8' }} />
                                    </div>
                                )}

                                {activeTechIcon && iconData[activeTechIcon] && (
                                    <div id="tech-info-panel" style={{ display: 'block' }}>
                                        <h4 id="panel-title">{iconData[activeTechIcon].title}</h4>
                                        <p id="panel-desc">{iconData[activeTechIcon].desc}</p>
                                        <div id="panel-actions" style={{ display: 'flex', gap: '10px' }}>
                                            {!user && <button onClick={initiateSSO} style={{ flex: 1, padding: '10px', background: 'var(--eh-purple)', color: '#fff', border: 'none', borderRadius: '4px', fontSize: '13px', fontWeight: 600, cursor: 'pointer' }}>Log In Now</button>}
                                        </div>
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>
                </nav>

                <div className="sub-nav">
                    <div className="sub-nav-container">
                        <div className="search-wrapper">
                            <Search className="search-icon-fixed" size={14} />
                            <input
                                type="text"
                                className="search-input"
                                placeholder="Search SVI database..."
                                autoComplete="off"
                                value={searchQuery}
                                onChange={(e) => setSearchQuery(e.target.value)}
                                ref={searchInputRef}
                            />
                            <span className="search-shortcut" title="Press Ctrl+K or Cmd+K to search">⌘K / Ctrl+K</span>
                            {searchResults.length > 0 && (
                                <div className="search-results" style={{ display: 'block' }}>
                                    {searchResults.map((item, idx) => (
                                        <Link key={idx} href={item.url} className="search-result-item" onClick={() => setSearchQuery('')}>
                                            <span className="title">{item.title}</span>
                                        </Link>
                                    ))}
                                </div>
                            )}
                        </div>
                        <div className="sub-nav-links-row">
                            <a href="#" onClick={showProjectToolsPopup} className="sub-nav-link footer-tech-link">Project Tools</a>
                            <Link href="/Verifi/data/user/default" className="sub-nav-link footer-tech-link" style={{ color: 'var(--status-ok)', fontWeight: 600, fontSize: '12px', display: 'inline-flex', alignItems: 'center', gap: '4px', textDecoration: 'none' }}>
                                <CheckCircle2 size={14} /> Verify
                            </Link>
                        </div>
                    </div>
                </div>
            </header>
        </>
    );
}
