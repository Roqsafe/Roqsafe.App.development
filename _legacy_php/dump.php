<?php require 'lib/env.php'; require 'lib/db.php'; file_put_contents('db_dump.txt', print_r(svi_db()->query('SELECT * FROM svi_blocked_ips')->fetchAll(), true));
