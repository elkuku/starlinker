<?php
/**
 * Created by PhpStorm.
 * User: test
 * Date: 10.12.17
 * Time: 18:05
 */

namespace App\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Fetch extends Command
{
	protected function configure()
	{
		$this
			->setName('app:fetch')
			->setDescription('Fetch StarLink stats.')
			->setHelp('This command fetches StarLink data.');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$output->writeln('Whoa!');

		$data = $this->fetchData();

		$table = new Table($output);
		$table
			->setHeaders(array('#', 'Captain', 'Score'));

		for ($i = 0; $i < 5; $i++)
		{
			$d = explode("\t", $data[$i]);
			$table->addRow([$i + 1, $d[1], $d[3]]);
		}

		$table->render();
	}

	private function fetchData()
	{

		$url = 'http://starlink.tasharen.com/ranked.php';

		$ch=curl_init();
		$timeout=5;

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

		$content = curl_exec($ch);

		curl_close($ch);

		$lines = explode("\n", $content);
		array_pop($lines);

		usort($lines, function ($a, $b){
			$as = explode("\t", $a);
			$bs = explode("\t", $b);

			$sField = 3;

			if ($as[$sField] === $bs[$sField])
			{
				return 0;
			}

			return $as[$sField] > $bs[$sField] ? -1 : 1;
		});

		return $lines;
	}
}
