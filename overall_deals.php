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

$dealsData = get_paginated_deals($page) ?? [];
$deals = $dealsData['deals'] ?? [];

// pagination
$total_deals = $dealsData['total'] ?? 0;
$total_pages = ceil($total_deals / 50);


$fields = get_deal_fileds();

$overall_deals = [];

if (!empty($deals)) {
    foreach ($deals as $index => $deal) {
        $overall_deals[$index]['Date'] = date('Y-m-d', strtotime($deal['BEGINDATE'] ?? ''));
        //get the transaction type value
        $transactionType = map_enum($fields, 'UF_CRM_1727625723908', $deal['UF_CRM_1727625723908']);
        $overall_deals[$index]['Transaction Type'] = $transactionType ?? null;

        // debug: deal type not defined in custom fields its named as pipeline instade, and also the pipleline field is missing for overall deal type
        if (isset($deal['UF_CRM_1691416031901'])) {
            $dealType = map_enum($fields, 'UF_CRM_1691416031901', $deal['UF_CRM_1691416031901']);
            $overall_deals[$index]['Deal Type'] = $dealType ?? null;
        } else {
            $overall_deals[$index]['Deal Type'] = "deal_type_not_defined";
        }

        $overall_deals[$index]['Project Name'] = $deal['UF_CRM_660FC429E780D'] ?? null;
        $overall_deals[$index]['Unit No'] = $deal['UF_CRM_1645615616'] ?? null;
        $overall_deals[$index]['Developer Name'] = mapDeveloperName($deal['UF_CRM_1678454522560']) ?? null;

        // map property type
        if (isset($deal['UF_CRM_1722846366045'])) {
            $propertyType = map_enum($fields, 'UF_CRM_1722846366045', $deal['UF_CRM_1722846366045']);
            $overall_deals[$index]['Property Type'] = $propertyType ?? null;
        } else {
            $overall_deals[$index]['Property Type'] = 'Not Defined';
        }

        // map no of br
        if (isset($deal['UF_CRM_1639989204901'])) {
            $noOfBr = map_enum($fields, 'UF_CRM_1639989204901', $deal['UF_CRM_1639989204901']);
            $overall_deals[$index]['No Of Br'] = $noOfBr ?? null;
        } else {
            $overall_deals[$index]['No Of Br'] = 'Not Defined';
        }

        $overall_deals[$index]['Client Name'] = $deal['UF_CRM_1721198189214'] ?? null;

        // get agent name by id
        if (isset($deal['ASSIGNED_BY_ID'])) {
            $agent = getUser($deal['ASSIGNED_BY_ID']);
            // Debug: agent second and last name not showing
            $overall_deals[$index]['Agent Name'] = $agent['NAME'] ?? '' . ' ' . $agent['SECOND_NAME'] ?? '' . ' ' . $agent['LAST_NAME'] ?? '' ?? null;
        } else {
            $overall_deals[$index]['Agent Name'] = "agent_not_found";
        }
        // map the team value
        if (isset($deal['UF_CRM_1727854555607'])) {
            $teamName = map_enum($fields, 'UF_CRM_1727854555607', $deal['UF_CRM_1727854555607']);
            $overall_deals[$index]['Team'] = $teamName ?? null;
        } else {
            $overall_deals[$index]['Team Name'] = "field_not_defined";
        }

        $overall_deals[$index]['Property Price'] = $deal['OPPORTUNITY'] ?? null;
        $overall_deals[$index]['Gross Commission (Incl. VAT)'] = (int)$deal['UF_CRM_1591696067251'] + (int)$deal['UF_CRM_1703511853638'] ?? null;
        $overall_deals[$index]['Gross Commission'] = $deal['UF_CRM_1591696067251'] ?? null;
        $overall_deals[$index]['VAT'] = $deal['UF_CRM_1703511853638'] ?? null;
        $overall_deals[$index]['Gi Properties Commission'] = $deal['UF_CRM_1736316474504'] ?? null;

        if (isset($deal['UF_CRM_1727854893657'])) {
            $leadSource = map_enum($fields, 'UF_CRM_1727854893657', $deal['UF_CRM_1727854893657']);
            $overall_deals[$index]['Lead Source'] = $leadSource ?? null;
        } else {
            $overall_deals[$index]['Lead Source'] = "field_not_defined";
        }

        if (isset($deal['UF_CRM_1727872815184'])) {
            $invoiceStatus = map_enum($fields, 'UF_CRM_1727872815184', $deal['UF_CRM_1727872815184']);
            $overall_deals[$index]['Invoice Status'] = $invoiceStatus ?? null;
        } else {
            $overall_deals[$index]['Invoice Status'] = "field_not_defined";
        }

        $overall_deals[$index]['Notification'] = null;

        if (isset($deal['UF_CRM_1727627289760'])) {
            $paymentReceived = map_enum($fields, 'UF_CRM_1727627289760', $deal['UF_CRM_1727627289760']);
            $overall_deals[$index]['Payment Received'] = $paymentReceived ?? null;
        } else {
            $overall_deals[$index]['Payment Received'] = "field_not_defined";
        }

        $overall_deals[$index]['Follow-up Notification'] = null;

        $overall_deals[$index]['1st Payment Received'] = $deal['UF_CRM_1727874909907'] ?? null;
        $overall_deals[$index]['2nd Payment Received'] = $deal['UF_CRM_1727874935109'] ?? null;
        $overall_deals[$index]['3rd Payment Received'] = $deal['UF_CRM_1727874959670'] ?? null;
        $overall_deals[$index]['Total Payment Received'] = $deal['UF_CRM_1727628185464'] ?? null;
        $overall_deals[$index]['Amount Receivable'] = $deal['UF_CRM_1727628203466'] ?? null;
    }
}
// echo "<pre>";
// print_r($overall_deals);
// echo "</pre>";

