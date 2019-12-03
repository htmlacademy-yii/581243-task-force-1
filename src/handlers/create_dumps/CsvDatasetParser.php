<?php


namespace TaskForce\handlers\create_dumps;


use TaskForce\exceptions\ParserException;

/**
 * Class CsvDatasetParser
 * @package TaskForce\handlers\create_dumps
 */
class CsvDatasetParser implements DatasetParser
{
    /**
     * @param $file
     * @return array
     * @throws ParserException
     */
    public static function parse($file): array
    {
        if (!is_string($file)) {
            throw new \TaskForce\exceptions\ParserException('file must be a string');
        }

        if (!file_exists($file)) {
            throw new \TaskForce\exceptions\ParserException('file not exist');
        }

        $file = new \SplFileObject($file);
        $file->setFlags(\SplFileObject::SKIP_EMPTY);
        $data = [];
        $data['columnNames'] = [];

        while (!$file->eof()) {
            $tr = [];

            $row = $file->fgetcsv();
            if (!$row) {
                continue;
            }

            foreach ($row as $td) {
                $tr[] = htmlspecialchars($td, ENT_QUOTES);
            }

            if (empty($data['columnNames'])) {
                $data['columnNames'] = $tr;
            } else {
                $data['data'][] = $tr;
            }
        }

        return $data;
    }
}
