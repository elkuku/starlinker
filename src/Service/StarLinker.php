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

	private $colors = [
		//red:
		'rgb(255, 99, 132)',
		// orange:
		'rgb(255, 159, 64)',
		// yellow:
		'rgb(255, 205, 86)',
		// green:
		'rgb(75, 192, 192)',
		// blue:
		'rgb(54, 162, 235)',
		// purple:
		'rgb(153, 102, 255)',
		// grey:
		'rgb(201, 203, 207)'
	];

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

		$stats    = [];
		$fileName = $dateTime->format('Y-m-d') . '-starlink-data.txt';

		$path = $this->projectDir . '/data/' . $fileName;

		if (false === $fs->exists($path))
		{
			return $stats;
		}

		$contents = trim(file_get_contents($path));

		return explode("\n", $contents);
	}

	public function getTopTen()
	{
		$fs = new Filesystem();

		$topTen   = [];
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

	public function getFormatForJsChart(\DateTime $dateTime)
	{
		$lines = $this->getStatsByDate($dateTime);

		$format = new \stdClass();

		$dateString = $dateTime->format('Y-m-d');

		$topTens = [];
		$headers = [];

		$kukuHeaders = [];
		$kukuData    = [];

		foreach ($lines as $line)
		{
			$data = json_decode($line);

			$headers[] = $dateString . ' ' . $data->time;

			foreach ($data->topten as $name => $score)
			{
				if ($name)
				{
					$topTens[$name][] = $score;

					if ('Kuku ' === $name)
					{
						$kukuHeaders[] = $dateString . ' ' . $data->time;
						$kukuData[]    = $score;
					}
				}
				else
				{
					foreach ($topTens as $ix => $vals)
					{
						$topTens[$ix][] = 0;
					}
				}
			}
		}

		$format->headerString = '\'' . implode('\',\'', $headers) . '\'';

		$topTensLines = [];

		$i = 0;

		foreach ($topTens as $name => $scores)
		{
			$topTensLines[] = 'label: \'' . $name . '\','
				. 'borderColor: \'' . (isset($this->colors[$i]) ? $this->colors[$i] : $this->colors[0]) . '\','
				. 'data: [' . implode(',', $scores) . ']';
			$i++;
		}

		$format->topTenLines = '{' . implode('},{', $topTensLines) . '}';

		$format->kukuHeaderString = '\'' . implode('\',\'', $kukuHeaders) . '\'';
		$format->kukuDataString   = '{label: \'KuKu\','
			.'borderColor: \'' . $this->colors[0] . '\','
			.'data: [' . implode(',', $kukuData) . ']}';

		return $format;
	}

	public function getTopTenDiff(\DateTime $dateTime)
	{
		$lines = $this->getStatsByDate($dateTime);

		if (!$lines)
		{
			return [];
		}

		$diff = [];

		$data1 = json_decode($lines[0]);
		$data2 = json_decode(end($lines));

		foreach ($data2->topten as $name => $score)
		{
			$data        = new \stdClass();
			$data->end   = $score;
			$diff[$name] = $data;
		}

		foreach ($data1->topten as $name => $score)
		{
			if (isset($diff[$name]))
			{
				$diff[$name]->start = $score;
				$diff[$name]->diff  = $diff[$name]->end - $score;
			}
		}

		return $diff;
	}

	public function getDiffChart()
	{
		$dateTime = (new \DateTime())->modify('+1 day');

		$chart            = [];
		$chart['headers'] = [];
		$chart['data']    = [];

		for ($i = 1; $i <= 30; $i++)
		{

			$dateTime->modify('-1 day');

			$lines = $this->getStatsByDate($dateTime);

			if (!$lines)
			{
				continue;
			}

			$diff = [];

			$data1 = json_decode($lines[0]);
			$data2 = json_decode(end($lines));

			foreach ($data2->topten as $name => $score)
			{
				$diff[$name] = $score;
			}

			foreach ($data1->topten as $name => $score)
			{
				if (isset($diff[$name]))
				{
					$chart['data'][$name][$dateTime->format('Y-m-d')] = $diff[$name] - $score;
				}
			}

			$chart['headers'][] = $dateTime->format('Y-m-d');
		}

		foreach ($chart['headers'] as $dt)
		{
			foreach ($chart['data'] as $name => $scores)
			{
				if (false === isset($chart['data'][$name][$dt]))
				{
					$chart['data'][$name][$dt] = 0;
				}
			}
		}

		$diffChart = new \stdClass();

		$diffChart->headerString = '\'' . implode('\',\'', $chart['headers']) . '\'';

		$data = [];
		$i    = 0;

		foreach ($chart['data'] as $name => $scores)
		{
			$data[] = 'label: \'' . $name . '\','
				. 'borderColor: \'' . (isset($this->colors[$i]) ? $this->colors[$i] : $this->colors[0]) . '\','
				. ' data: [' . implode(',', $scores) . ']';
			$i++;
		}

		$diffChart->data = '{' . implode('},{', $data) . '}';


		return $diffChart;
	}
}
