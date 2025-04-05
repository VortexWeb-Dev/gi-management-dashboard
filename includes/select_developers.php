 <!-- buttons div -->
 <div class="flex items-center gap-2">
     <button id="dropdownSearchButton" data-dropdown-toggle="dropdownSearch" data-dropdown-placement="bottom" class="white-gary-800 dark:text-white border border-gray-300 dark:border-blue-800 hover:bg-blue-600 hover:text-white focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" type="button">
         <?= isset($_GET['developer_name']) && $_GET['developer_name'] ? $_GET['developer_name'] : 'Select Developers' ?>
         <svg class="w-2.5 h-2.5 ms-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
             <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
         </svg>
     </button>
     <a href="management_dashboard.php?year=<?= $_GET['year'] ?? date('m/d/Y') ?>" id="clearFilterButton" class="<?= $developer_name ? '' : 'hidden' ?> text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800" type="button">
         <svg class="w-4 h-4" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor">
             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
         </svg>
         <!-- <img src="./assets//images//clear-filter-icon.png" alt="clear filter" class="w-4 h-4 text-white"> -->
         <p class="ml-2">Clear</p>
     </a>

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
             <input type="text" id="input-group-search" class="block w-full p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search Developrs">
         </div>
     </div>
     <ul class="h-48 px-3 pb-3 overflow-y-auto text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownSearchButton">
         <?php foreach ($developers as $index => $developer): ?>
             <li id="<?= $developer ?>" class="mb-1 <?= isset($_GET['developer_name']) && $developer == $_GET['developer_name'] ? 'bg-gray-100 dark:bg-gray-600' : '' ?>">
                 <form class="select-developer-form" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="get">
                     <div class="flex items-center ps-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                         <input type="text" name="developer_name" value="<?= $developer ?>" hidden>
                         <input type="text" name="year" value="<?= $_GET['year'] ?? date('m/d/Y') ?>" hidden>
                         <button type="submit" <?= isset($_GET['developer_name']) && $developer == $_GET['developer_name'] ? 'disabled' : '' ?> class="w-full text-start py-2 ms-2 text-sm font-medium text-gray-900 rounded dark:text-gray-300"><?= $developer ?></button>
                     </div>
                 </form>
             </li>
         <?php endforeach; ?>
     </ul>
 </div>
 <script>
     document.getElementById('input-group-search').addEventListener('input', function() {
         var input = this.value.toLowerCase();
         let developers = <?= json_encode($developers) ?>;

         // Loop through options and hide those that don't match the search query
         developers.forEach(function(developer) {
             var option = document.getElementById(developer);
             var optionText = developer.toLowerCase();
             option.style.display = optionText.includes(input) ? 'block' : 'none';
         });
     });

     //  show loader
     //  $(document).ready(function() {
     //      $('.select-developer-form').submit(function() {
     //          $('#loader').show(); // Show the loader on form submit
     //      });
     //  });s
 </script>