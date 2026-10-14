import { NextResponse } from 'next/server';
import { query } from '@/lib/db';

export async function GET(request: Request) {
    const { searchParams } = new URL(request.url);
    const queryText = searchParams.get('q') || '';

    if (!queryText.trim()) {
        return NextResponse.json([]);
    }

    try {
        const searchParam = `%${queryText}%`;
        const rows = await query<{ page: string }>(
            `SELECT DISTINCT page FROM svi_content WHERE page ILIKE $1 OR "key" ILIKE $2 OR "value" ILIKE $3 LIMIT 5`,
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
        if ('Project Tools IP Verification'.toLowerCase().includes(queryText.toLowerCase())) {
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
