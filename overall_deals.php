<?php
include_once __DIR__ .  "/crest/crest.php";
include_once __DIR__ .  "/crest/settings.php";
include_once __DIR__ .  "/utils/index.php";
include('includes/header.php');

// include the fetch deals page
include_once __DIR__ . "/data/fetch_deals.php";
include_once __DIR__ . "/data/fetch_users.php";

$selected_year = isset($_GET['year']) ? explode('/', $_GET['year'])[2] : date('Y');
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$deal_type = isset($_GET['deal_type']) ? $_GET['deal_type'] : null;

$filter = [
    'CATEGORY_ID' => 0,
    '>=BEGINDATE' => "$selected_year-01-01",
    '<=BEGINDATE' => "$selected_year-12-31",
    'UF_CRM_1691416031901' => $deal_type
];

// Reduced select fields to only what's necessary
$select = [
    'ID',
    'ASSIGNED_BY_ID',
    'BEGINDATE',
    'OPPORTUNITY',
    'UF_CRM_1691416031901', // Deal Type (Pipeline)
    'UF_CRM_1722846366045', // Property Type
    'UF_CRM_660FC429E780D', // Project Name
    'UF_CRM_1645615616',    // Unit No
    'UF_CRM_1678454522560', // Developer Name
    'UF_CRM_1721198189214', // Client Name
    'UF_CRM_1591696067251', // Gross Commission
    'UF_CRM_1703511853638', // VAT
    'UF_CRM_1736316474504', // Gi Properties Commission
    'UF_CRM_1727854555607', // Team
    'UF_CRM_1727627289760', // Payment Received
    'UF_CRM_1727628185464', // Total Payment Received
    'UF_CRM_1727628203466', // Amount Receivable
];

$dealsData = get_paginated_deals($page, [], $select, ['ID' => 'desc']) ?? [];
$deals = $dealsData['deals'] ?? [];

$users = getUsers();

// pagination
$total_deals = $dealsData['total'] ?? 0;
$total_pages = ceil($total_deals / 50);

$fields = get_deal_fileds();

$overall_deals = [];

if (!empty($deals)) {
    foreach ($deals as $index => $deal) {
        $overall_deals[$index]['Date'] = date('Y-m-d', strtotime($deal['BEGINDATE'] ?? ''));

        // Deal Type
        if (isset($deal['UF_CRM_1691416031901'])) {
            $dealType = map_enum($fields, 'UF_CRM_1691416031901', $deal['UF_CRM_1691416031901']);
            $overall_deals[$index]['Deal Type'] = $dealType ?? 'N/A';
        } else {
            $overall_deals[$index]['Deal Type'] = "N/A";
        }

        // Basic property details
        $overall_deals[$index]['Project Name'] = $deal['UF_CRM_660FC429E780D'] ?? 'N/A';
        $overall_deals[$index]['Unit No'] = $deal['UF_CRM_1645615616'] ?? 'N/A';
        $overall_deals[$index]['Developer'] = mapDeveloperName($deal['UF_CRM_1678454522560']) ?? 'N/A';

        // map property type
        if (isset($deal['UF_CRM_1722846366045'])) {
            $propertyType = map_enum($fields, 'UF_CRM_1722846366045', $deal['UF_CRM_1722846366045']);
            $overall_deals[$index]['Property Type'] = $propertyType ?? 'N/A';
        } else {
            $overall_deals[$index]['Property Type'] = 'N/A';
        }

        // Client & Agent details
        $overall_deals[$index]['Client Name'] = $deal['UF_CRM_1721198189214'] ?? 'N/A';

        // Get agent name by id
        if (isset($deal['ASSIGNED_BY_ID'])) {
            $filteredAgents = array_filter($users, fn($user) => $user['ID'] == $deal['ASSIGNED_BY_ID']);
            $agent = array_values($filteredAgents)[0] ?? [];

            $firstName = $agent['NAME'] ?? '';
            $middleName = $agent['SECOND_NAME'] ?? '';
            $lastName = $agent['LAST_NAME'] ?? '';
            $overall_deals[$index]['Agent'] = trim("$firstName $middleName $lastName");
        } else {
            $overall_deals[$index]['Agent'] = "N/A";
        }

        // Financial information
        $overall_deals[$index]['Property Price'] = number_format($deal['OPPORTUNITY'] ?? 0) . " AED";

        $grossCommission = (int)($deal['UF_CRM_1591696067251'] ?? 0);
        $vat = (int)($deal['UF_CRM_1703511853638'] ?? 0);
        $overall_deals[$index]['Commission (Inc. VAT)'] = number_format($grossCommission + $vat) . " AED";

        // Payment status
        if (isset($deal['UF_CRM_1727627289760'])) {
            $paymentReceived = map_enum($fields, 'UF_CRM_1727627289760', $deal['UF_CRM_1727627289760']);
            $overall_deals[$index]['Payment Status'] = $paymentReceived ?? 'N/A';
        } else {
            $overall_deals[$index]['Payment Status'] = "N/A";
        }

        // Payment amounts
        $totalReceived = (int)($deal['UF_CRM_1727628185464'] ?? 0);
        $amountReceivable = (int)($deal['UF_CRM_1727628203466'] ?? 0);

        $overall_deals[$index]['Received'] = number_format($totalReceived) . " AED";
        $overall_deals[$index]['Receivable'] = number_format($amountReceivable) . " AED";
    }
}
?>

