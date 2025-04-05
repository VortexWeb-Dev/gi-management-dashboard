<?php
require_once __DIR__ . '/../crest/crest.php';

// get all the deals
function get_all_deals()
{
    $result = CRest::call('crm.deal.list', [
        'select' => ['*', 'UF_*'],
        'filter' => ['CATEGORY_ID' => 0],
    ]);
    $deals = $result['result'];
    return $deals;
}


// get deals by id
function getDeal($deal_id)
{
    $result = CRest::call('crm.deal.get', ['ID' => $deal_id]);
    $deal = $result['result'];

    return $deal;
}


// get filtered deals
function getFilteredDeals($filter = [], $select = null, $order = null)
{
    $result = CRest::call('crm.deal.list', [
        'select' => $select ?? ['*', 'UF_*'],
        'filter' => $filter,
        'order' => $order,
    ]);
    $deals = $result['result'];
    return $deals;
}

// get paginated deals
function get_paginated_deals($page = 1, $filter = [], $select = null, $order = null)
{
    $result = CRest::call('crm.deal.list', [
        'select' => $select ?? ['*', 'UF_*'],
        'filter' => $filter,
        'order' => $order,
        'start' => ($page - 1) * 50,
    ]);
    $deals = $result['result'];
    $totalDeals = $result['total'];
    return ['deals' => $deals, 'total' => $totalDeals];
}

// get deal fields
function get_deal_fileds()
{

    $result = CRest::call('crm.deal.fields', [
        'select' => ['*', 'UF_*'],
        // 'filter' => [
        //     'CATEGORY_ID' => 0
        // ]
    ]);
    $fields = $result['result'];
    return $fields;
}
