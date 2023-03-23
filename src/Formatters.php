<?php

namespace Differ\Formatters;

function format(array $data, string $format): string
{
    switch ($format) {
        case 'stylish':
            return stylish($data);
        case 'plain':
            return plain($data);
        case 'json':
            return json($data);
        default:
            throw new \Exception("Unknown format");
    }
}