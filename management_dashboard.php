<?php
include_once "./crest/crest.php";
include_once "./crest/settings.php";
include('includes/header.php');

// get deals
include_once "./data/fetch_deals.php";

// import utils 
include_once "./utils/index.php";

//get the year from get request
$selected_year = isset($_GET['year']) ? explode('/', $_GET['year'])[2] : date('Y');
$developer_name = isset($_GET['developer_name']) ? $_GET['developer_name'] : null;

// echo "<pre>";
// print_r($_GET);
// echo "</pre>";

$filter = [
    // 'CATEGORY_ID' => 0,
    '>=BEGINDATE' => "$selected_year-01-01",
    '<=BEGINDATE' => "$selected_year-12-31",
];

$deals = getFilteredDeals($filter) ?? [];
$deal_fields = get_deal_fileds();

$developerwise_deals = $deals;

if (!empty($developer_name)) {
    $developerwise_deals = array_filter($deals, function ($deal) use ($developer_name) {
        return mapDeveloperName($deal['UF_CRM_1678454522560']) == $developer_name;
    });
}

// get the develpers name
include_once "./static/developers.php";
$developers = getDevelopers();

if (!empty($deals)) {

    // get deals per deal type
    function get_deals_per_deal_type($deals, $deal_fields)
    {
        $deal_types = $deal_fields['UF_CRM_1691416031901']['items'] ?? [];

        $deals_per_deal_type = [];

        foreach ($deal_types as $deal_type) {
            $type_id = $deal_type['ID'] ?? null;
            $deal_type_name = map_enum($deal_fields, 'UF_CRM_1691416031901', $type_id) ?? 'Unknown';

            $deals_per_deal_type[$deal_type_name] = array_filter($deals, function ($deal) use ($type_id) {
                return $deal['UF_CRM_1691416031901'] ==  $type_id;
            });
        }

        return $deals_per_deal_type;
    }

    $deals_per_deal_type = get_deals_per_deal_type($deals, $deal_fields);

    // echo "<pre>";
    // print_r($deals_per_deal_type);
    // echo "</pre>";

    function get_closed_deals($deals)
    {
        return array_filter($deals, function ($deal) {
            return $deal['CLOSED'] == 'Y';
        });
    }

    $closed_deals = get_closed_deals($deals);

    function get_formatted_deals(&$deals)
    {

        $final_deals = [
            'January' => [
                'count_of_closed_deals' => 0,
                'property_price' => 0,
                'gross_commission' => 0,
                'net_commission' => 0,
                'total_payment_received' => 0,
                'amount_receivable' => 0,
            ],
            'February' => [
                'count_of_closed_deals' => 0,
                'property_price' => 0,
                'gross_commission' => 0,
                'net_commission' => 0,
                'total_payment_received' => 0,
                'amount_receivable' => 0,
            ],
            'March' => [
                'count_of_closed_deals' => 0,
                'property_price' => 0,
                'gross_commission' => 0,
                'net_commission' => 0,
                'total_payment_received' => 0,
                'amount_receivable' => 0,
            ],
            'April' => [
                'count_of_closed_deals' => 0,
                'property_price' => 0,
                'gross_commission' => 0,
                'net_commission' => 0,
                'total_payment_received' => 0,
                'amount_receivable' => 0,
            ],
            'May' => [
                'count_of_closed_deals' => 0,
                'property_price' => 0,
                'gross_commission' => 0,
                'net_commission' => 0,
                'total_payment_received' => 0,
                'amount_receivable' => 0,
            ],
            'June' => [
                'count_of_closed_deals' => 0,
                'property_price' => 0,
                'gross_commission' => 0,
                'net_commission' => 0,
                'total_payment_received' => 0,
                'amount_receivable' => 0,
            ],
            'July' => [
                'count_of_closed_deals' => 0,
                'property_price' => 0,
                'gross_commission' => 0,
                'net_commission' => 0,
                'total_payment_received' => 0,
                'amount_receivable' => 0,
            ],
            'August' => [
                'count_of_closed_deals' => 0,
                'property_price' => 0,
                'gross_commission' => 0,
                'net_commission' => 0,
                'total_payment_received' => 0,
                'amount_receivable' => 0,
            ],
            'September' => [
                'count_of_closed_deals' => 0,
                'property_price' => 0,
                'gross_commission' => 0,
                'net_commission' => 0,
                'total_payment_received' => 0,
                'amount_receivable' => 0,
            ],
            'October' => [
                'count_of_closed_deals' => 0,
                'property_price' => 0,
                'gross_commission' => 0,
                'net_commission' => 0,
                'total_payment_received' => 0,
                'amount_receivable' => 0,
            ],
            'November' => [
                'count_of_closed_deals' => 0,
                'property_price' => 0,
                'gross_commission' => 0,
                'net_commission' => 0,
                'total_payment_received' => 0,
                'amount_receivable' => 0,
            ],
            'December' => [
                'count_of_closed_deals' => 0,
                'property_price' => 0,
                'gross_commission' => 0,
                'net_commission' => 0,
                'total_payment_received' => 0,
                'amount_receivable' => 0,
            ],
            'total' => [
                'count_of_closed_deals' => 0,
                'property_price' => 0,
                'gross_commission' => 0,
                'net_commission' => 0,
                'total_payment_received' => 0,
                'amount_receivable' => 0,
            ],
        ];

        foreach ($deals as $deal) {
            $final_deals['total']['count_of_closed_deals'] += $deal['CATEGORY_ID'] == 0 ? 1 : 0;
            $final_deals['total']['property_price'] += (int)$deal['OPPORTUNITY'] ?? 0;
            $final_deals['total']['gross_commission'] += (int)explode('|', $deal['UF_CRM_1591696067251'])[0] ?? 0;
            $final_deals['total']['net_commission'] += (int)explode('|', $deal['UF_CRM_1736316474504'])[0] ?? 0;
            $final_deals['total']['total_payment_received'] += (int)explode('|', $deal['UF_CRM_1727628185464'])[0] ?? 0;
            $final_deals['total']['amount_receivable'] += $deal['UF_CRM_1727628203466'] ?? 0;

            $month = date('F', strtotime($deal['BEGINDATE']));
            $final_deals[$month]['count_of_closed_deals'] += $deal['CLOSED'] == 'Y' ? 1 : 0;
            $final_deals[$month]['property_price'] += (int)$deal['OPPORTUNITY'] ?? 0;
            $final_deals[$month]['gross_commission'] += (int)explode('|', $deal['UF_CRM_1591696067251'])[0] ?? 0;
            $final_deals[$month]['net_commission'] += (int)explode('|', $deal['UF_CRM_1736316474504'])[0] ?? 0;
            $final_deals[$month]['total_payment_received'] += (int)explode('|', $deal['UF_CRM_1727628185464'])[0] ?? 0;
            $final_deals[$month]['amount_receivable'] += $deal['UF_CRM_1727628203466'] ?? 0;
        };

        return $final_deals;
    }

    $final_deals = get_formatted_deals($deals);
    $developerwise_final_deals = get_formatted_deals($developerwise_deals);

    $total_deals = array_pop($final_deals);
    $developerwise_total_deals = array_pop($developerwise_final_deals);


    // monthly deals per developer with total monthly and yearly property value
    function get_monthly_deals_per_developer($deals, &$developers)
    {
        $monthlyDealsPerDeveloper = [];
        $quarters = [
            'Q1' => ['Jan', 'Feb', 'Mar'],
            'Q2' => ['Apr', 'May', 'Jun'],
            'Q3' => ['Jul', 'Aug', 'Sep'],
            'Q4' => ['Oct', 'Nov', 'Dec']
        ];

        foreach ($developers as $developer) {
            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

            // Monthly calculations
            foreach ($months as $month) {
                $monthwiseDeals = array_filter($deals, function ($deal) use ($month) {
                    return date('M', strtotime($deal['BEGINDATE'])) == $month;
                });

                $monthlyDealsPerDeveloper[$developer]['monthly_deals'][$month]['deals'] = array_filter($monthwiseDeals, function ($deal) use ($developer) {
                    return mapDeveloperName($deal['UF_CRM_1678454522560']) == $developer;
                });

                $monthlyDealsPerDeveloper[$developer]['monthly_deals'][$month]['total_monthly_property_value'] = array_reduce($monthlyDealsPerDeveloper[$developer]['monthly_deals'][$month]['deals'], function ($total, $deal) {
                    return isset($deal['OPPORTUNITY']) ? $total + (int)$deal['OPPORTUNITY'] : $total;
                }, 0);
            }

            // Quarterly calculations
            foreach ($quarters as $quarter => $quarterMonths) {
                $quarterlyDeals = array_filter($deals, function ($deal) use ($quarterMonths) {
                    return in_array(date('M', strtotime($deal['BEGINDATE'])), $quarterMonths);
                });

                $monthlyDealsPerDeveloper[$developer]['quarterly_deals'][$quarter]['deals'] = array_filter($quarterlyDeals, function ($deal) use ($developer) {
                    return mapDeveloperName($deal['UF_CRM_1678454522560']) == $developer;
                });

                $monthlyDealsPerDeveloper[$developer]['quarterly_deals'][$quarter]['total_quarterly_property_value'] = array_reduce($monthlyDealsPerDeveloper[$developer]['quarterly_deals'][$quarter]['deals'], function ($total, $deal) {
                    return isset($deal['OPPORTUNITY']) ? $total + (int)$deal['OPPORTUNITY'] : $total;
                }, 0);
            }

            // Total property value calculation
            $monthlyDealsPerDeveloper[$developer]['total_property_value'] = array_reduce($monthlyDealsPerDeveloper[$developer]['monthly_deals'], function ($total, $prev) {
                return isset($prev['total_monthly_property_value']) ? $total + (int)$prev['total_monthly_property_value'] : $total;
            });
        }

        return $monthlyDealsPerDeveloper;
    }

    $monthly_deals_per_developer = get_monthly_deals_per_developer($deals, $developers);

    // echo "<pre>";
    // print_r($monthly_deals_per_developer);
    // echo "</pre>";


    // Deals per lead source
    function get_deals_per_lead_source($deals, $deal_fields)
    {
        $lead_sources = $deal_fields['UF_CRM_1727854893657']['items'];
        echo "<pre>";
        // print_r($lead_sources);
        echo "</pre>";
        $deals_per_lead_source = [];
        foreach ($lead_sources as $lead_source) {
            $lead_source_id = $lead_source['ID'];
            $lead_source_name = map_enum($deal_fields, 'UF_CRM_1727854893657',  $lead_source_id) ?? 'Unknown';

            $deals_per_lead_source[$lead_source_name] = array_filter($deals, function ($deal) use ($lead_source_id) {
                return $deal['UF_CRM_1727854893657'] ==  $lead_source_id;
            });
        }

        return $deals_per_lead_source;
    }

    $deals_per_lead_source = get_deals_per_lead_source($deals, $deal_fields) ?? [];

    // echo "<pre>";
    // print_r($deals_per_lead_source);
    // echo "</pre>";
}
// echo "<pre>";
// print_r($deals);
// echo "</pre>";

