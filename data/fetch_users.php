<?php
require_once __DIR__ . '/../crest/crest.php';
require_once __DIR__ . '/../crest/settings.php';
require_once __DIR__ . '/../crest/crestcurrent.php';


function getUsers()
{
    $users = [];


    $next = 0;
    $leftUser = true;

    while ($leftUser) {
        $result = CRest::call('user.get', [
            'select' => ['*', 'UF_*'],
            'filter' => ['UF_DEPARTMENT' => [5, 78, 77, 440], 'ACTIVE' => 'Y'],
            'start' => $next,
        ]);
        $users = array_merge($users, $result['result']);

        if (!isset($result['next'])) {
            $leftUser = false;
        } else {
            $next = $result['next'];
        }
    }

    return $users;
}
function get_filtered_users($filter = [], $select = [], $order = [])
{
    $users = [];

    $no_of_users = CRest::call('user.get', [
        'filter' => [...$filter, 'ACTIVE' => 'Y'],
    ])['total'];
    // error_log("Total number of users: $no_of_users\n", 3, __DIR__ . '/debug.log');

    $no_of_request_required = ceil($no_of_users / 50); // bitrix sends 50 response per request
    error_log("no_of_request_required: $no_of_request_required\n", 3, __DIR__ . '/debug.log');

    $batch = [];
    $step = 0;
    for ($i = 0; $i < $no_of_request_required; $i++) {
        // make batches for different start
        $batch['step_' . $step] = [
            'method' => 'user.get',
            'params' => [
                'select' => $select ?? ['*', 'UF_*'],
                'filter' => [...$filter, 'ACTIVE' => 'Y'],
                'order' => $order,
                'start' => $i * 50,
            ],
        ];

        if ($step == 49 || $step == $no_of_request_required - 1) {
            $result = CRest::callBatch($batch, 1)['result']['result'];

            while ($step >= 0) { // Changed from step > 0 to step >= 0 to include the last batch
                $users = array_merge($users, $result['step_' . ($no_of_request_required - $step - 1)]);
                $step--;
            }
            $batch = [];
        } else $step++;
    }
    $no_of_users = count($users);
    error_log("Total number of users: $no_of_users\n", 3, __DIR__ . '/debug.log');
    return $users;
}

function get_paginated_users($page = 1, $filter = [], $select = [], $order = [])
{
    $users = [];
    $start = ($page - 1) * 50;

    $result = CRest::call('user.get', [
        'select' => $select ?? ['*', 'UF_*'],
        'filter' => [...$filter, 'ACTIVE' => 'Y'],
        'order' => $order,
        'start' => $start,
    ]);

    $users = $result['result'];
    $total = $result['total'];

    return ['users' => $users, 'total' => $total];
}

function get_user_fields()
{
    $result = CRest::call('user.fields', ['select' => ['*', 'UF_*']]);

    return $result['result'];
}

function get_custom_user_fields()
{
    $result = CRest::call('user.userfield.list');

    return $result;
}

function getUser($user_id)
{
    $result = CRest::call('user.get', ['ID' => $user_id]);
    $user = $result['result'][0];
    return $user;
}

function getCurrentUser()
{
    $result = CRestCurrent::call('user.current');
    $user = $result['result'];

    return $user;
}
