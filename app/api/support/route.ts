import { NextResponse } from 'next/server';
import { sendMail } from '@/lib/mail';
import { headers } from 'next/headers';

export async function POST(req: Request) {
    try {
        const data = await req.formData();
        const name = data.get('sp_name') as string;
        const email = data.get('sp_email') as string;
        const topic = data.get('sp_topic') as string;
        let ip = data.get('sp_ip') as string;
        const message = data.get('sp_message') as string;

        if (!name || !email || !message) {
            return NextResponse.json({ success: false, error: 'Missing required fields' }, { status: 400 });
        }

        if (!ip) {
            const headersList = await headers();
            const forwardedFor = headersList.get('x-forwarded-for');
            const realIp = headersList.get('x-real-ip');
            ip = realIp || (forwardedFor ? forwardedFor.split(',')[0].trim() : '127.0.0.1');
        }

        const to = 'security@roqsafe.com';
        const subject = `[Roqsafe SVI Support] ${topic} – from ${name}`;
        const body = `Name: ${name}\nEmail: ${email}\nTopic: ${topic}\nIP Address: ${ip}\n\nMessage:\n${message}`;

        const success = await sendMail(to, subject, body, email);

        if (success) {
            return NextResponse.redirect(new URL('/About?sent=1', req.url));
        } else {
            return NextResponse.redirect(new URL('/About?error=1', req.url));
        }
    } catch (e) {
        console.error('Support API error:', e);
        return NextResponse.redirect(new URL('/About?error=1', req.url));
    }
}
