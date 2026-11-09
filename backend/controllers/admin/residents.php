<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/bootstrap.php';
require_once __DIR__ . '/guard.php';

$residentDemographics = official_resident_demographics();
$residentPopulationTrend = official_resident_population_trend();
