<div class="mt-4 w-full flex justify-center gap-1 py-2">
    <?php if ($page > 1): ?>
        <a href="?page=<?= $page - 1 ?>&year=<?= isset($_GET['year']) ? $_GET['year'] : date('d/m/Y') ?>" class="bg-gray-500/40 border border-gray-800 rounded-md px-2 py-1 text-gray-800 dark:text-gray-100 text-xs font-medium hover:bg-gray-600 hover:text-gray-100">
            <i class="fas fa-chevron-left"></i>
        </a>
    <?php endif; ?>

    <?php
    $range = 2; // How many pages to show around current
    $dotShown = false;
    for ($i = 1; $i <= $total_pages; $i++) {
        if (
            $i == 1 ||
            $i == $total_pages ||
            ($i >= $page - $range && $i <= $page + $range)
        ) {
            if ($i == $page): ?>
                <button type="button" class="bg-indigo-500 rounded-md px-2 py-1 text-indigo-100 text-xs font-medium hover:bg-indigo-600 dark:hover:bg-indigo-600" disabled><?= $i ?></button>
            <?php else: ?>
                <a href="?page=<?= $i ?>&year=<?= isset($_GET['year']) ? $_GET['year'] : date('d/m/Y') ?>" class="bg-indigo-500/40 border border-indigo-800 rounded-md px-2 py-1 text-gray-800 dark:text-indigo-100 text-xs font-medium hover:bg-indigo-600 hover:text-white"><?= $i ?></a>
    <?php endif;
            $dotShown = false;
        } elseif (!$dotShown) {
            echo '<span class="px-2 text-gray-500 dark:text-gray-400 text-xs">...</span>';
            $dotShown = true;
        }
    }
    ?>

    <?php if ($page < $total_pages): ?>
        <a href="?page=<?= $page + 1 ?>&year=<?= isset($_GET['year']) ? $_GET['year'] : date('d/m/Y') ?>" class="bg-indigo-500/40 border border-indigo-800 rounded-md px-2 py-1 text-gray-800 dark:text-indigo-100 text-xs font-medium hover:bg-indigo-600 hover:text-white">
            <i class="fas fa-chevron-right"></i>
        </a>
    <?php endif; ?>
</div>