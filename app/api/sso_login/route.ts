import { NextResponse } from 'next/server';
import { getDb } from '@/lib/db';
import { RowDataPacket } from 'mysql2/promise';

export async function POST(req: Request) {
    try {
        const data = await req.json();

        if (!data || !data.email) {
            return NextResponse.json({ success: false, error: 'Invalid SSO payload received.' }, { status: 400 });
        }

        const email = String(data.email).toLowerCase().trim();
        const clientIp = req.headers.get('x-forwarded-for')?.split(',')[0].trim() || req.headers.get('x-real-ip') || '127.0.0.1';
        const ua = req.headers.get('user-agent')?.substring(0, 512) || '';
        
        const db = getDb();

        // Domain Validation
        if (!email.endsWith('@ericksonholding.com')) {
            await db.execute(
                "INSERT INTO `svi_login_log` (`username`,`ip`,`success`,`user_agent`) VALUES (?,?,0,?)",
                [email, clientIp, ua]
            );
            return NextResponse.json({ success: false, error: 'Only authorized Erickson Holding emails can log in. Access Denied.' });
        }

        // Admin Verification
        const [rows] = await db.execute<RowDataPacket[]>("SELECT * FROM `svi_admins` WHERE `email` = ?", [email]);
        const admin = rows[0];

        if (admin) {
            if (admin.locked_until && new Date() < new Date(admin.locked_until)) {
                const unlockAt = new Date(admin.locked_until).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                return NextResponse.json({ success: false, error: `Account temporarily locked due to previous failed attempts. Try again after ${unlockAt}.` });
            }

            await db.execute(
                "UPDATE `svi_admins` SET `login_attempts`=0, `locked_until`=NULL, `last_login`=NOW() WHERE `id`=?",
                [admin.id]
            );
            await db.execute(
                "INSERT INTO `svi_login_log` (`username`,`ip`,`success`,`user_agent`) VALUES (?,?,1,?)",
                [email, clientIp, ua]
            );

            // Set session cookie
            const response = NextResponse.json({ success: true, redirect: '/' });
            
            // Simple session payload, in production use JWT or secure iron-session
            const sessionPayload = Buffer.from(JSON.stringify({ admin_id: admin.id, username: admin.username, timestamp: Date.now() })).toString('base64');
            
            response.cookies.set('svi_admin', sessionPayload, {
                path: '/',
                httpOnly: true,
                secure: process.env.NODE_ENV === 'production',
                sameSite: 'strict'
            });

            return response;
        } else {
            await db.execute(
                "INSERT INTO `svi_login_log` (`username`,`ip`,`success`,`user_agent`) VALUES (?,?,0,?)",
                [email, clientIp, ua]
            );
            return NextResponse.json({ success: false, error: 'No SVI admin account found for this email address.' });
        }

    } catch (e) {
        console.error('SSO Login API Error:', e);
        return NextResponse.json({ success: false, error: 'Internal server error' }, { status: 500 });
    }
}
