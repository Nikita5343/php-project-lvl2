<?php

namespace Differ\Formatters;

function plain(array $data, string $path = ''): string
{
    $result = array_map(
        fn ($node) => makePlainLine($node, $path),
        $data
    );

    return implode(PHP_EOL, array_filter($result));
}

function makePlainLine(array $node, string $keys): string
{
    $path = $keys === '' ? $node ['name'] : "{$keys}.{$node['name']}";
    $type = $node['type'] ?? '';

    switch ($type) {
        case 'object':
            return plain($node['children'], $path);
        case 'deleted':
            $message = "was removed";
            break;
        case 'added':
            $value = getValue([$node['currentValue']]);
            $message = "was added with value: {$value}";
            break;
        case 'updated':
            $value1 = getValue([$node['expectedValue']]);
            $value2 = getValue([$node['currentValue']]);
            $message = "was updated. From {$value1} to {$value2}";
            break;
        default:
            return '';
    }
    return "Property '{$path}' {$message}";
}

function getValue(array $data): string
{
    if (is_array($data[0])) {
        return '[complex value]';
    }

    $string = json_encode($data[0]);
    if ($string === false) {
        throw new \Exception("Unknown format");
    }

    return str_replace('"', "'", $string);
}