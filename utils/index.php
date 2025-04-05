<?php

// Add resuable utility functions here

// calculate duration in months
function duration_months($date)
{
    $date1 = new DateTime($date);
    $date2 = new DateTime();
    $interval = $date1->diff($date2);
    return $interval->m + ($interval->y * 12);
}

// map enum with the values
function map_enum($fields, $field_id, $key_to_map)
{
    foreach ($fields as $field) {
        if (isset($field['title']) && $field['title'] == $field_id) {
            foreach ($field['items'] as $item) {
                if ($item['ID'] == $key_to_map) {
                    return $item['VALUE'];
                }
            }
        }
    }
}

function get_quarter($month)
{
    if ($month == 'Jan' || $month == 'Feb' || $month == 'Mar') {
        return 'Q1';
    } elseif ($month == 'Apr' || $month == 'May' || $month == 'Jun') {
        return 'Q2';
    } elseif ($month == 'Jul' || $month == 'Aug' || $month == 'Sep') {
        return 'Q3';
    } elseif ($month == 'Oct' || $month == 'Nov' || $month == 'Dec') {
        return 'Q4';
    }
}

function clearCache($fileName)
{
    $filePath = 'cache/' . $fileName;
    if (file_exists($filePath)) {
        unlink($filePath);
    }
}

function mapDeveloperName($developerId)
{
    $developers = [
        '503' => 'Emaar',
        '504' => 'Damac',
        '505' => 'Nakheel',
        '506' => 'Arada',
        '507' => 'Ellington Properties',
        '508' => 'Danube',
        '509' => 'Binghatti',
        '510' => 'Sobha',
        '511' => 'Tiger Properties',
        '582' => 'Bloom Properties',
        '619' => 'Others',
        '4373' => 'District One',
        '4385' => 'Dubai Properties',
        '4464' => 'Azizi',
    ];

    return isset($developers[$developerId]) ? $developers[$developerId] : 'Unknown Developer';
}
