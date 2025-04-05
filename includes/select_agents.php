<?php
$all_agents = get_filtered_users(['UF_DEPARTMENT' => [5, 78, 77, 440]]); // get sales department members only
usort($all_agents, function ($a, $b) {
    return strcasecmp($a['NAME'], $b['NAME']);
});
$current_agent = getUser($_GET['agent_id'] ?? 1);
$fname = $current_agent['NAME'] ?? '';
$lname = $current_agent['LAST_NAME'] ?? '';
$mname = $current_agent['SECOND_NAME'] ?? '';
$agent_name = $fname . ' ' . $mname . ' ' . $lname;
?>

<div class="flex justify-between items-center">
    <!-- buttons div -->
    <div class="flex items-center gap-2">
        <button id="dropdownSearchButton" data-dropdown-toggle="dropdownSearch" data-dropdown-placement="bottom" class="white-gary-800 dark:text-white border border-gray-300 dark:border-blue-800 hover:bg-blue-600 hover:text-white focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" type="button">
            <?= isset($_GET['agent_id']) ? $agent_name : 'Select Agents' ?>
            <svg class="w-2.5 h-2.5 ms-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
            </svg>
        </button>
    </div>
    <!-- Dropdown menu -->
    <div id="dropdownSearch" class="z-10 hidden bg-white rounded-lg shadow w-60 dark:bg-gray-700">
        <div class="p-3">
            <label for="input-group-search" class="sr-only">Search</label>
            <div class="relative">
                <div class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                    </svg>
                </div>
                <input type="text" id="input-group-search" class="block w-full p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search Agents">
            </div>
        </div>
        <ul class="h-48 px-3 pb-3 overflow-y-auto text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownSearchButton">
            <?php foreach ($all_agents as $agent): ?>
                <li id="<?= $agent['ID'] ?>" class="mb-1 <?= isset($_GET['agent_id']) && $agent['ID'] == $_GET['agent_id'] ? 'bg-gray-600 text-white' : 'text-gray-900' ?>">
                    <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="get">
                        <div class="flex items-center ps-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                            <input type="text" name="year" value="<?= $_GET['year'] ?? date('m/d/Y') ?>" hidden>
                            <input type="text" id="agent_id" name="agent_id" value="<?= $agent['ID'] ?>" hidden>
                            <button type="submit" <?= isset($_GET['agent_id']) && $agent['ID'] == $_GET['agent_id'] ? 'disabled' : '' ?> class="w-full text-start py-2 ms-2 text-sm font-medium text-gray-900 rounded dark:text-gray-300">
                                <?php
                                $firstName = $agent['NAME'] ?? '';
                                $middleName = $agent['SECOND_NAME'] ?? '';
                                $lastName = $agent['LAST_NAME'] ?? '';
                                $agentName = $firstName . ' ' . $middleName . ' ' . $lastName;
                                echo $agentName;
                                ?>
                            </button>
                        </div>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<script>
    document.getElementById('input-group-search').addEventListener('input', function() {
        var input = this.value.toLowerCase();
        let agents = <?= json_encode($all_agents) ?>;
        console.log(agents);

        // Loop through options and hide those that don't match the search query
        agents.forEach(function(agent) {
            let agentName = `${agent['NAME'] ?? ''} ${agent['SECOND_NAME'] ?? ''} ${agent['LAST_NAME'] ?? ''}`;
            var option = document.getElementById(agent['ID']);
            var optionText = agentName.toLowerCase();
            option.style.display = optionText.includes(input) ? 'block' : 'none';
        });
    });
</script>