<?php


namespace TaskForce\handlers\create_dumps;


interface DatasetParser
{
    /**
     * @param $resource
     * @return array
     */
    public static function parse($resource): array;
}