?>

<div class="flex w-full h-screen">
    <?php include('includes/sidebar.php'); ?>
    <div class="main-content-area overflow-y-auto flex-1 bg-gray-100 dark:bg-gray-900"> <?php include('includes/navbar.php'); ?>
        <div class="px-8 py-6">
            <!-- date picker -->
            <?php include('./includes/datepicker.php'); ?>

            <?php if (empty($final_deals)): ?>
                <div class="h-[65vh] flex justify-center items-center">
                    <h1 class="text-2xl font-bold mb-6 dark:text-white">No data available</h1>
                </div>
            <?php else: ?>
                <div>
                    <!-- cards container -->
                    <div class="mb-6 max-w-full grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 justify-between gap-4">
                        <a href="#" class="block max-w-sm p-6 bg-white border-l-8 rounded-lg shadow border-green-500 hover:shadow-lg dark:bg-gray-800 dark:border-green-300/60 dark:hover:bg-green-200/10">
                            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Closed Deals</h5>
                            <p class="font-normal text-gray-700 dark:text-gray-400"><?= $developerwise_total_deals['count_of_closed_deals'] ?> / <?= count($deals) ?></p>
                        </a>
                        <a href="#" class="block max-w-sm p-6 bg-white border-l-8 rounded-lg shadow border-red-500 hover:shadow-lg dark:bg-gray-800 dark:border-red-300/60 dark:hover:bg-red-200/10">
                            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Total Property Price</h5>
                            <p class="font-normal text-gray-700 dark:text-gray-400"><?= number_format($developerwise_total_deals['property_price'], 2) ?> AED</p>
                        </a>
                        <a href="#" class="block max-w-sm p-6 bg-white border-l-8 rounded-lg shadow border-blue-500 hover:shadow-lg dark:bg-gray-800 dark:border-blue-300/60 dark:hover:bg-blue-200/10">
                            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Gross Commission</h5>
                            <p class="font-normal text-gray-700 dark:text-gray-400"><?= number_format($total_deals['gross_commission'], 2) ?> AED</p>
                        </a>
                        <a href="#" class="block max-w-sm p-6 bg-white border-l-8 rounded-lg shadow border-orange-500 hover:shadow-lg dark:bg-gray-800 dark:border-orange-300/60 dark:hover:bg-orange-200/10">
                            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Net Commission</h5>
                            <p class="font-normal text-gray-700 dark:text-gray-400"><?= number_format($total_deals['net_commission'], 2) ?> AED</p>
                        </a>
                    </div>

                    <!-- main deals table and property type chart -->
                    <div class="my-4 grid grid-cols-1 lg:grid-cols-3 gap-4">
                        <!-- table container -->
                        <div class="h-[30rem] col-span-2 bg-white dark:bg-gray-800 border shadow-xl border-gray-200 dark:border-gray-700 rounded-xl">
                            <!-- select developer filter -->
                            <div class="flex p-2 justify-between items-center">
                                <?php include './includes/select_developers.php'; ?>
                                <div class="flex items-center gap-2">
                                    <!-- <span class="text-gray-600 dark:text-gray-400 font-semibold text-lg">Developer: </span> -->
                                    <p class="text-gray-600 dark:text-gray-400 font-semibold text-lg bg-gray-200 dark:bg-gray-700 rounded px-2"><?= isset($_GET['developer_name']) ? ($_GET['developer_name'] == '' ? 'All Developers' : $_GET['developer_name']) : 'All Developers' ?></p>
                                </div>
                                <div class="flex items-center justify-end gap-2 p-2">
                                    <a href="#" onclick="download_table_as_csv('monthly_deals_table');" class="flex items-center gap-1 text-gray-600 dark:text-gray-400 hover:text-blue-500 dark:hover:text-blue-500 transition duration-300" title="Download as CSV">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                    </a>
                                </div>
                            </div>

                            <div class="relative h-[85%] overflow-auto sm:rounded-lg">
                                <table id="monthly_deals_table" class="text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                    <thead class="sticky top-0 text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                        <tr>
                                            <th scope="col" class="px-6 py-3">
                                                Month
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                Count of Closed Deals
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                Property Price
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                Gross Commission
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                Net Commission
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                Total Payment Received
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                Amount Receivable
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($developerwise_final_deals as $month => $details) : ?>
                                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                    <?= $month ?>
                                                </th>
                                                <td class="px-6 py-4">
                                                    <?= $details['count_of_closed_deals'] ?>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <?= number_format($details['property_price'], 2) ?>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <?= number_format($details['gross_commission'], 2) ?>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <?= number_format($details['net_commission'], 2) ?>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <?= number_format($details['total_payment_received'], 2) ?>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <?= number_format($details['amount_receivable'], 2) ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot class="sticky bottom-0 text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                        <tr>
                                            <th scope="row" class="px-6 py-4 font-medium font-bold whitespace-nowrap">
                                                Total
                                            </th>
                                            <td class="px-6 py-4">
                                                <?= $developerwise_total_deals['count_of_closed_deals'] ?>
                                            </td>
                                            <td class="px-6 py-4">
                                                <?= number_format($developerwise_total_deals['property_price'], 2) ?>
                                            </td>
                                            <td class="px-6 py-4">
                                                <?= number_format($developerwise_total_deals['gross_commission'], 2) ?>
                                            </td>
                                            <td class="px-6 py-4">
                                                <?= number_format($developerwise_total_deals['net_commission'], 2) ?>
                                            </td>
                                            <td class="px-6 py-4">
                                                <?= number_format($developerwise_total_deals['total_payment_received'], 2) ?>
                                            </td>
                                            <td class="px-6 py-4">
                                                <?= number_format($developerwise_total_deals['amount_receivable'], 2) ?>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        
                    </div>

                    
                </div>
                
        </div>

    <?php endif; ?>
    </div>
