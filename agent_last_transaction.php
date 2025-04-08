<?php
include_once __DIR__ . "/crest/crest.php";
include_once __DIR__ . "/crest/settings.php";
include('includes/header.php');
include_once __DIR__ . "/data/fetch_deals.php";
include_once __DIR__ . "/data/fetch_users.php";
include_once __DIR__ . "/utils/index.php";

$selected_agent_id = $_GET['agent_id'] ?? null;
$page = (int)($_GET['page'] ?? 1);
$selected_year = isset($_GET['year']) ? explode('/', $_GET['year'])[2] : date('Y');

$userFilter = [];
if ($selected_agent_id) $userFilter['ID'] = $selected_agent_id;

$userData = get_paginated_users($page, $userFilter, [], ['NAME' => 'asc']);
$users = $userData['users'] ?? [];
$user_ids = array_column($users, 'ID');

$total_agents = $userData['total'] ?? 0;
$total_pages = ceil($total_agents / 50);

$filter = [
    '>=BEGINDATE' => "$selected_year-01-01",
    '<=BEGINDATE' => "$selected_year-12-31",
    '@ASSIGNED_BY_ID' => $user_ids,
];

$deals = getFilteredDeals($filter) ?? [];
$deal_fields = get_deal_fileds();

$agents = [];

foreach ($users as $user) {
    $agents[$user['ID']] = [
        "id" => $user['ID'],
        "name" => trim("{$user['NAME']} {$user['SECOND_NAME']} {$user['LAST_NAME']}"),
    ];
}

foreach ($deals as $deal) {
    $agentId = $deal['ASSIGNED_BY_ID'];
    $dealDate = date('Y-m-d', strtotime($deal['BEGINDATE']));

    if (!isset($agents[$agentId]["last_deal_date"]) || strtotime($agents[$agentId]["last_deal_date"]) < strtotime($dealDate)) {
        $agents[$agentId]["last_deal_date"] = $dealDate;
        $agents[$agentId]["deal_current_duration"] = duration_months($deal['BEGINDATE']);
    }
}
?>

<div class="flex w-full h-screen">
    <?php include('includes/sidebar.php'); ?>
    <div class="main-content-area flex-1 overflow-y-auto bg-gray-100 dark:bg-gray-900">
        <?php include('includes/navbar.php'); ?>
        <div class="px-8 py-6">
            <?php include('./includes/datepicker.php'); ?>

            <?php if (empty($deals)): ?>
                <div class="h-[65vh] flex justify-center items-center">
                    <h1 class="text-2xl font-bold mb-6 dark:text-white">No data available</h1>
                </div>
            <?php else: ?>
                <div class="p-4 shadow-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg">
                    <div class="mb-1 flex justify-between items-center">
                        <h1 class="text-lg font-semibold text-gray-800 dark:text-gray-400">
                            Last Transaction Of: <?= $selected_agent_id ? $agents[$selected_agent_id]['name'] : 'All Agents' ?>
                        </h1>
                        <div class="flex gap-2">
                            <?php include('./includes/select_agents.php'); ?>
                            <a href="agent_last_transaction.php?year=<?= $_GET['year'] ?? date('m/d/Y') ?>" class="<?= $selected_agent_id ? '' : 'hidden' ?> text-white bg-red-500 hover:bg-red-600 rounded-lg text-sm px-4 py-3 dark:bg-red-600 dark:hover:bg-red-700">
                                <svg class="w-4 h-4 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                <span class="ml-2">Clear Filter</span>
                            </a>
                        </div>
                    </div>

                    <div class="relative mt-4 overflow-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="sticky top-0 bg-gray-50 dark:bg-gray-700 text-xs uppercase text-gray-700 dark:text-gray-400">
                                <tr>
                                    <th class="px-6 py-3">Agent</th>
                                    <th class="px-6 py-3">Last Deal Date</th>
                                    <th class="px-6 py-3">Months Without Closing</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($agents as $agent): ?>
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <td class="px-6 py-2 font-medium text-gray-900 dark:text-white"><?= $agent['name'] ?? 'Undefined' ?></td>
                                        <td class="px-6 py-2"><?= $agent['last_deal_date'] ?? '--' ?></td>
                                        <td class="px-6 py-2"><?= isset($agent['deal_current_duration']) ? $agent['deal_current_duration'] . ' months' : '--' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if (!empty($agents)): ?>
                        <?php include('includes/pagination_control.php'); ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>