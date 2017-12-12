<?php
/**
 * Created by PhpStorm.
 * User: test
 * Date: 10.12.17
 * Time: 18:05
 */

namespace App\Command;


use App\Helper\StarLinker;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

class Fetch extends ContainerAwareCommand
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
		$io = new SymfonyStyle($input, $output);

		$root     = $this->getContainer()->get('kernel')->getProjectDir();
		$fileName = $root . '/data/' . date('Y-m-d--H') . '-starlink-data.txt';

		$data = $this->fetchData();

		file_put_contents($fileName, $data);

		$output->writeln(sprintf('File has been saved to "%s"', $fileName));

		$lines = StarLinker::sortByField(
			explode("\n", trim($data)), 3);

		$topten = [];

		for ($i = 0; $i < 5; $i++)
		{
			$d = explode("\t", $lines[$i]);

			$topten[$d[1]] = $d[3];
		}

		$response          = new \stdClass();
		$response->time    = (new \DateTime())->format('H:i');
		$response->topten   = $topten;

		$outputFile = $root.'/data/'.(new \DateTime())->format('Y-m-d').'-starlink-data.txt';

		$io->text('write to ' . $outputFile);
		$fs = new Filesystem();
		$fs->appendToFile($outputFile, json_encode($response) . "\n");
	}

	private function fetchData()
	{
		$url = 'http://starlink.tasharen.com/ranked.php';

		$ch      = curl_init();
		$timeout = 5;

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

		$content = curl_exec($ch);

		curl_close($ch);

		return $content;
	}
}