<div class="flex w-full h-screen">
    <?php include('includes/sidebar.php'); ?>
    <div class="main-content-area flex-1 overflow-y-auto bg-gray-100 dark:bg-gray-900">
        <?php include('includes/navbar.php'); ?>
        <div class="px-4 md:px-8 py-6">
            <!-- date picker -->
            <?php include('./includes/datepicker.php'); ?>

            <?php if (empty($deals)): ?>
                <div class="h-[65vh] flex justify-center items-center">
                    <h1 class="text-2xl font-bold mb-6 dark:text-white">No data available</h1>
                </div>
            <?php else: ?>
                <div class="p-4 shadow-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg">
                    <!-- Filter controls -->
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <label class="text-gray-700 dark:text-gray-300 whitespace-nowrap" for="deal_type">Deal Type:</label>
                        <select id="deal_type" class="bg-white dark:text-gray-300 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-900">
                            <option value="">All</option>
                            <?php
                            $deal_types = [
                                '1171' => 'offplan',
                                '1169' => 'secondary'
                            ];
                            $selected_type = $_GET['deal_type'] ?? '';
                            foreach ($deal_types as $id => $deal_type):
                            ?>
                                <option value="<?= $id ?>" <?= $selected_type == $id ? 'selected' : '' ?>><?= ucfirst($deal_type) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg focus:outline-none" onclick="applyFilter()">Apply</button>
                        <?php if (isset($_GET['deal_type'])): ?>
                            <button onclick="clearFilter()" class="text-white bg-red-500 hover:bg-red-600 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-4 py-2 text-center inline-flex items-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
                                <svg class="w-4 h-4" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                <p class="ml-2">Clear</p>
                            </button>
                        <?php endif; ?>
                    </div>

                    <!-- Table container -->
                    <div class="pb-4 rounded-lg border-0 bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700">
                        <div class="relative rounded-lg border-b border-gray-200 dark:border-gray-700 w-full overflow-auto">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-4 py-3">Date</th>
                                        <th scope="col" class="px-4 py-3">Project</th>
                                        <th scope="col" class="px-4 py-3">Unit</th>
                                        <th scope="col" class="px-4 py-3">Developer</th>
                                        <th scope="col" class="px-4 py-3">Client</th>
                                        <th scope="col" class="px-4 py-3">Agent</th>
                                        <th scope="col" class="px-4 py-3">Property Price</th>
                                        <th scope="col" class="px-4 py-3">Commission</th>
                                        <th scope="col" class="px-4 py-3">Payment Status</th>
                                        <th scope="col" class="px-4 py-3">Received</th>
                                        <th scope="col" class="px-4 py-3">Receivable</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($overall_deals as $deal): ?>
                                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                            <th scope="row" class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                <?= $deal['Date'] ?? "N/A" ?>
                                            </th>
                                            <td class="px-4 py-3">
                                                <?= $deal['Project Name'] ?>
                                            </td>
                                            <td class="px-4 py-3">
                                                <?= $deal['Unit No'] ?>
                                            </td>
                                            <td class="px-4 py-3">
                                                <?= $deal['Developer'] ?>
                                            </td>
                                            <td class="px-4 py-3">
                                                <?= $deal['Client Name'] ?>
                                            </td>
                                            <td class="px-4 py-3">
                                                <?= $deal['Agent'] ?>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <?= $deal['Property Price'] ?>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <?= $deal['Commission (Inc. VAT)'] ?>
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="<?= strtolower($deal['Payment Status']) == 'yes' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' ?> text-xs font-medium px-2.5 py-0.5 rounded">
                                                    <?= $deal['Payment Status'] ?>
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <?= $deal['Received'] ?>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <?= $deal['Receivable'] ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- pagination control -->
                        <?php if (!empty($overall_deals)): ?>
                            <?php include('includes/pagination_control.php'); ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    function applyFilter() {
        let deal_type = document.getElementById('deal_type').value;
        let url = new URL(window.location.href);
        url.searchParams.set('deal_type', deal_type);
        window.location.href = url.toString();
    }

    function clearFilter() {
        let url = new URL(window.location.href);
        url.searchParams.delete('deal_type');
        window.location.href = url.toString();
    }
</script>

<?php include('includes/footer.php'); ?>