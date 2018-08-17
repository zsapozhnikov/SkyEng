<?php

/**
 * Need to be upgraded for floats and negative numbers
 */
function sum($a, $b)
{
    $partSize = 6; //split by 6 symbols
    $steps = $this->getSteps($a, $b, $partSize);
    $step = 0;
    $result = [];

    $arrayA = $this->convertStringToArray($a, $partSize);
    $arrayB = $this->convertStringToArray($b, $partSize);

    while ($step <= $steps) {
        $partA = (int) ($arrayA[$step] ?? 0);
        $partB = (int) ($arrayB[$step] ?? 0);
        $currentValue = (int) ($result[$step] ?? 0);

        $tmpResult = $partA + $partB;
        $newValue = $tmpResult + $currentValue;
        $newIntValueArray = $this->convertStringToArray((string) $newValue, $partSize);

        /**
         * add leading zeros to make length equal to $partSize
         */
        $result[$step] = sprintf('%0' . $partSize . 'd', $newIntValueArray[0]);

        /**
         * if result string is longer, then $partSize, move the rest to the next step
         */
        if (isset($newIntValueArray[1])) {
            $result[$step + 1] = $newIntValueArray[1];
        }

        $step++;
    }

    return ltrim($result, '0');
}

function getSteps($a, $b, $partSize)
{
    $max = max(array_map('strlen', [$a, $b]));

    return  (int) ceil($max / $partSize);
}

function convertStringToArray($str, $partSize)
{
    return array_map('strrev', str_split(strrev($str), $partSize));
}

/**
 * Unit for `sum` function
 *
 * @dataProvider getNumbers
 */
function testSum($a, $b, $result)
{
    $this->assertEquals($result, sum($a, $b));
}

function getNumbers()
{
    return [
        ['1', '1', '2'],
        ['75654', '0', '75654'],
        [
            '340282366920938463463374607431768211405',
                      '23912310230123182389124860743',
            '340282366944850773693497789820893072148'
        ]
    ];
}
