<?php

namespace Differ\Formatters;

const TAB = "    ";

function stylish(array $data, int $depth = 0): string
{
    $lines = array_map(
        fn ($item) => makeLine($item, $depth),
        $data
    );
    $result = ['{', ...$lines, str_repeat(TAB, $depth) . '}'];

    return implode(PHP_EOL, $result);
}

function makeLine(array $node, int $depth = 0): string
{
    $types = ['children', 'value', 'expectedValue', 'currentValue'];
    $key = $node['name'];
    $indent = str_repeat(TAB, $depth);

    $currentTypes = array_filter(
        $types,
        fn ($type) =>
        array_key_exists($type, $node)
    );

    $lines = array_map(
        function ($type) use ($key, $indent, $node, $depth) {
            $currentNode = $node[$type];
            if (is_array($currentNode)) {
                return $indent . getPrefix($type) . $key . ": " . stylish($currentNode, $depth + 1);
            }
            return $indent . getPrefix($type) . $key . ": " . toString([$currentNode]);
        },
        $currentTypes
    );

    return implode(PHP_EOL, $lines);
}

function getPrefix(string $type): string
{
    switch ($type) {
        case 'expectedValue':
            return substr_replace(TAB, '- ', -2);
        case 'currentValue':
            return substr_replace(TAB, '+ ', -2);
    }
    return TAB;
}

function toString(array $data): string
{
    $value = $data[0];
    $string = json_encode($value);
    if ($string === false) {
        throw new \Exception("Unknown format");
    }
    return trim($string, '"');
}