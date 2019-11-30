<?php

use TaskForce\classes\Task;
use TaskForce\handlers\create_dumps\CsvDatasetParser;
use TaskForce\handlers\create_dumps\SqlDumpBuilder;

require_once '../vendor/autoload.php';

$data = CsvDatasetParser::parse(__DIR__ . '/data/cities.csv');

$builder = new SqlDumpBuilder();
$builder->setDatabase('task_force');

$tables = [
    'cities',
    'categories',
    'opinions',
    'users',
    'tasks',
    'statuses',
    'replies',
    'opinions',
];

foreach ($tables as $table) {
    $data = CsvDatasetParser::parse(__DIR__ . '/data/' . $table . '.csv');
    $builder->setTableSettings($table, $data['columnNames']);
    $builder->createDump($data['data']);
}
