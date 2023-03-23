<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parse(string $content, string $type): object
{
    switch ($type) {
        case 'json':
            return json_decode($content, false);
        case 'yaml':
        case 'yml':
            return Yaml::parse($content, Yaml::PARSE_OBJECT_FOR_MAP);
        default:
            throw new \Exception("Unknown file type {$type}");
    }
}