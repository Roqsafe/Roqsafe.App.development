import { query } from "./db";

interface BlockRow {
    id: number;
    ip: string;
    ip_address?: string;
    block_type: string;
    reason: string | null;
    deblock_req: boolean;
    deblocked: boolean;
    blocked_website: string | null;
    updated_at?: Date | string | null;
}

interface ContentRow {
    key: string;
    value: string;
}

interface SettingRow {
    value: string;
}

interface CountRow {
    misses?: number;
    cnt?: number;
}

export async function checkGlobalBlock(ip: string) {
    if (!ip) return null;

    const ips = (ip === "::1" || ip === "127.0.0.1" || ip === "localhost")
        ? ["::1", "127.0.0.1", "localhost"]
        : [ip];

    try {
        const rows = await query<BlockRow>(
            "SELECT * FROM svi_blocked_ips WHERE ip = ANY($1::text[]) AND deblocked = false",
            [ips],
        );
        if (rows.length > 0) return rows[0];

        const legacyRows = await query<BlockRow>(
            "SELECT * FROM blocked_ips WHERE ip_address = ANY($1::text[]) AND active = true",
            [ips],
        );
        if (legacyRows.length > 0) {
            const r2 = legacyRows[0];
            return {
                id: r2.id,
                ip: r2.ip_address ?? "",
                block_type: "permanent",
                reason: r2.reason,
                deblock_req: false,
                deblocked: false,
                blocked_website: null,
            } as BlockRow;
        }
        return null;
    } catch (e) {
        console.error("DB error during block check:", e);
        return null;
    }
}

export async function handle404Autoblock(ip: string) {
    if (!ip) return;

    try {
        const records = await query<BlockRow>(
            "SELECT * FROM svi_blocked_ips WHERE ip = $1",
            [ip],
        );
        const record = records[0];

        if (record && record.block_type === "permanent") return;

        await query(
            "INSERT INTO svi_check_log (ip, target_ip, created_at) VALUES ($1, '404_NOT_FOUND', NOW())",
            [ip],
        );

        const counts = await query<CountRow>(
            "SELECT COUNT(*)::int AS misses FROM svi_check_log WHERE ip = $1 AND target_ip = '404_NOT_FOUND' AND created_at::date = CURRENT_DATE",
            [ip],
        );
        const misses = counts[0]?.misses || 0;

        if (misses >= 3) {
            await query(
                `INSERT INTO svi_blocked_ips (ip, block_type, reason)
                 VALUES ($1, 'soft', 'Auto-blocked: multiple unknown route access attempts')
                 ON CONFLICT (ip) DO UPDATE SET
                   block_type = 'soft',
                   reason = 'Auto-blocked: multiple unknown route access attempts',
                   deblocked = false,
                   deblock_req = false,
                   updated_at = NOW()`,
                [ip],
            );
        }
    } catch (e) {
        console.error("DB error during autoblock:", e);
    }
}

export async function getSviContent(page: string = "main"): Promise<Record<string, string>> {
    try {
        const rows = await query<ContentRow>(
            `SELECT "key", "value" FROM svi_content WHERE page = $1`,
            [page],
        );
        const content: Record<string, string> = {};
        for (const row of rows) {
            content[row.key] = row.value;
        }
        return content;
    } catch (e) {
        console.error("Error fetching content:", e);
        return {};
    }
}

export async function getSviSetting(key: string, defaultVal: string = ""): Promise<string> {
    try {
        const rows = await query<SettingRow>(
            `SELECT "value" FROM svi_settings WHERE setting_key = $1`,
            [key],
        );
        if (rows.length > 0) return rows[0].value;
        return defaultVal;
    } catch (e) {
        console.error("Error fetching setting:", e);
        return defaultVal;
    }
}
