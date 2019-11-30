<?php


namespace TaskForce\handlers\create_dumps;


interface DumpBuilder
{
    /**
     * @param array $data
     * @return string|null
     */
    public function createDump(array $data): ?string;
}
