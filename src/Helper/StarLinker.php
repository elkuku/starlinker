<?php
/**
 * Created by PhpStorm.
 * User: test
 * Date: 12.12.17
 * Time: 08:51
 */

namespace App\Helper;

class StarLinker
{
	public static function sortByField(array $array, int $field)
	{
		usort($array, function ($a, $b) {
			$as = explode("\t", $a);
			$bs = explode("\t", $b);

			// @todo change..
			$sField = 3;

			if ($as[$sField] === $bs[$sField])
			{
				return 0;
			}

			return $as[$sField] > $bs[$sField] ? -1 : 1;
		});

		return $array;
	}

	public static function splitLine(string $line)
	{

	}
}
