import { NextResponse } from 'next/server';
import { query } from '@/lib/db';
import { getSviSetting, getSviContent } from '@/lib/auth';

export async function POST(req: Request) {
    try {
        const body = await req.json();
        const ip = body.ip ? String(body.ip).trim() : '';
        const checkerIp = req.headers.get('x-forwarded-for')?.split(',')[0].trim() || req.headers.get('x-real-ip') || '127.0.0.1';

        if (!ip) {
            return NextResponse.json({ status: 'error', message: 'No IP address provided.' }, { status: 400 });
        }

        // Rate limiting
        const maxAttempts = parseInt(await getSviSetting('max_check_attempts', '10'));
        const autoBlock = await getSviSetting('auto_block_on_exceed', '1') === '1';

        let todayChecks = 0;
        if (checkerIp !== '::1' && checkerIp !== '127.0.0.1' && checkerIp !== 'localhost') {
            const checkCounts = await query<{ cnt: number }>(
                "SELECT COUNT(*)::int AS cnt FROM svi_check_log WHERE ip = $1 AND created_at::date = CURRENT_DATE",
                [checkerIp]
            );
            todayChecks = checkCounts[0]?.cnt || 0;
        }

        if (todayChecks >= maxAttempts) {
            if (autoBlock) {
                await query(
                    `INSERT INTO svi_blocked_ips (ip, block_type, reason) VALUES ($1, $2, $3)
                     ON CONFLICT (ip) DO UPDATE SET block_type = EXCLUDED.block_type, reason = EXCLUDED.reason, updated_at = NOW()`,
                    [checkerIp, 'soft', 'Auto-blocked: exceeded daily check limit']
                );
            }
            return NextResponse.json({ status: 'error', message: 'Too many requests. You have exceeded the daily check limit.' });
        }

        // Log this check
        await query("INSERT INTO svi_check_log (ip, target_ip) VALUES ($1, $2)", [checkerIp, ip]);

        const content = await getSviContent('main');
        const txt = (key: string) => content[key] || '';

        // Lookup IP
        const isLocal = ip === '::1' || ip === '127.0.0.1' || ip === 'localhost';
        const q1 = isLocal
            ? "SELECT * FROM svi_blocked_ips WHERE ip IN ('::1', '127.0.0.1', 'localhost') ORDER BY id DESC LIMIT 1"
            : "SELECT * FROM svi_blocked_ips WHERE ip = $1";
        const params1 = isLocal ? [] : [ip];

        const rows = await query<{
            id: number;
            ip: string;
            block_type: string;
            reason: string | null;
            deblock_req: boolean;
            deblocked: boolean;
            blocked_website: string | null;
        }>(q1, params1);
        let record = rows[0];

        if (!record) {
            const q2 = isLocal
                ? "SELECT * FROM blocked_ips WHERE ip_address IN ('::1', '127.0.0.1', 'localhost') AND active = true ORDER BY id DESC LIMIT 1"
                : "SELECT * FROM blocked_ips WHERE ip_address = $1 AND active = true ORDER BY id DESC LIMIT 1";
            const params2 = isLocal ? [] : [ip];
            const legacyRows = await query<{
                id: number;
                ip_address: string;
                reason: string | null;
            }>(q2, params2);
            if (legacyRows[0]) {
                record = {
                    id: legacyRows[0].id,
                    ip: legacyRows[0].ip_address,
                    block_type: 'permanent',
                    reason: legacyRows[0].reason,
                    deblock_req: false,
                    deblocked: false,
                    blocked_website: null
                };
            }
        }

        if (!record) {
            return NextResponse.json({ status: 'not_found', message: txt('result_not_found') });
        }

        if (record.deblocked) {
            return NextResponse.json({ status: 'deblocked', message: txt('result_deblocked') });
        }

        const urlStr = record.blocked_website || 'our platform';
        
        if (record.block_type === 'permanent') {
            const msg = `Suspicious activities detected on ${urlStr} involving your IP address. Please verify.<br><br>${txt('result_perm_blocked')}`;
            return NextResponse.json({ status: 'permanent', message: msg });
        }

        // Soft block
        const msgSoft = `Suspicious activities detected on ${urlStr} involving your IP address. Please verify.<br><br>${txt('result_soft_blocked')}`;
        return NextResponse.json({
            status: 'soft',
            message: msgSoft,
            deblock_requested: !!record.deblock_req
        });
        
    } catch (e) {
        console.error('Block check API Error:', e);
        return NextResponse.json({ status: 'error', message: 'Internal server error' }, { status: 500 });
    }
}
