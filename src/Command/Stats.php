<?php
/**
 * Created by PhpStorm.
 * User: test
 * Date: 12.12.17
 * Time: 09:21
 */

namespace App\Command;

use App\Helper\StarLinker;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Stats extends ContainerAwareCommand
{
	protected function configure()
	{
		$this
			->setName('app:stats')
			->setDescription('Display StarLink stats.')
			->setHelp('This command displays StarLink stats.');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$root     = $this->getContainer()->get('kernel')->getProjectDir();
		$fileName = date('Y-m-d--H') . '-starlink-data.txt';

		$contents = trim(file_get_contents($root . '/data/' . $fileName));

		$data = StarLinker::sortByField(
			explode("\n", $contents), 3);

		$table = new Table($output);

		$table->setHeaders(array('#', 'Captain', 'Score'));

		for ($i = 0; $i < 5; $i++)
		{
			$d = explode("\t", $data[$i]);

			$table->addRow([$i + 1, $d[1], $d[3]]);
		}

		$table->render();
	}
}
