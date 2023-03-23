<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class GenDiffTest extends TestCase
{
    private function getFixturePath(string $fileName): string
    {
        return implode(DIRECTORY_SEPARATOR, [__DIR__, "fixtures", $fileName]);
    }

    /**
     * @dataProvider additionProvider
     */

    public function testGenDiff($expected, $pathToFile1, $pathToFile2, $format = 'stylish'): void
    {
        $this->assertStringEqualsFile($expected, genDiff($pathToFile1, $pathToFile2, $format));
    }

    public function additionProvider()
    {
        $stylishFormat = 'stylish';
        $plainFormat = 'plain';
        $jsonFormat = 'json';

        $expectedStylish = $this->getFixturePath("stylish.txt");
        $expectedPlain = $this->getFixturePath("plain.txt");
        $expectedJson = $this->getFixturePath("json.txt");
        $file1Json = $this->getFixturePath('file1.json');
        $file2Yml = $this->getFixturePath('file2.yml');

        return [
            [$expectedStylish, $file1Json, $file2Yml],
            [$expectedStylish, $file1Json, $file2Yml, $stylishFormat],
            [$expectedPlain, $file1Json, $file2Yml, $plainFormat],
            [$expectedJson, $file1Json, $file2Yml, $jsonFormat],
        ];
    }
}