import Link from 'next/link';
import { Home } from 'lucide-react';
import { headers } from 'next/headers';
import { getSviContent, handle404Autoblock } from '@/lib/auth';

export const dynamic = 'force-dynamic';

export default async function NotFound() {
    let clientIp = '127.0.0.1';
    try {
        const headersList = await headers();
        const forwardedFor = headersList.get('x-forwarded-for');
        const realIp = headersList.get('x-real-ip');
        clientIp = realIp || (forwardedFor ? forwardedFor.split(',')[0].trim() : '127.0.0.1');
        await handle404Autoblock(clientIp);
    } catch (e) {
        // May fail if statically generated
    }

    const content = await getSviContent('404');
    
    const txt = (key: string, fallback: string = '') => content[key] || fallback;
    
    const heading = txt('heading', 'Page Not Found');
    const emoji = txt('emoji', '🧐');
    const bodyText = txt('body', 'The page you are looking for does not exist or has been moved.');
    const btnLabel = txt('btn_label', 'Return to Home');

    return (
        <div style={{ minHeight: '60vh', display: 'flex', alignItems: 'center', justifyContent: 'center', padding: '60px 24px' }}>
            <style dangerouslySetInnerHTML={{ __html: `
                .error-box { max-width: 520px; width: 100%; text-align: center; padding: 56px 40px; border: 1px solid var(--border); border-radius: 16px; box-shadow: 0 20px 60px rgba(46,58,89,0.07); background: #fff; }
                .error-code { font-family: 'Outfit', 'Inter', sans-serif; font-size: 96px; font-weight: 800; color: var(--border); line-height: 1; margin-bottom: 4px; letter-spacing: -4px; }
                .error-emoji { font-size: 52px; margin-bottom: 16px; display: block; }
                .error-box h1 { font-family: 'Playfair Display', serif; font-weight: 700; font-size: 30px; color: var(--svi-navy); margin-bottom: 14px; }
                .error-box p { font-size: 15px; color: var(--muted); margin-bottom: 32px; line-height: 1.7; }
                .error-btn { display: inline-flex; alignItems: center; gap: 8px; background: var(--eh-purple); color: #fff; text-decoration: none; padding: 13px 28px; border-radius: 8px; font-size: 14px; font-weight: 700; transition: all 0.2s; }
                .error-btn:hover { background: #3b236b; transform: translateY(-1px); }
                .error-links { margin-top: 20px; display: flex; gap: 16px; justify-content: center; flex-wrap: wrap; }
                .error-link { font-size: 13px; color: var(--muted); text-decoration: none; }
                .error-link:hover { color: var(--eh-purple); }
            `}} />
            <div className="error-box">
                <div className="error-code">404</div>
                <span className="error-emoji">{emoji}</span>
                <h1>{heading}</h1>
                <p>{bodyText}</p>
                <Link href="/" className="error-btn">
                    <Home size={16} />
                    {btnLabel}
                </Link>
                <div className="error-links">
                    <Link href="/About" className="error-link">Contact Support</Link>
                    <Link href="/Verifi/data/user/default" className="error-link">Verify IP Status</Link>
                </div>
            </div>
        </div>
    );
}
