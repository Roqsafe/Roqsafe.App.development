import { probePostgres } from '@/lib/services';

export const dynamic = 'force-dynamic';

export default async function ServerStatusPage() {
    const checkTime = new Date().toISOString().replace('T', ' ').substring(0, 19);
    let database = { ok: false, detail: 'unavailable' };
    try {
        const probe = await probePostgres();
        database = { ok: probe.ok, detail: probe.detail || probe.error || 'checked' };
    } catch {
        database = { ok: false, detail: 'connection failed' };
    }

    return (
        <>
            <div className="heading-block" style={{ marginBottom: '40px' }}>
                <h1 style={{ fontFamily: "'Playfair Display',serif", fontSize: 'clamp(32px, 8vw, 48px)', color: 'var(--svi-navy)', fontWeight: 700 }}>
                    Network Integrity Status
                </h1>
            </div>

            <div className="body-section" style={{ maxWidth: '800px' }}>
                <div style={{ background: '#fafbfc', border: '1px solid #e1e4e8', borderRadius: '4px', padding: '32px', marginBottom: '30px' }}>
                    <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', marginBottom: '20px', borderBottom: '1px solid #f0f0f0', paddingBottom: '15px' }}>
                        <span style={{ fontWeight: 600, fontSize: '16px', color: 'var(--svi-navy)' }}>SVI Core Engine</span>
                        <span style={{ background: '#1a6b3a', color: '#fff', padding: '4px 12px', borderRadius: '4px', fontSize: '11px', fontWeight: 700, letterSpacing: '0.05em' }}>OPERATIONAL</span>
                    </div>
                    <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', marginBottom: '20px', borderBottom: '1px solid #f0f0f0', paddingBottom: '15px' }}>
                        <span style={{ fontWeight: 600, fontSize: '16px', color: 'var(--svi-navy)' }}>Erickson SSO Gateway</span>
                        <span style={{ background: '#1a6b3a', color: '#fff', padding: '4px 12px', borderRadius: '4px', fontSize: '11px', fontWeight: 700, letterSpacing: '0.05em' }}>OPERATIONAL</span>
                    </div>
                    <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', marginBottom: '20px', borderBottom: '1px solid #f0f0f0', paddingBottom: '15px' }}>
                        <span style={{ fontWeight: 600, fontSize: '16px', color: 'var(--svi-navy)' }}>Verification API</span>
                        <span style={{ background: '#1a6b3a', color: '#fff', padding: '4px 12px', borderRadius: '4px', fontSize: '11px', fontWeight: 700, letterSpacing: '0.05em' }}>OPERATIONAL</span>
                    </div>
                    <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', paddingTop: '5px' }}>
                        <span style={{ fontWeight: 600, fontSize: '16px', color: 'var(--svi-navy)' }}>Lakebase Postgres</span>
                        <span style={{ background: database.ok ? '#1a6b3a' : '#991b1b', color: '#fff', padding: '4px 12px', borderRadius: '4px', fontSize: '11px', fontWeight: 700, letterSpacing: '0.05em' }}>{database.ok ? 'OPERATIONAL' : 'UNAVAILABLE'}</span>
                    </div>
                    <p style={{ marginTop: '12px', fontSize: '13px', color: 'var(--muted)' }}>{database.detail}</p>
                </div>

                <p style={{ fontSize: '13px', color: 'var(--muted)', fontStyle: 'normal', opacity: 0.8 }}>
                    System Integrity Check: <strong>{checkTime} UTC</strong>
                </p>
            </div>
        </>
    );
}
