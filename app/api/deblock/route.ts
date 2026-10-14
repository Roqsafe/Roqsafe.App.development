import { NextResponse } from 'next/server';
import { query } from '@/lib/db';
import { getSviSetting } from '@/lib/auth';

export async function POST(req: Request) {
    try {
        const body = await req.json();
        const ip = body.ip ? String(body.ip).trim() : '';

        if (!ip) {
            return NextResponse.json({ status: 'error', message: 'No IP address provided.' }, { status: 400 });
        }

        const allowDeblock = await getSviSetting('allow_deblock', '1');
        if (allowDeblock !== '1') {
            return NextResponse.json({ status: 'error', message: 'Deblock requests are currently not accepted. Please contact support directly.' });
        }

        const rows = await query<{
            block_type: string;
            deblocked: boolean;
            deblock_req: boolean;
            updated_at: Date | string | null;
        }>("SELECT * FROM svi_blocked_ips WHERE ip = $1", [ip]);
        const record = rows[0];

        if (!record) {
            return NextResponse.json({ status: 'not_found', message: 'This IP address was not found in our records.' });
        }

        if (record.block_type === 'permanent') {
            return NextResponse.json({ status: 'permanent', message: 'This IP address has been permanently blocked. This decision is final and cannot be reversed. No deblock request can be submitted.' });
        }

        if (record.deblocked) {
            return NextResponse.json({ status: 'already', message: 'This IP address is already deblocked. No further action is required.' });
        }

        const cooldownHours = parseInt(await getSviSetting('deblock_cooldown_hours', '24'));
        if (record.deblock_req && cooldownHours > 0) {
            const updatedAt = new Date(record.updated_at ?? Date.now()).getTime();
            const now = new Date().getTime();
            const hoursSince = (now - updatedAt) / (1000 * 60 * 60);

            if (hoursSince < cooldownHours) {
                const remaining = Math.ceil(cooldownHours - hoursSince);
                return NextResponse.json({ status: 'pending', message: `A deblock request for this IP is already pending review. Please wait approximately ${remaining} hour(s) before resubmitting.` });
            }
        }

        await query("UPDATE svi_blocked_ips SET deblock_req = true, updated_at = NOW() WHERE ip = $1", [ip]);

        return NextResponse.json({ status: 'success', message: 'Your deblock request has been submitted successfully. Our team will review your case and contact you if additional information is required.' });

    } catch (e) {
        console.error('Deblock API Error:', e);
        return NextResponse.json({ status: 'error', message: 'Internal server error' }, { status: 500 });
    }
}
