<?php
$current_page = basename($_SERVER['PHP_SELF']);

$sidebar_items = [
    [
        'title' => 'Management Dashboard',
        'icon' => 'tachometer-alt',
        'link' => 'management_dashboard.php',
        'current_page' => $current_page === 'management_dashboard.php',
    ],
    [
        'title' => 'Overall Deals',
        'icon' => 'handshake',
        'link' => 'overall_deals.php',
        'current_page' => $current_page === 'overall_deals.php',
    ],
    [
        'title' => 'Agent Last Transaction',
        'icon' => 'clock',
        'link' => 'agent_last_transaction.php',
        'current_page' => $current_page === 'agent_last_transaction.php',
    ],
    [
        'title' => 'Team',
        'icon' => 'users',
        'link' => 'team.php',
        'current_page' => $current_page === 'team.php',
    ],
    [
        'title' => 'Agent Ranking',
        'icon' => 'trophy',
        'link' => 'agent_ranking.php',
        'current_page' => $current_page === 'agent_ranking.php',
    ],
];

?>


<div class="sidebar border-r-2 border-gray-700 bg-gray-800 text-white flex flex-col gap-4 w-[15rem] transition-all duration-300">
    <div class="p-6 flex justify-between items-center">
        <h1 id="sidebar-header" class="text-2xl font-semibold text-gray-300 text-center hover:text-gray-300">
            <a href="index.php">Dashboard</a>
        </h1>
        <button id="toggle-sidebar" class="text-gray-400 hover:text-gray-300 transition duration-200">
            <i class="fas fa-angle-double-left"></i>
        </button>
    </div>
    <div id="sidebar-content" class="pb-6 transition-all duration-300">
        <ul class="flex flex-col gap-1">
            <?php foreach ($sidebar_items as $item): ?>
                <li class="text-sm">
                    <a href="<?php echo $item['link'] ?>" class="block py-4 px-6 transition duration-200 hover:bg-gray-700 <?php echo $item['current_page'] ? 'bg-gray-700' : ''; ?>">
                        <span class="flex items-center">
                            <i class="fas fa-<?php echo $item['icon'] ?>"></i>
                            <span class="sidebar-list-item ms-2"><?php echo $item['title'] ?></span>
                        </span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<script>
    const sidebarCollapsedKey = 'sidebarCollapsed';

    function checkSidebarCollapsed() {
        return localStorage.getItem('sidebarCollapsed') === 'true';
    }

    const isSidebarCollapsed = checkSidebarCollapsed();


    if (isSidebarCollapsed) {
        document.getElementById('sidebar-header').classList.add('hidden');
        document.querySelectorAll('.sidebar-list-item').forEach(function(el) {
            el.classList.add('hidden');
        });
        document.getElementById('toggle-sidebar').classList.add('rotate-180');
        document.querySelector('.sidebar').classList.add('w-[4rem]');
        // document.querySelectorAll('.main-content-area').forEach(function(el) {
        //     el.style.width = '96%';
        // });

    }

    document.getElementById('toggle-sidebar').addEventListener('click', function() {
        const isCollapsed = document.getElementById('sidebar-header').classList.toggle('hidden');

        localStorage.setItem(sidebarCollapsedKey, isCollapsed);
        document.querySelectorAll('.sidebar-list-item').forEach(function(el) {
            el.classList.toggle('hidden');
        });
        document.getElementById('toggle-sidebar').classList.toggle('rotate-180');
        document.querySelector('.sidebar').classList.toggle('w-[4rem]');
        // document.querySelectorAll('.main-content-area').forEach(function(el) {
        //     el.style.width = checkSidebarCollapsed() ? '96%' : '85%';
        // });
    });
</script>

<style>
    #sidebar-content {
        transition: all 0.3s ease;
    }

    #toggle-sidebar {
        transition: all 0.3s ease;
    }

    .rotate-180 {
        transform: rotate(180deg);
    }
</style>