</div>
</div>

<script>

    function isDarkTheme() {
        return localStorage.getItem('darkMode') === 'true';
    }

    let isdark = isDarkTheme();

    function display_property_type_chart() {
        var dealsPerDealType = <?php echo json_encode($deals_per_deal_type); ?>;
        console.log('dealsPerDealType', dealsPerDealType);

        var offplanDeals = Object.keys(dealsPerDealType['Offplan']).length;
        var secondaryDeals = Object.keys(dealsPerDealType['Secondary']).length;

        // property type
        var options = {
            series: [offplanDeals, secondaryDeals],
            labels: ["Offplan", "Secondary"],
            chart: {
                width: 380,
                type: 'donut',
                events: {
                    dataPointSelection: function(event, chartContext, config) {
                        const deal_types = {
                            'offplan': '1171',
                            'secondary': '1169'
                        };
                        var selectedType = config.w.config.labels[config.dataPointIndex].trim().toLowerCase();
                        var selectedTypeId = deal_types[selectedType];
                        if (confirm(`Would you like to apply the filter to show only ${selectedType} deals?`)) {
                            window.location.href = `overall_deals.php?deal_type=${selectedTypeId}`;
                        }
                    }
                }
            },
            dataLabels: {
                enabled: true,
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        show: false
                    }
                }
            }],
            legend: {
                position: 'top',
                offsetY: 0,
                // height: 230,
                labels: {
                    colors: `${isdark ? '#ffffff' : '#000000'}`
                }
            }
        };
        var chart = new ApexCharts(document.querySelector("#property-type-chart"), options);
        chart.render();
    }

    display_property_type_chart();

    function display_developer_property_value_chart() {
        let developersData = <?php echo json_encode($sorted_monthly_deals_per_developer); ?>;

        let developers = [];
        let property_values = [];
        for (developer in developersData) {
            // console.log(developer);
            developers.push(developer)
            property_values.push(developersData[developer]['total_property_value']);
        }

        // console.log(developers);
        // console.log(property_values);


        let options = {
            series: [...property_values.slice(0, 10)],
            labels: [...developers.slice(0, 10)],
            chart: {
                width: 500,
                type: 'donut',
            },
            plotOptions: {
                pie: {
                    startAngle: -90,
                    endAngle: 270
                }
            },
            dataLabels: {
                enabled: true
            },
            fill: {
                type: 'gradient',
            },
            legend: {
                formatter: function(val, opts) {
                    return val + " - " + opts.w.globals.series[opts.seriesIndex]
                },
                // position: 'top',
                offsetY: 0,
                labels: {
                    colors: `${isdark ? '#ffffff' : '#000000'}`
                }
            },
            title: {
                // text: 'Developers VS Property Value',
                style: {
                    color: `${isdark ? '#ffffff' : '#000000'}`
                }
            },
            responsive: [{
                breakpoint: 670,
                options: {
                    chart: {
                        width: 300
                    },
                    legend: {
                        position: 'bottom',
                        offsetY: 0,
                        labels: {
                            colors: `${isdark ? '#ffffff' : '#000000'}`
                        }
                    }
                }
            }]
        };

        let chart = new ApexCharts(document.querySelector("#developer-property-value-chart"), options);
        chart.render();
    }

    display_developer_property_value_chart();

    function display_lead_source_chart() {

        let deals_per_lead_source = <?php echo json_encode($deals_per_lead_source); ?>;
        let categories = [];
        let net_commission = {};
        let gross_commission = {};

        for (x in deals_per_lead_source) {
            // console.log(deals_per_lead_source[x]);
            categories.push(x);

            // initialise_commission_array for all types of leads
            if (net_commission[x] == null) {
                net_commission[x] = 0;
            }

            if (gross_commission[x] == null) {
                gross_commission[x] = 0;
            }

            if (Array.isArray(deals_per_lead_source[x]) && deals_per_lead_source[x].length > 0) {
                deals_per_lead_source[x].forEach(deal => {
                    if (deal['UF_CRM_1736316474504'] != null) {
                        net_commission[x] += parseFloat(deal['UF_CRM_1736316474504'].split('|')[0]);
                    }

                    if (deal['UF_CRM_1591696067251'] != null) {
                        gross_commission[x] += parseFloat(deal['UF_CRM_1591696067251'].split('|')[0]);
                    }
                });
            } else {
                for (deal in deals_per_lead_source[x]) {
                    if (deals_per_lead_source[x][deal]['UF_CRM_1736316474504'] != null) {
                        net_commission[x] += parseFloat(deals_per_lead_source[x][deal]['UF_CRM_1736316474504'].split('|')[0]);
                    }

                    if (deals_per_lead_source[x][deal]['UF_CRM_1591696067251'] != null) {
                        gross_commission[x] += parseFloat(deals_per_lead_source[x][deal]['UF_CRM_1591696067251'].split('|')[0]);
                    }
                }
            }
        }

        let net_commission_values = Object.values(net_commission);
        let gross_commission_values = Object.values(gross_commission);

        console.log(deals_per_lead_source);

        console.log(net_commission_values, gross_commission_values);

        let options = {
            series: [{
                name: 'Net Commission',
                data: [...net_commission_values]
            }, {
                name: 'Gross Commission',
                data: [...gross_commission_values]
            }],
            chart: {
                type: 'bar',
                height: 350
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    endingShape: 'rounded'
                },
            },
            dataLabels: {
                enabled: false,
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: [...categories],
                labels: {
                    style: {
                        colors: `${isdark ? '#ffffff' : '#000000'}`
                    }
                }
            },
            yaxis: {
                title: {
                    text: 'AED',
                    style: {
                        color: `${isdark ? '#ffffff' : '#000000'}`
                    }
                },
                labels: {
                    style: {
                        colors: `${isdark ? '#ffffff' : '#000000'}`
                    }
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + " AED"
                    }
                }
            },
            legend: {
                labels: {
                    colors: `${isdark ? '#ffffff' : '#000000'}`
                }
            }
        };

        let chart = new ApexCharts(document.querySelector("#lead-source-chart"), options);
        chart.render();
    }

    display_lead_source_chart();
</script>

<?php include('includes/footer.php'); ?>