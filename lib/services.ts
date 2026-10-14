import { complete } from "./ai";
import { query } from "./db";
import { storeText } from "./storage";

export type ServiceProbe = {
    ok: boolean;
    detail?: string;
    error?: string;
};

export async function probePostgres(): Promise<ServiceProbe> {
    const rows = await query<{ pages: number }>(
        "SELECT COUNT(*)::int AS pages FROM svi_content",
    );
    await query(
        "INSERT INTO svi_check_log (ip, target_ip) VALUES ($1, $2)",
        ["127.0.0.1", "neon-probe"],
    );
    const written = await query<{ n: number }>(
        "SELECT COUNT(*)::int AS n FROM svi_check_log WHERE target_ip = $1",
        ["neon-probe"],
    );
    return {
        ok: (rows[0]?.pages ?? 0) > 0 && (written[0]?.n ?? 0) > 0,
        detail: `${rows[0]?.pages ?? 0} content rows, ${written[0]?.n ?? 0} probes`,
    };
}

export async function probeFunction(): Promise<ServiceProbe> {
    const url = process.env.NEON_FUNCTION_HELLO_BASE_URL;
    if (!url) {
        return { ok: false, error: "NEON_FUNCTION_HELLO_BASE_URL is not set" };
    }
    const res = await fetch(url, { cache: "no-store" });
    const body = await res.text();
    if (!res.ok) {
        return { ok: false, error: `function ${res.status}: ${body.slice(0, 200)}` };
    }
    return { ok: true, detail: body.slice(0, 200) };
}

export async function probeAiGateway(): Promise<ServiceProbe> {
    if (!process.env.NEON_AI_GATEWAY_TOKEN || !process.env.NEON_AI_GATEWAY_BASE_URL) {
        return { ok: false, error: "NEON_AI_GATEWAY_TOKEN or NEON_AI_GATEWAY_BASE_URL is not set" };
    }
    const text = await complete("Reply with the single word pong.");
    return { ok: text.toLowerCase().includes("pong"), detail: text.slice(0, 200) };
}

export async function probeStorage(): Promise<ServiceProbe> {
    if (!process.env.AWS_ENDPOINT_URL_S3) {
        return { ok: false, error: "AWS_ENDPOINT_URL_S3 is not set" };
    }
    const key = "uploads/file.txt";
    const url = await storeText(key, "Hello World!");
    const res = await fetch(url, { cache: "no-store" });
    const body = await res.text();
    if (!res.ok || body !== "Hello World!") {
        return { ok: false, error: `storage read ${res.status}: ${body.slice(0, 120)}` };
    }
    return { ok: true, detail: `[view] ${url}` };
}
