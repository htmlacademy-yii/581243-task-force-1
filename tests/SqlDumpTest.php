<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use TaskForce\handlers\create_dumps\CsvDatasetParser;
use TaskForce\handlers\create_dumps\SqlDumpBuilder;

class SqlDumpTest extends TestCase
{
    public function testDumpCreation(): void
    {
        $file = __DIR__ . '/../frontend/web/data/opinions.csv';

        $data = CsvDatasetParser::parse($file);

        /*$this->assertEquals([
            'created_at',
            'rate',
            'comment',
            'author_id',
            'task_id',
            'evaluated_user_id',
        ], $data['columnNames']);*/

        $builder = new SqlDumpBuilder();
        $builder->setDatabase('task_force');
        $builder->setTableSettings('opinions', $data['columnNames']);
        $builder->createDump($data['data']);

        $file = $builder->createDump($data['data']);
        $this->assertTrue(file_exists($file));
        unlink($file);
    }
}
