<?php
/**
 * Created by PhpStorm.
 * User: test
 * Date: 12.12.17
 * Time: 08:15
 */

namespace App\Controller;

use App\Service\StarLinker;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Main extends Controller
{
	/**
	 * @Route("/", name="welcome")
	 * @param StarLinker $starLinker
	 *
	 * @return Response
	 */
	public function main(StarLinker $starLinker)
	{
		$topTen = $starLinker->getTopTen();
		$stats = $starLinker->getFormatForJsChart(new \DateTime());
		$diff = $starLinker->getTopTenDiff(new \DateTime());

		return $this->render(
			'default/index.html.twig',
			[
				'stats' => $stats,
                'toptenDiff' => $diff
			]
		);
	}
}
