<?php


namespace Tests;


use PHPUnit\Framework\TestCase;
use TaskForce\handlers\create_dumps\CsvDatasetParser;
use TaskForce\handlers\create_dumps\SqlDumpBuilder;

class SqlDumpTest extends TestCase
{
    public function testDumpCreation()
    {
        $file = __DIR__ . '/../frontend/web/data/cities.csv';

        $data = CsvDatasetParser::parse($file);

        $this->assertEquals(["city", 'lat', 'long'], $data['columnNames']);

        $builder = new SqlDumpBuilder();
        $builder->setDatabase('task_force');
        $builder->setTableSettings('cities', $data['columnNames']);
        $builder->createDump($data['data']);

        $file = $builder->createDump($data['data']);
        $this->assertTrue(file_exists($file));
        unlink($file);
    }
}
