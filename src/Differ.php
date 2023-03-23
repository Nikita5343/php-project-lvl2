<?php

namespace Differ\Differ;

use function Differ\Parsers\parse;
use function Differ\Formatters\format;
use function Functional\sort as f_sort;

function genDiff(string $pathToFile1, string $pathToFile2, string $format = 'stylish'): string
{
    $obj1 = readFile($pathToFile1);
    $obj2 = readFile($pathToFile2);

    return format(makeDiff($obj1, $obj2), $format);
}

function readFile(string $path): object
{
    $content = file_get_contents($path);

    if ($content === false) {
        throw new \Exception("Error file reading {$path}");
    }

    $type = pathinfo($path, PATHINFO_EXTENSION);

    return parse($content, $type);
}

function makeDiff(object $obj1, object $obj2): array
{
    $keys1 = array_keys(get_object_vars($obj1));
    $keys2 = array_keys(get_object_vars($obj2));
    $keys = sortArrayValues(array_unique(array_merge($keys1, $keys2)));

    return  array_map(
        fn ($key) => makeDiffNode($key, $obj1, $obj2),
        $keys
    );
}

function makeDiffNode(string $name, object $expected, object $current): array
{
    if (!property_exists($expected, $name)) {
        $currentValue = makeNode([$current->$name]);
        $type = 'added';

        return compact('name', 'currentValue', 'type');
    }
    if (!property_exists($current, $name)) {
        $expectedValue = makeNode([$expected->$name]);
        $type = 'deleted';

        return compact('name', 'expectedValue', 'type');
    }
    if (is_object($expected->$name) && is_object($current->$name)) {
        $children = makeDiff($expected->$name, $current->$name);
        $type = 'object';
        $result = compact('name', 'children', 'type');
    } elseif ($expected->$name === $current->$name) {
        $value = makeNode([$current->$name]);
        $type = 'nested';
        $result = compact('name', 'value', 'type');
    } else {
        $currentValue = makeNode([$current->$name]);
        $expectedValue = makeNode([$expected->$name]);
        $type = 'updated';
        $result = compact('name', 'currentValue', 'expectedValue', 'type');
    }

    return $result;
}

function makeNode(array $arrayData)
{
    $data = $arrayData[0];

    if (!is_object($data)) {
        return $data;
    }

    $keys = sortArrayValues(array_keys(get_object_vars($data)));

    $result = array_map(
        fn ($key) => is_object($data->$key) ?
            [
                'name' => $key,
                'children' => makeNode([$data->$key])
            ] :
            [
                'name' => $key,
                'value' => makeNode([$data->$key])
            ],
        $keys
    );
    return $result;
}

function sortArrayValues(array $array): array
{
    return f_sort(
        $array,
        function ($left, $right) {
            return strcmp($left, $right);
        }
    );
}