// echo "<pre>";
// print_r($fields);
// echo "</pre>";
?>


<div class="flex w-full h-screen">
    <?php include('includes/sidebar.php'); ?>
    <div class="main-content-area flex-1 overflow-y-auto bg-gray-100 dark:bg-gray-900">
        <?php include('includes/navbar.php'); ?>
        <div class="px-8 py-6">
            <!-- date picker -->
            <?php include('./includes/datepicker.php'); ?>

            <?php if (empty($deals)): ?>
                <div class="h-[65vh] flex justify-center items-center">
                    <h1 class="text-2xl font-bold mb-6 dark:text-white">No data available</h1>
                </div>
            <?php else: ?>
                <div class="p-4 shadow-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg">
                    <div class="flex items-center space-x-4 mb-4">
                        <label class="text-gray-700 dark:text-gray-300" for="deal_type">Deal Type:</label>
                        <select id="deal_type" class="bg-white dark:text-gray-300 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-900">
                            <option value="">All</option>
                            <?php
                            $deal_types = [
                                '1171' => 'offplan',
                                '1169' => 'secondary'
                            ];
                            if (isset($_GET['deal_type'])) {
                                $selected_type = $_GET['deal_type'];
                            } else {
                                $selected_type = '';
                            }
                            ?>
                            <?php foreach ($deal_types as $id => $deal_type): ?>
                                <option value="<?= $id ?>" <?= $selected_type == $id ? 'selected' : '' ?>><?= $deal_type ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg focus:outline-none" onclick="applyFilter()">Apply</button>
                        <?php if (isset($_GET['deal_type'])): ?>
                            <button onclick="clearFilter()" class="text-white bg-red-500 hover:bg-red-600 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-4 py-2 text-center inline-flex items-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
                                <svg class="w-4 h-4" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                <p class="ml-2">Clear</p>
                            </button>
                        <?php endif; ?>
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

                    <div class="pb-4 rounded-lg border-0 bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 rounded-lg">
                        <!-- Overall deals -->
                        <div class="relative rounded-lg border-b border-gray-200 dark:border-gray-700 w-full overflow-auto">
                            <table class="w-full h-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">Date</th>
                                        <th scope="col" class="px-6 py-3">Transaction Type</th>
                                        <th scope="col" class="px-6 py-3">Deal Type</th>
                                        <th scope="col" class="px-6 py-3">Project Name</th>
                                        <th scope="col" class="px-6 py-3">Unit No</th>
                                        <th scope="col" class="px-6 py-3">Developer Name</th>
                                        <th scope="col" class="px-6 py-3">Property Type</th>
                                        <th scope="col" class="px-6 py-3">No Of Br</th>
                                        <th scope="col" class="px-6 py-3">Client Name</th>
                                        <th scope="col" class="px-6 py-3">Agent Name</th>
                                        <th scope="col" class="px-6 py-3">Team</th>
                                        <th scope="col" class="px-6 py-3">Property Price</th>
                                        <th scope="col" class="px-6 py-3">Gross Commission (Incl. VAT)</th>
                                        <th scope="col" class="px-6 py-3">Gross Commission</th>
                                        <th scope="col" class="px-6 py-3">VAT</th>
                                        <!-- <th scope="col" class="px-6 py-3">Agent Net Commission</th>
                                        <th scope="col" class="px-6 py-3">Managers Commission</th>
                                        <th scope="col" class="px-6 py-3">Sales Support Commission</th> -->
                                        <th scope="col" class="px-6 py-3">Gi Properties Commission</th>

                                        <th scope="col" class="px-6 py-3">Lead Source</th>
                                        <th scope="col" class="px-6 py-3">Invoice Status</th>
                                        <!-- <th scope="col" class="px-6 py-3">Notification</th> -->
                                        <th scope="col" class="px-6 py-3">Payment Received</th>
                                        <!-- <th scope="col" class="px-6 py-3">Follow-up Notification</th> -->
                                        <th scope="col" class="px-6 py-3">1st Payment Received</th>
                                        <th scope="col" class="px-6 py-3">2nd Payment Received</th>
                                        <th scope="col" class="px-6 py-3">3rd Payment Received</th>
                                        <th scope="col" class="px-6 py-3">Total Payment Received</th>
                                        <th scope="col" class="px-6 py-3">Amount Receivable</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($overall_deals as $deal) :
                                    ?>
                                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                <?php echo $deal['Date'] ?? "--"; ?>
                                            </th>
                                            <!-- transaction type -->
                                            <td class="px-6 py-4">
                                                <?php echo $deal['Transaction Type'] ?? "--"; ?>
                                            </td>
                                            <!-- deal type -->
                                            <td class="px-6 py-4">
                                                <?php echo $deal['Deal Type'] ?? "--"; ?>
                                            </td>
                                            <!-- project name -->
                                            <td class="px-6 py-4">
                                                <?php echo $deal['Project Name'] ?? "--"; ?>
                                            </td>
                                            <!-- unit no -->
                                            <td class="px-6 py-4">
                                                <?php echo $deal['Unit No'] ?? "--"; ?>
                                            </td>
                                            <!-- developer name -->
                                            <td class="px-6 py-4">
                                                <?php echo $deal['Developer Name'] ?? "--"; ?>
                                            </td>
                                            <!-- type -->
                                            <td class="px-6 py-4">
                                                <?php echo $deal['Property Type'] ?? "--"; ?>
                                            </td>
                                            <!-- no of br -->
                                            <td class="px-6 py-4">
                                                <?php echo $deal['No Of Br'] ?? "--"; ?>
                                            </td>
                                            <!-- client name -->
                                            <td class="px-6 py-4">
                                                <?php echo $deal['Client Name'] ?? "--"; ?>
                                            </td>
                                            <!-- agent name -->
                                            <td class="px-6 py-4">
                                                <?php echo $deal['Agent Name'] ?? "--"; ?>
                                            </td>
                                            <!-- team -->
                                            <td class="px-6 py-4">
                                                <?php echo $deal['Team'] ?? "--"; ?>
                                            </td>
                                            <!-- property price -->
                                            <td class="px-6 py-4">
                                                <?php echo $deal['Property Price'] ?? "--"; ?>
                                            </td>
                                            <!-- gross commission (incl. vat) -->
                                            <td class="px-6 py-4">
                                                <?php echo $deal['Gross Commission (Incl. VAT)'] ?? "--"; ?>
                                            </td>
                                            <!-- gross commission -->
                                            <td class="px-6 py-4">
                                                <?php echo $deal['Gross Commission'] ?? "--"; ?>
                                            </td>
                                            <!-- vat -->
                                            <td class="px-6 py-4">
                                                <?php echo $deal['VAT'] ?? "--"; ?>
                                            </td>
                                            <!-- agent net commission -->
                                            <!-- <td class="px-6 py-4">
                                                <?php echo $deal['Agent Net Commission'] ?? "--"; ?>
                                            </td> -->
                                            <!-- managers commission -->
                                            <!-- <td class="px-6 py-4">
                                                <?php echo $deal['Managers Commission'] ?? "--"; ?>
                                            </td> -->
                                            <!-- sales support commission -->
                                            <!-- <td class="px-6 py-4">
                                                <?php echo $deal['Sales Support Commission'] ?? "--"; ?>
                                            </td> -->
                                            <!-- Gi Properties commission -->
                                            <td class="px-6 py-4">
                                                <?php echo $deal['Gi Properties Commission'] ?? "--"; ?>
                                            </td>
                                            <!-- commission slab -->
                                            <!-- <td class="px-6 py-4">
                                                <?php echo $deal['Commission Slab (%)'] ?? "--"; ?>
                                            </td> -->
                                            <!-- referral -->
                                            <!-- <td class="px-6 py-4">
                                                <?php echo $deal['Referral'] ?? "--"; ?>
                                            </td> -->
                                            <!-- referral fee -->
                                            <!-- <td class="px-6 py-4">
                                                <?php echo $deal['Referral Fee'] ?? "--"; ?>
                                            </td> -->
                                            <!-- lead source -->
                                            <td class="px-6 py-4">
                                                <?php echo $deal['Lead Source'] ?? "--"; ?>
                                            </td>
                                            <!-- invoice status -->
                                            <td class="px-6 py-4">
                                                <?php echo $deal['Invoice Status'] ?? "--"; ?>
                                            </td>
                                            <!-- notification -->
                                            <!-- <td class="px-6 py-4">
                                                <?php echo $deal['Notification'] ?? "--"; ?>
                                            </td> -->
                                            <!-- payment received -->
                                            <td class="px-6 py-4">
                                                <?php echo $deal['Payment Received'] ?? "--"; ?>
                                            </td>
                                            <!-- follow-up notification -->
                                            <!-- <td class="px-6 py-4">
                                                <?php echo $deal['Follow-up Notification'] ?? "--"; ?>
                                            </td> -->
                                            <!-- 1st payment received -->
                                            <td class="px-6 py-4">
                                                <?php echo $deal['1st Payment Received'] ?? "--"; ?>
                                            </td>
                                            <!-- 2nd payment received -->
                                            <td class="px-6 py-4">
                                                <?php echo $deal['2nd Payment Received'] ?? "--"; ?>
                                            </td>
                                            <!-- 3rd payment received -->
                                            <td class="px-6 py-4">
                                                <?php echo $deal['3rd Payment Received'] ?? "--"; ?>
                                            </td>
                                            <!-- total payment received -->
                                            <td class="px-6 py-4">
                                                <?php echo $deal['Total Payment Received'] ?? "--"; ?>
                                            </td>
                                            <!-- amount receivable -->
                                            <td class="px-6 py-4">
                                                <?php echo $deal['Amount Receivable'] ?? "--"; ?>
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

<?php include('includes/footer.php'); ?>