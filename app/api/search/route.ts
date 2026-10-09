import { NextResponse } from 'next/server';
import { getDb } from '@/lib/db';
import { RowDataPacket } from 'mysql2/promise';

export async function GET(request: Request) {
    const { searchParams } = new URL(request.url);
    const query = searchParams.get('q') || '';

    if (!query.trim()) {
        return NextResponse.json([]);
    }

    try {
        const db = getDb();
        const searchParam = `%${query}%`;
        const [rows] = await db.execute<RowDataPacket[]>(
            `SELECT DISTINCT page FROM svi_content WHERE page LIKE ? OR \`key\` LIKE ? OR \`value\` LIKE ? LIMIT 5`,
            [searchParam, searchParam, searchParam]
        );

        const formatted = rows.map((row) => {
            const pageName = row.page;
            const url = (pageName === 'main' || pageName === 'home') ? '/' : `/${pageName}`;
            
            return {
                title: pageName.charAt(0).toUpperCase() + pageName.slice(1) + ' Information',
                url,
                description: `Security details regarding ${pageName}.`
            };
        });

        // Special case for project tools
        if ('Project Tools IP Verification'.toLowerCase().includes(query.toLowerCase())) {
            formatted.push({
                title: 'Project Tools',
                url: '/Verifi/data/user/default',
                description: 'IP security and de-blocking tools.'
            });
        }

        return NextResponse.json(formatted);
    } catch (e) {
        console.error('Search API Error:', e);
        return NextResponse.json([]);
    }
}
