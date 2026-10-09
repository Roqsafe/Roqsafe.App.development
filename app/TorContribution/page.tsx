export default function TorContributionPage() {
    return (
        <>
            <div className="heading-block" style={{ marginBottom: '40px' }}>
                <h1 style={{ fontFamily: "'Playfair Display',serif", fontSize: 'clamp(32px, 8vw, 48px)', color: 'var(--svi-navy)', fontWeight: 700 }}>
                    Tor Network Commitment
                </h1>
            </div>

            <div className="body-section" style={{ maxWidth: '800px' }}>
                <p style={{ fontSize: '16px', lineHeight: 1.8, color: 'var(--muted)', marginBottom: '24px' }}>
                    At Roqsafe, we recognize the essential role that anonymity networks play in global digital privacy. Our commitment to the Tor Project includes technical contributions and network integrity support.
                </p>
                <p style={{ fontSize: '16px', lineHeight: 1.8, color: 'var(--muted)' }}>
                    Through our monitoring and verification tools, we aim to ensure the SVI ecosystem remains a safe and reliable gateway for privacy-conscious users worldwide. We believe that privacy is a fundamental human right and strive to protect it through advanced security inspections.
                </p>
            </div>
        </>
    );
}
