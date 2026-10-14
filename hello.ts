import { attachDatabasePool } from "@neon/functions";
import { Pool } from "pg";

const pool = new Pool({
    connectionString: process.env.DATABASE_URL,
    max: 5,
});
attachDatabasePool(pool);

export default {
    async fetch(): Promise<Response> {
        const { rows } = await pool.query<{ pages: number }>(
            "SELECT COUNT(*)::int AS pages FROM svi_content",
        );
        return Response.json({
            ok: true,
            service: "hello",
            pages: rows[0]?.pages ?? 0,
        });
    },
};
