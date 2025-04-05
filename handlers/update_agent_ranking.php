<?php
include_once __DIR__ . '/../crest/crest.php';
include_once __DIR__ . '/../crest/settings.php';

include_once __DIR__ . '/../utils/index.php';
include_once __DIR__ . '/../controllers/calculate_agent_rank.php';

// Enable error logging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    clearCache('global_ranking_cache.json');
}

calculateAgentRank();
