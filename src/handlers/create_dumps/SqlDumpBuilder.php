<?php

namespace TaskForce\handlers\create_dumps;

use TaskForce\exceptions\DumpBuilderException;

/**
 * Class SqlDumpBuilder
 * @package TaskForce\handlers\create_dumps
 */
class SqlDumpBuilder implements DumpBuilder
{
    protected $columns = [];
    protected $database = null;
    protected $table = null;

    /**
     * @param string $database
     */
    public function setDatabase(string $database): void
    {
        $this->database = $database;
    }

    /**
     * @param string $table
     * @param array $columns
     * @throws DumpBuilderException
     */
    public function setTableSettings(string $table, array $columns): void
    {
        $this->table = $table;

        foreach ($columns as $columnName) {
            if (!is_string($columnName)) {
                throw new DumpBuilderException('Column names must be a sting');
            }
        }

        $this->columns = $columns;
    }

    /**
     * @param array $data
     * @return string|null
     * @throws DumpBuilderException
     */
    public function createDump(array $data): ?string
    {
        $this->checkDatabaseSetting();

        if (empty($data)) {
            throw new DumpBuilderException('Data is empty');
        }

        $output = 'INSERT INTO `' . $this->database .'`.`' . $this->table . '` (';
        foreach ($this->columns as $column) {
            $output .= '`' . $column . '`, ';
        }
        $output = trim($output, ', ');
        $output .= ') VALUES ';

        foreach ($data as $row) {
            if (count($row) !== count($this->columns)) {
                throw new DumpBuilderException(count($this->columns) . ' values required. ' . count($row) . ' given.');
            }

            $output .= ' (';
            foreach ($row as $td) {
                $output .= '\'' . $td . '\', ';
            }
            $output = trim($output, ', ');
            $output .= '), ';
        }
        $output = trim($output, ', ');
        $output .= ';';

        $fileName = __DIR__ . '/../../../frontend/web/data/sql/' . $this->table . '_INSERT_' . date('Y-m-d H:i:s', time()) . '.sql';
        $file = new \SplFileObject($fileName, 'w+');

        if ($file->fwrite($output)) {
            return $fileName;
        }

        return null;
    }

    /**
     * @throws DumpBuilderException
     */
    public function checkDatabaseSetting(): void
    {
        if (!$this->database) {
            throw new DumpBuilderException('Database is not set');
        }
        if (!$this->table) {
            throw new DumpBuilderException('Table is not set');
        }
        if (empty($this->columns)) {
            throw new DumpBuilderException('Column names are not set');
        }
    }
}
