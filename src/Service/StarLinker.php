<?php
/**
 * Created by PhpStorm.
 * User: elkuku
 * Date: 16/05/17
 * Time: 2:45
 */

namespace App\Service;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class StarLinker
 */
class StarLinker
{
    private $projectDir;

    /**
     * ShaFinder constructor.
     *
     * @param string $projectDir
     */
    public function __construct(string $projectDir)
    {
	    $this->projectDir = $projectDir;
    }

	public function getStatsByDate(\DateTime $dateTime)
	{
		$fs = new Filesystem();

		$stats = [];
		$fileName = $dateTime->format('Y-m-d') . '-starlink-data.txt';

		$path = $this->projectDir . '/data/' . $fileName;

		if (false === $fs->exists($path))
		{
			return $stats;
		}

		$contents = trim(file_get_contents($path));

	}

	public function getTopTen()
	{
		$fs = new Filesystem();

		$topTen = [];
		$fileName = date('Y-m-d--H') . '-starlink-data.txt';

		$path = $this->projectDir . '/data/' . $fileName;

		if (false === $fs->exists($path))
		{
			return $topTen;
		}

		$contents = trim(file_get_contents($path));

		$data = StarLinker::sortByField(explode("\n", $contents), 3);

		for ($i = 0; $i < 5; $i++)
		{
			$d = explode("\t", $data[$i]);

			$topTen[$d[1]] = $d[3];
		}

		return $topTen;
    }

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

}
