import { headers } from 'next/headers';
import { getSviContent, getSviSetting, checkGlobalBlock } from '@/lib/auth';
import ClientForm from './ClientForm';

export default async function VerifiPage({ params }: { params: Promise<{ token: string }> }) {
    const { token } = await params;
    
    const headersList = await headers();
    const forwardedFor = headersList.get('x-forwarded-for');
    const realIp = headersList.get('x-real-ip');
    const clientIp = realIp || (forwardedFor ? forwardedFor.split(',')[0].trim() : '127.0.0.1');

    const content = await getSviContent('main');
    const maintenanceMode = await getSviSetting('maintenance_mode', '0') === '1';
    const maintenanceMsg = await getSviSetting('maintenance_message', 'We are currently performing scheduled maintenance.');
    const showIpTool = await getSviSetting('show_ip_tool', '1') === '1';

    const blockRecord = await checkGlobalBlock(clientIp);
    const isBlocked = !!blockRecord;

    const txt = (key: string, fallback: string = '') => content[key] || fallback;

    const heroHeading = isBlocked ? txt('hero_heading', 'Oh no…') : txt('hero_heading_safe', 'All Good!');
    const heroEmoji = isBlocked ? txt('hero_emoji', '🚫') : txt('hero_emoji_safe', '✅');
    const introP1 = isBlocked ? txt('intro_p1') : txt('intro_p1_safe', 'You are a great user! Your IP address has been verified and is currently safe on our platform.');
    const introP2 = isBlocked ? txt('intro_p2') : txt('intro_p2_safe', 'Our systems continuously monitor network health to ensure Erickson Holding remains a secure environment. Your connection checks out perfectly.');

    const sec1Title = isBlocked ? txt('section1_title') : txt('section1_title_safe', 'Security First');
    const sec1Body = isBlocked ? txt('section1_body') : txt('section1_body_safe', 'We prioritize your online safety by actively monitoring our network infrastructure.');
    const sec2Title = isBlocked ? txt('section2_title') : txt('section2_title_safe', 'Continuous Verification');
    const sec2Body = isBlocked ? txt('section2_body') : txt('section2_body_safe', 'Occasional soft-checks help guarantee the ecosystem stays protected.');
    const sec3Title = isBlocked ? txt('section3_title') : txt('section3_title_safe', 'Data Privacy');
    const sec3Body = isBlocked ? txt('section3_body') : txt('section3_body_safe', 'Your session and connection details remain securely encrypted.');
    const sec4Title = isBlocked ? txt('section4_title') : txt('section4_title_safe', 'Best Practices');
    const sec4Item1 = isBlocked ? txt('section4_item1') : txt('section4_item1_safe', 'Always use strong, unique passwords.');
    const sec4Item2 = isBlocked ? txt('section4_item2') : txt('section4_item2_safe', 'Enable Two-Factor Authentication.');
    const sec4Item3 = isBlocked ? txt('section4_item3') : txt('section4_item3_safe', 'Regularly monitor your active sessions.');
    const closing = isBlocked ? txt('closing_note') : txt('closing_note_safe', 'Thank you for being a part of our secure platform.');

    return (
        <div className="page-container">
            {maintenanceMode ? (
                <div className="maintenance-box">
                    <div className="m-icon">🔧</div>
                    <h1>Under Maintenance</h1>
                    <p>{maintenanceMsg}</p>
                </div>
            ) : (
                <>
                    {token === 'default' && (
                        <script dangerouslySetInnerHTML={{ __html: `setTimeout(function() { window.location.reload(); }, 60000);` }} />
                    )}

                    <div className="heading-block">
                        <h1>
                            {heroHeading} <span className="emoji">{heroEmoji}</span>
                        </h1>
                        {token !== 'default' && (
                            <p style={{ fontSize: '14px', color: 'var(--muted)', marginTop: '8px' }}>
                                Active Session Token: <code>{token}</code>
                            </p>
                        )}
                    </div>

                    <div className="body-section"><p>{introP1}</p></div>
                    <div className="body-section"><p>{introP2}</p></div>

                    <ClientForm isBlocked={isBlocked} showIpTool={showIpTool} content={content} />

                    <h2 className="section-title" style={{ fontFamily: "'Playfair Display',serif", fontWeight: 400, fontSize: 'clamp(22px,5vw,32px)', color: 'var(--svi-navy)', margin: '44px 0 16px', lineHeight: 1.2 }}>{sec1Title}</h2>
                    <p className="section-body">{sec1Body}</p>

                    <h2 className="section-title" style={{ fontFamily: "'Playfair Display',serif", fontWeight: 400, fontSize: 'clamp(22px,5vw,32px)', color: 'var(--svi-navy)', margin: '44px 0 16px', lineHeight: 1.2 }}>{sec2Title}</h2>
                    <p className="section-body">{sec2Body}</p>

                    <h2 className="section-title" style={{ fontFamily: "'Playfair Display',serif", fontWeight: 400, fontSize: 'clamp(22px,5vw,32px)', color: 'var(--svi-navy)', margin: '44px 0 16px', lineHeight: 1.2 }}>{sec3Title}</h2>
                    <p className="section-body">{sec3Body}</p>

                    <div className="note-box" style={{ marginTop: '30px', borderLeft: '4px solid var(--eh-purple)', paddingLeft: '20px' }}>
                        <h3 className="note-title" style={{ fontFamily: "'Playfair Display',serif", fontSize: '22px', color: 'var(--svi-navy)', marginBottom: '10px' }}>{sec4Title}</h3>
                        <ul style={{ listStyle: 'none', padding: 0 }}>
                            <li style={{ marginBottom: '8px', fontWeight: 300 }}>— {sec4Item1}</li>
                            <li style={{ marginBottom: '8px', fontWeight: 300 }}>— {sec4Item2}</li>
                            <li style={{ marginBottom: '8px', fontWeight: 300 }}>— {sec4Item3}</li>
                        </ul>
                    </div>

                    <p className="section-body" style={{ fontWeight: 600, marginTop: '32px' }}>
                        {closing}
                    </p>
                </>
            )}
        </div>
    );
}
