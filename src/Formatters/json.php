<?php

namespace Differ\Formatters;

function json(array $data): string
{
    return json_encode($data);
}