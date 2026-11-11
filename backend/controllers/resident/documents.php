<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/bootstrap.php';
require_once __DIR__ . '/context.php';

extract(resident_page_context(), EXTR_SKIP);

$documentTypes = document_types();
$selectedCode = strtolower(trim((string) ($_GET['type'] ?? '')));
$selectedType = $documentTypes[0] ?? null;
foreach ($documentTypes as $documentType) {
    if ($documentType['code'] === $selectedCode) {
        $selectedType = $documentType;
        break;
    }
}
$chipLetters = ['clearance' => 'C', 'indigency' => 'I', 'residency' => 'R'];
$chipClasses = ['clearance' => 'blue', 'indigency' => 'green', 'residency' => 'amber'];
