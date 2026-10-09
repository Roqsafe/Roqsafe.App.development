import { getDb } from './db';
import { RowDataPacket } from 'mysql2/promise';

export async function checkGlobalBlock(ip: string) {
    if (!ip) return null;
    
    // Unify local development IP schemas
    const ips = (ip === '::1' || ip === '127.0.0.1' || ip === 'localhost') ? ['::1', '127.0.0.1', 'localhost'] : [ip];
    const placeholders = ips.map(() => '?').join(',');
    
    const db = getDb();
    
    try {
        // Primary Table Check
        const [rows] = await db.execute<RowDataPacket[]>(
            `SELECT * FROM svi_blocked_ips WHERE ip IN (${placeholders}) AND deblocked=0`,
            ips
        );
        if (rows.length > 0) return rows[0];
        
        // External / Legacy Table Check 
        const [legacyRows] = await db.execute<RowDataPacket[]>(
            `SELECT * FROM blocked_ips WHERE ip_address IN (${placeholders}) AND active=1`,
            ips
        );
        if (legacyRows.length > 0) {
            const r2 = legacyRows[0];
            return {
                id: r2.id,
                ip: r2.ip_address,
                block_type: 'permanent',
                reason: r2.reason,
                deblock_req: 0,
                deblocked: 0,
                blocked_website: null
            } as unknown as RowDataPacket;
        }
        return null;
    } catch (e) {
        console.error('DB error during block check:', e);
        return null; // Fail open if DB is down, or handle differently based on requirements
    }
}

export async function handle404Autoblock(ip: string) {
    if (!ip) return;
    
    const db = getDb();
    
    try {
        const [records] = await db.execute<RowDataPacket[]>(
            "SELECT * FROM svi_blocked_ips WHERE ip=?",
            [ip]
        );
        const record = records[0];
        
        if (record && record.block_type === 'permanent') return; // already perm blocked
        
        // Track 404 hit
        await db.execute(
            "INSERT INTO svi_check_log (ip, target_ip, created_at) VALUES (?, '404_NOT_FOUND', NOW())",
            [ip]
        );
        
        // Count recent 404s (e.g., today)
        const [counts] = await db.execute<RowDataPacket[]>(
            "SELECT COUNT(*) as misses FROM svi_check_log WHERE ip=? AND target_ip='404_NOT_FOUND' AND DATE(created_at)=CURDATE()",
            [ip]
        );
        const misses = counts[0]?.misses || 0;
        
        if (misses >= 3) {
            await db.execute(
                "INSERT INTO svi_blocked_ips (ip, block_type, reason) VALUES (?, 'soft', 'Auto-blocked: multiple unknown route access attempts') ON DUPLICATE KEY UPDATE block_type='soft', reason='Auto-blocked: multiple unknown route access attempts', deblocked=0, deblock_req=0",
                [ip]
            );
        }
    } catch (e) {
        console.error('DB error during autoblock:', e);
    }
}

export async function getSviContent(page: string = 'main'): Promise<Record<string, string>> {
    const db = getDb();
    try {
        const [rows] = await db.execute<RowDataPacket[]>(
            "SELECT `key`, `value` FROM svi_content WHERE page = ?",
            [page]
        );
        const content: Record<string, string> = {};
        for (const row of rows) {
            content[row.key] = row.value;
        }
        return content;
    } catch (e) {
        console.error('Error fetching content:', e);
        return {};
    }
}

export async function getSviSetting(key: string, defaultVal: string = ''): Promise<string> {
    const db = getDb();
    try {
        const [rows] = await db.execute<RowDataPacket[]>(
            "SELECT \`value\` FROM svi_settings WHERE setting_key = ?",
            [key]
        );
        if (rows.length > 0) return rows[0].value;
        return defaultVal;
    } catch (e) {
        console.error('Error fetching setting:', e);
        return defaultVal;
    }
}
