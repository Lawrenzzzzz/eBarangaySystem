<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/bootstrap.php';
require_once __DIR__ . '/context.php';

extract(resident_page_context('../login_page/login.php'), EXTR_SKIP);
