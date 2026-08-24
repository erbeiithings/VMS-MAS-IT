<?php

// Paksa settingan khusus Vercel agar tidak nulis ke hardisk
putenv('APP_DEBUG=true');
putenv('LOG_CHANNEL=errorlog');
putenv('CACHE_DRIVER=array');
putenv('SESSION_DRIVER=cookie');
putenv('VIEW_COMPILED_PATH=/tmp');

require __DIR__ . '/../public/index.php';