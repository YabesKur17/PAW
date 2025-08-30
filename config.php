<?php
// === Database ===
define('DB_HOST', 'localhost');
define('DB_NAME', 'pusat_data_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// === App ===
define('APP_NAME', 'DATA PAW GMS SEMARANG');
// Kosongkan jika tidak pakai absolute base. Atau isi '/pusat-data-php' jika di subfolder tsb.
define('BASE_URL', '');
define('UPLOAD_DIR', __DIR__ . '/uploads');
define('MAX_UPLOAD_MB', 20);

// === Admin credentials (plaintext) ===
// Username: admin
// Password: PAWGMSSEMARANG
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'PAWGMSSEMARANG');

// === Pagination ===
define('PAGE_SIZE_DEFAULT', 10);
