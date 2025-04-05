<div class="py-3 mb-4 sticky top-0 z-50">
    <form id="myForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="GET">
        <div class="flex justify-between gap-4">
            <div class="flex items-center">
                <h1 class="text-xl font-bold dark:text-gray-100">Financial Year : <?= $selected_year ?></h1>
            </div>
            <div class="flex flex-end justify-end gap-2">
                <!-- <div class="relative max-w-sm">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                        </svg>
                    </div>
                    <input id="datepicker-actions" datepicker datepicker-buttons datepicker-autoselect-today type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Select date" name="year">
                </div>
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Submit</button> -->

                <div class="border border-gray-300 dark:border-gray-700 rounded-md">
                    <!-- <label for="year" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Select Year:</label> -->
                    <select id="year" name="year" class="block w-full pl-4 pr-10 py-3 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                        <?php for ($i = 2020; $i <= date('Y'); $i++): ?>
                            <option value="<?= "1/1/$i" ?>" <?= $i == $selected_year ? 'selected' : '' ?>><?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Apply
                </button>
            </div>
        </div>
    </form>
</div>

<?php include_once('./includes/loader.php') ?>

<script>
    // $(document).ready(function() {
    //     $('#loader').hide(); // Hide the loader initially

    //     $('#myForm').submit(function() {
    //         $('#loader').show(); // Show the loader on form submit
    //     });
    // });
</script>