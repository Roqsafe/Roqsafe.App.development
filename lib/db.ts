import "dotenv/config";
import { config as loadEnv } from "dotenv";
import { Pool, type QueryResultRow } from "pg";

loadEnv({ path: ".env.local" });

let pool: Pool | null = null;

export function getDb(): Pool {
    if (!pool) {
        const connectionString = process.env.DATABASE_URL;
        if (!connectionString) {
            throw new Error("DATABASE_URL is not set");
        }
        pool = new Pool({
            connectionString,
            max: 10,
        });
    }
    return pool;
}

export async function query<T extends QueryResultRow = QueryResultRow>(
    text: string,
    params: unknown[] = [],
): Promise<T[]> {
    const result = await getDb().query<T>(text, params);
    return result.rows;
}
