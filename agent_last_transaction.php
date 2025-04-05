<?php
include_once __DIR__ . "/crest/crest.php";
include_once __DIR__ . "/crest/settings.php";
include('includes/header.php');

// include the fetch deals page
include_once __DIR__ . "/data/fetch_deals.php";
include_once __DIR__ . "/data/fetch_users.php";

// utility functions
include_once __DIR__ . "/utils/index.php";

$selected_agent_id = isset($_GET['agent_id']) ? $_GET['agent_id'] : null;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

$userFilter = $selected_agent_id ? ['id' => $selected_agent_id, 'UF_DEPARTMENT' => [5, 78, 77, 440]] : ['UF_DEPARTMENT' => [5, 78, 77, 440]]; // get sales department members only

$userData = get_paginated_users($page, $userFilter);
$users = $userData['users'] ?? [];

$user_ids = array_column($users, 'ID');

// echo "<pre>";
// print_r($user_ids);
// echo "</pre>";

// pagination
$total_agents = $userData['total'] ?? 0;
$total_pages = ceil($total_agents / 50);

// get the filter data from get request
$selected_year = isset($_GET['year']) ? explode('/', $_GET['year'])[2] : date('Y');

$filter = [
    // 'CATEGORY_ID' => 0,
    '>=BEGINDATE' => "$selected_year-01-01",
    '<=BEGINDATE' => "$selected_year-12-31",
    '@ASSIGNED_BY_ID' => [...$user_ids], // get the deals of the specific user type, i.e - sales
];


$deals = getFilteredDeals($filter) ?? [];

$user_fields = get_user_fields();
$deal_fields = get_deal_fileds();

$agents = [];

if (!empty($deals)) {
    // fetch details from the users
    foreach ($users as $user) {
        $agents[$user['ID']]["id"] = $user['ID'];
        $agents[$user['ID']]["first_name"] = $user['NAME'] ?? '';
        $agents[$user['ID']]["last_name"] = $user['LAST_NAME'] ?? '';
        $agents[$user['ID']]["middle_name"] = $user['SECOND_NAME'] ?? '';
    }

    //fetch details from the deals
    foreach ($deals as $deal) {
        if (isset($agents[$deal['ASSIGNED_BY_ID']]["last_deal_date"])) {
            // Only update if the current deal is latest one
            if (strtotime($agents[$deal['ASSIGNED_BY_ID']]["last_deal_date"]) < date('Y-m-d', strtotime($deal['BEGINDATE']))) {
                $agents[$deal['ASSIGNED_BY_ID']]["last_deal_date"] = date('Y-m-d', strtotime($deal['BEGINDATE'])) ?? null;

                if (isset($deal['UF_CRM_1727854555607'])) {
                    $teamName = map_enum($deal_fields, 'UF_CRM_1727854555607', $deal['UF_CRM_1727854555607']);
                    $agents[$deal['ASSIGNED_BY_ID']]["team"] = $teamName ?? null;
                } else {
                    $agents[$deal['ASSIGNED_BY_ID']]["team"] = 'field_not_defined';
                }

                $agents[$deal['ASSIGNED_BY_ID']]["project"] = $deal['UF_CRM_660FC429E780D'] ?? null;
                $agents[$deal['ASSIGNED_BY_ID']]["amount"] = $deal['OPPORTUNITY'] ?? null;
                $agents[$deal['ASSIGNED_BY_ID']]["gross_comms"] = $deal['UF_CRM_1591696067251'] ?? null;
                //get the duration from the current date
                $duration = duration_months($deal['BEGINDATE']);
                $agents[$deal['ASSIGNED_BY_ID']]["deal_current_duration"] = $duration ?? null;
            }
        } else {
            $agents[$deal['ASSIGNED_BY_ID']]["last_deal_date"] = date('Y-m-d', strtotime($deal['BEGINDATE'])) ?? null;

            // map team value
            $team = map_enum($deal_fields, 'UF_CRM_1727854555607', $deal['UF_CRM_1727854555607']);
            $agents[$deal['ASSIGNED_BY_ID']]["team"] = $team ?? null;

            $agents[$deal['ASSIGNED_BY_ID']]["project"] = $deal['UF_CRM_660FC429E780D'] ?? null;
            $agents[$deal['ASSIGNED_BY_ID']]["amount"] = $deal['OPPORTUNITY'] ?? null;
            $agents[$deal['ASSIGNED_BY_ID']]["gross_comms"] = $deal['UF_CRM_1591696067251'] ?? null;

            $duration = duration_months($deal['BEGINDATE']);
            $agents[$deal['ASSIGNED_BY_ID']]["deal_current_duration"] = $duration ?? null;
        }
    }
}

