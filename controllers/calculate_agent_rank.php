<?php
require_once __DIR__ . '/../crest/crest.php';
require_once __DIR__ . '/../crest/settings.php';

include_once __DIR__ . '/../data/fetch_deals.php';
include_once __DIR__ . '/../data/fetch_users.php';
include_once __DIR__ . '/../utils/index.php';

function calculateAgentRank()
{
    $currentYear = date('Y');
    $startYear = $currentYear - 4;
    $cacheFile = __DIR__ . '/../cache/global_ranking_cache.json';

    if (file_exists($cacheFile)) {
        return json_decode(file_get_contents($cacheFile), true);
    }

    $globalRanking = initializeGlobalRanking($startYear, $currentYear);

    $dealFilters = [
        '>BEGINDATE' => "$startYear-01-01",
        '<=BEGINDATE' => "$currentYear-12-31",
    ];
    $dealSelects = ['BEGINDATE', 'ASSIGNED_BY_ID', 'UF_CRM_1591696067251'];
    $dealOrders = ['UF_CRM_1591696067251' => 'DESC', 'BEGINDATE' => 'DESC'];

    $sortedDeals = getFilteredDeals($dealFilters, $dealSelects, $dealOrders);
    storeAgentsData($sortedDeals, $globalRanking);

    $agents = getUsers();
    storeRemainingAgents($agents, $globalRanking, $startYear, $currentYear);

    foreach (['monthwise_rank', 'quarterly_rank', 'yearly_rank'] as $rankType) {
        assignRank($globalRanking, $rankType);
    }

    file_put_contents($cacheFile, json_encode($globalRanking));
    return $globalRanking;
}

function initializeGlobalRanking($startYear, $endYear)
{
    $ranking = [];
    $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    $quarters = ['Q1', 'Q2', 'Q3', 'Q4'];

    for ($year = $startYear; $year <= $endYear; $year++) {
        $ranking[$year] = [
            'monthwise_rank' => array_fill_keys($months, []),
            'quarterly_rank' => array_fill_keys($quarters, []),
            'yearly_rank' => []
        ];
    }

    return $ranking;
}

function storeAgentsData($deals, &$ranking)
{
    foreach ($deals as $deal) {
        $agentId = $deal['ASSIGNED_BY_ID'];
        $agent = getUser($agentId);
        $year = date('Y', strtotime($deal['BEGINDATE']));
        $month = date('M', strtotime($deal['BEGINDATE']));
        $quarter = get_quarter($month);
        $grossComms = isset($deal['UF_CRM_1591696067251']) ? (int)explode('|', $deal['UF_CRM_1591696067251'])[0] : 0;
        $name = ($agent['NAME'] ?? '') . ' ' . ($agent['LAST_NAME'] ?? '');

        foreach (['monthwise_rank' => $month, 'quarterly_rank' => $quarter] as $rankType => $period) {
            $ranking[$year][$rankType][$period][$agentId]['name'] = $name;
            $ranking[$year][$rankType][$period][$agentId]['gross_comms'] =
                ($ranking[$year][$rankType][$period][$agentId]['gross_comms'] ?? 0) + $grossComms;
        }

        $ranking[$year]['yearly_rank'][$agentId]['name'] = $name;
        $ranking[$year]['yearly_rank'][$agentId]['gross_comms'] =
            ($ranking[$year]['yearly_rank'][$agentId]['gross_comms'] ?? 0) + $grossComms;
    }
}

function storeRemainingAgents($agents, &$ranking, $startYear, $endYear)
{
    for ($year = $startYear; $year <= $endYear; $year++) {
        foreach (['monthwise_rank', 'quarterly_rank'] as $rankType) {
            foreach ($ranking[$year][$rankType] as $period => &$periodData) {
                foreach ($agents as $id => $agent) {
                    if (!isset($periodData[$id])) {
                        $periodData[$id] = [
                            'id' => $agent['ID'],
                            'name' => $agent['NAME'] ?? '',
                            'gross_comms' => 0
                        ];
                    }
                }
            }
        }

        foreach ($agents as $id => $agent) {
            if (!isset($ranking[$year]['yearly_rank'][$id])) {
                $ranking[$year]['yearly_rank'][$id] = [
                    'id' => $agent['ID'],
                    'name' => $agent['NAME'] ?? '',
                    'gross_comms' => 0
                ];
            }
        }
    }
}

function assignRank(&$ranking, $rankType)
{
    foreach ($ranking as $year => &$yearData) {
        foreach ($yearData[$rankType] as $period => &$agents) {
            if (!is_array($agents)) continue;

            uasort($agents, fn($a, $b) => ($b['gross_comms'] ?? 0) <=> ($a['gross_comms'] ?? 0));

            $rank = 1;
            $previousComms = null;
            foreach ($agents as &$agent) {
                if (!is_array($agent)) continue;

                if ($previousComms !== null && $agent['gross_comms'] === $previousComms) {
                    $agent['rank'] = $rank;
                } else {
                    $agent['rank'] = $rank;
                    $previousComms = $agent['gross_comms'];
                }
                $rank++;
            }
        }
    }
}
