<?php
include_once "./crest/crest.php";
include_once "./crest/settings.php";
include('includes/header.php');

$departments = CRest::call('department.get');

$users = CRest::call('user.get', [
    'FILTER' => ['ACTIVE' => 'Y'],
    'SELECT' => ['ID', 'NAME', 'LAST_NAME', 'UF_DEPARTMENT', 'WORK_POSITION']
]);

$teams = [];

if (!empty($departments['result']) && !empty($users['result'])) {
    foreach ($departments['result'] as $dept) {
        $teams[$dept['NAME']] = [];
    }

    foreach ($users['result'] as $user) {
        if (!empty($user['UF_DEPARTMENT'])) {
            foreach ($user['UF_DEPARTMENT'] as $deptId) {
                foreach ($departments['result'] as $dept) {
                    if ($dept['ID'] == $deptId) {
                        $teams[$dept['NAME']][] = [
                            'name' => $user['NAME'] . ' ' . $user['LAST_NAME'],
                            'role' => $user['WORK_POSITION'] ?? 'Member',
                        ];
                    }
                }
            }
        }
    }
}
?>


<div class="flex w-full h-screen">
    <?php include('includes/sidebar.php') ?>

    <div class="main-content-area flex-1 overflow-y-auto bg-gray-100 dark:bg-gray-900">
        <?php include('includes/navbar.php'); ?>
        <div class="px-8 py-6">
            <div class="container mx-auto">
                <header class="flex flex-col md:flex-row justify-between items-center mb-8 space-y-4 md:space-y-0">
                    <h1 class="text-3xl font-bold dark:text-white">Teams</h1>
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <input
                                type="text"
                                id="searchInput"
                                placeholder="Search team leaders or members"
                                class="pl-10 pr-4 py-2 rounded-full bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                        </div>
                    </div>
                </header>
                <div id="teamStructure" class="space-y-8">
                    <?php foreach ($teams as $team_leader => $members): ?>
                        <div class="team-leader bg-white dark:bg-gray-800 rounded-lg shadow-md p-6" data-leader-id="<?= $team_leader ?>" data-leader-name="<?= $team_leader ?>">
                            <div class="flex items-center justify-between">
                                <h2 class="text-2xl font-semibold dark:text-white"><?= $team_leader ?></h2>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
                                <?php if (empty($members)): ?>
                                    <div class="team-member flex flex-col space-y-1 border rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 dark:border-gray-600 p-4">
                                        <span class="font-medium dark:text-gray-300">No members found</span>
                                    </div>
                                <?php endif; ?>

                                <?php foreach ($members as $member): ?>
                                    <div class="team-member border rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 dark:border-gray-600 p-4" data-member-name="<?= $member['name'] ?>">
                                        <span class="font-medium dark:text-gray-300"><?= $member['name'] ?></span> - <span class="font-small dark:text-gray-200"><?= $member['role'] ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('searchInput').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const teamLeaders = document.querySelectorAll('.team-leader');

        teamLeaders.forEach(leader => {
            const leaderName = leader.getAttribute('data-leader-name').toLowerCase();
            const teamMembers = leader.querySelectorAll('.team-member') || [];
            let leaderVisible = false;

            if (leaderName.includes(searchTerm)) {
                leaderVisible = true;
                teamMembers.forEach(member => {
                    member.style.display = 'flex';
                });
            } else {

                teamMembers.forEach(member => {
                    const memberName = member.getAttribute('data-member-name').toLowerCase();
                    if (memberName.includes(searchTerm)) {
                        leaderVisible = true;
                        member.style.display = 'flex';
                    } else {
                        member.style.display = 'none';
                    }
                });
            }


            leader.style.display = leaderVisible ? 'block' : 'none';
        });
    });
</script>

<?php include('includes/footer.php'); ?>