echo "<pre>";
// print_r($deals);
// print_r($deal_fields);
// print_r($user_fields);
// print_r($users);
// print_r($agents);
echo "</pre>";
?>

<div class="flex w-full h-screen">
    <?php include('includes/sidebar.php'); ?>
    <div class="main-content-area flex-1 overflow-y-auto bg-gray-100 dark:bg-gray-900"> <?php include('includes/navbar.php'); ?>
        <div class="px-8 py-6">
            <!-- date picker -->
            <?php include('./includes/datepicker.php'); ?>

            <?php if (empty($deals)): ?>
                <div class="h-[65vh] flex justify-center items-center">
                    <h1 class="text-2xl font-bold mb-6 dark:text-white">No data available</h1>
                </div>
            <?php else: ?>
                <div class="p-4 shadow-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg">
                    <div class="mb-1 flex justify-between items-center">
                        <?php
                        if ($selected_agent_id) {
                            $selected_agent = $agents[$selected_agent_id];
                            $selected_agent_fullname = $selected_agent['first_name'] . ' ' . $selected_agent['middle_name'] . ' ' . $selected_agent['last_name'];
                        } else {
                            $selected_agent_fullname = 'All Agents';
                        }
                        ?>
                        <h1 class="text-lg ms-2 font-semibold text-gray-800 dark:text-gray-400">Last Transaction Of : <?= $selected_agent_fullname ?></h1>
                        <div class="py-2 flex gap-2">
                            <!-- search bar -->
                            <?php include('./includes/select_agents.php'); ?>
                            <!-- clear filter button -->
                            <div class="flex justify-center items-center">
                                <a href="agent_last_transaction.php?year=<?= $_GET['year'] ?? date('m/d/Y') ?>" id="clearFilterButton" class="<?= $selected_agent_id ? '' : 'hidden' ?> text-white bg-red-500 hover:bg-red-600 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-4 py-3 text-center inline-flex items-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800" type="button">
                                    <svg class="w-4 h-4" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    <p class="ml-2">Clear Filter</p>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="pb-4 rounded-lg border-0 bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 rounded-lg">
                        <!-- table -->
                        <div class="relative rounded-lg border-b border-gray-200 dark:border-gray-700 w-full overflow-auto">
                            <table class="w-full h-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                <thead class="sticky top-0 text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">Agent</th>
                                        <th scope="col" class="px-6 py-3 w-[150px]">Last Deal Date</th>
                                        <th scope="col" class="px-6 py-3">Project</th>
                                        <th scope="col" class="px-6 py-3">Amount</th>
                                        <th scope="col" class="px-6 py-3">Gross Comms</th>
                                        <th scope="col" class="px-6 py-3">No. of Months without Closing</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($agents as $agent) : ?>
                                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                            <th scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                <?php
                                                if (isset($agent['first_name'], $agent['last_name'], $agent['middle_name'])) {
                                                    $agent_fullname = $agent['first_name'] . ' ' . $agent['middle_name'] . ' ' . $agent['last_name'];
                                                    echo $agent_fullname;
                                                } else {
                                                    echo "Undefined";
                                                }
                                                ?>
                                            </th>
                                            <td class="px-6 py-4">
                                                <?= $agent['last_deal_date'] ?? '--' ?>
                                            </td>
                                            <td class="px-6 py-4">
                                                <?= $agent['project'] ?? '--' ?>
                                            </td>
                                            <td class="px-6 py-4">
                                                <?= $agent['amount'] ?? '--' ?>
                                            </td>
                                            <td class="px-6 py-4">
                                                <?= $agent['gross_comms'] ?? '--' ?>
                                            </td>
                                            <td class="px-6 py-4">
                                                <?= isset($agent['deal_current_duration']) ? $agent['deal_current_duration'] . ' months' : '--' ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- pagination control -->
                        <?php if (!empty($agents)): ?>
                            <?php include('includes/pagination_control.php'); ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>