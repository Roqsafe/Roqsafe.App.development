'use client';

import Link from 'next/link';

export default function Footer({ clientIp }: { clientIp: string }) {
    return (
        <footer style={{ borderTop: '1px solid var(--border)', padding: '32px 28px', marginTop: '60px', background: '#fff' }}>
            <div style={{ maxWidth: '1200px', margin: '0 auto', display: 'flex', justifyContent: 'space-between', alignItems: 'center', flexWrap: 'wrap', gap: '20px' }}>
                
                <div style={{ fontSize: '12px', fontWeight: 400, color: 'var(--muted)', letterSpacing: '0.02em', lineHeight: 1.5 }}>
                    © 2021 - {new Date().getFullYear()} Roqsafe LTD . All rights reserved.
                </div>

                <div style={{ display: 'flex', gap: '24px', alignItems: 'center' }}>
                    <span style={{ color: 'var(--muted)', fontSize: '13px', fontWeight: 400, paddingRight: '12px', borderRight: '1px solid var(--border)' }}>
                        IP: {clientIp || 'Unknown'}
                    </span>
                    <Link href="/About" style={{ color: 'var(--svi-navy)', textDecoration: 'none', fontSize: '13px', fontWeight: 500, transition: 'color 0.2s' }}>About Us</Link>
                    <Link href="/TorContribution" style={{ color: 'var(--svi-navy)', textDecoration: 'none', fontSize: '13px', fontWeight: 500, transition: 'color 0.2s' }}>Tor Contribution</Link>
                    <Link href="/ServerStatus" style={{ color: 'var(--svi-navy)', textDecoration: 'none', fontSize: '13px', fontWeight: 500, transition: 'color 0.2s' }}>Server Status</Link>
                </div>

            </div>
        </footer>
    );
}
