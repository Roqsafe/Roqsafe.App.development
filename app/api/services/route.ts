import { NextResponse } from "next/server";
import { probeAiGateway, probeFunction, probePostgres, probeStorage } from "@/lib/services";

export const dynamic = "force-dynamic";

async function run(name: string, probe: () => Promise<{ ok: boolean; detail?: string; error?: string }>) {
    try {
        return await probe();
    } catch (error) {
        const message = error instanceof Error ? error.message : "probe failed";
        console.error(`[services] ${name}:`, message);
        return { ok: false, error: message };
    }
}

export async function GET() {
    const postgres = await run("postgres", probePostgres);
    const functions = await run("function", probeFunction);
    const aiGateway = await run("aiGateway", probeAiGateway);
    const storage = await run("storage", probeStorage);
    const results = { postgres, functions, aiGateway, storage };
    const ok = Object.values(results).every((result) => result.ok);
    return NextResponse.json({ ok, results }, { status: ok ? 200 : 503 });
}
