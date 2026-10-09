import type { Metadata } from 'next';
import { headers } from 'next/headers';
import './globals.css';
import Header from '@/components/Header';
import Footer from '@/components/Footer';
import { checkGlobalBlock, getSviSetting } from '@/lib/auth';

export const metadata: Metadata = {
  title: 'EH.SVI | Security and Verification Inspector',
  description: 'SVI – Security and Verification Inspector by Roqsafe LTD. Check your security status.',
};

export default async function RootLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  const headersList = await headers();
  const forwardedFor = headersList.get('x-forwarded-for');
  const realIp = headersList.get('x-real-ip');
  
  // Basic IP extraction
  const clientIp = realIp || (forwardedFor ? forwardedFor.split(',')[0].trim() : '127.0.0.1');

  // Check block status
  const blockRecord = await checkGlobalBlock(clientIp);

  if (blockRecord) {
    return (
      <html lang="en">
        <body>
          <div style={{ padding: '50px', textAlign: 'center', fontFamily: 'Inter, sans-serif' }}>
            <h1 style={{ color: '#991b1b' }}>Access Denied</h1>
            <p style={{ color: '#5a6070', marginTop: '20px' }}>
              Your IP address ({clientIp}) has been blocked from accessing this system.
            </p>
            {blockRecord.reason && (
              <p style={{ marginTop: '10px', fontSize: '12px', color: '#888' }}>
                Reason: {blockRecord.reason}
              </p>
            )}
            <p style={{ marginTop: '30px' }}>
              If you believe this is an error, please contact security support.
            </p>
          </div>
        </body>
      </html>
    );
  }

  // Get site notice if any
  const siteNotice = await getSviSetting('site_notice', '');

  return (
    <html lang="en">
      <body>
        <div className="page-wrap">
          <Header siteNotice={siteNotice} />
          <main className="page-container">
            {children}
          </main>
          <Footer clientIp={clientIp} />
        </div>
      </body>
    </html>
  );
}
