import mysql from 'mysql2/promise';

let pool: mysql.Pool | null = null;
let remotePool: mysql.Pool | null = null;

export const getDb = () => {
    if (!pool) {
        pool = mysql.createPool({
            host: process.env.DB_HOST || 'localhost',
            user: process.env.DB_USER || 'root',
            password: process.env.DB_PASS || '',
            database: process.env.DB_NAME || 'svi_db',
            waitForConnections: true,
            connectionLimit: 10,
            queueLimit: 0
        });
    }
    return pool;
};

export const getRemoteDb = () => {
    if (!remotePool) {
        remotePool = mysql.createPool({
            host: process.env.DB_REMOTE_HOST || 'localhost',
            user: process.env.DB_REMOTE_USER || 'erichmsx_re_Database_hmsx_Roqs',
            password: process.env.DB_REMOTE_PASS || 't0OmxbPpPb03',
            database: process.env.DB_REMOTE_NAME || 'erichmsx_Roqsafe',
            waitForConnections: true,
            connectionLimit: 10,
            queueLimit: 0
        });
    }
    return remotePool;
};
