type ModelEntry = {
    id: string;
    enabled?: boolean;
    architecture?: { output_modalities?: string[] };
};

export async function complete(prompt: string): Promise<string> {
    const token = process.env.NEON_AI_GATEWAY_TOKEN;
    const base = process.env.NEON_AI_GATEWAY_BASE_URL;
    if (!token || !base) {
        throw new Error("NEON_AI_GATEWAY_TOKEN and NEON_AI_GATEWAY_BASE_URL are not set");
    }

    const modelsRes = await fetch(`${base}/v1/models`, {
        headers: { Authorization: `Bearer ${token}` },
        cache: "no-store",
    });
    if (!modelsRes.ok) {
        throw new Error(`AI Gateway models request failed (${modelsRes.status})`);
    }
    const catalog = (await modelsRes.json()) as { data?: ModelEntry[] };
    const model =
        catalog.data?.find(
            (entry) =>
                entry.enabled !== false &&
                (entry.architecture?.output_modalities ?? ["text"]).includes("text"),
        )?.id ?? "gpt-5-mini";

    const res = await fetch(`${base}/v1/chat/completions`, {
        method: "POST",
        headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            model,
            messages: [{ role: "user", content: prompt }],
        }),
        cache: "no-store",
    });
    if (!res.ok) {
        const detail = await res.text();
        throw new Error(`AI Gateway chat failed (${res.status}): ${detail.slice(0, 300)}`);
    }
    const data = (await res.json()) as {
        choices?: { message?: { content?: string } }[];
    };
    const text = data.choices?.[0]?.message?.content?.trim();
    if (!text) {
        throw new Error("AI Gateway returned an empty completion");
    }
    return text;
}
