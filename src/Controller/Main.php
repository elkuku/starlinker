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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Main extends Controller
{
    /**
     * @Route("/", name="welcome")
     *
     * @param StarLinker $starLinker
     * @param Request $request
     *
     * @return Response
     */
	public function main(StarLinker $starLinker, Request $request)
	{
        $showDate = $request->query->get('showdate');

        $dt = $showDate ? new \DateTime($showDate) : new \DateTime();

		$stats = $starLinker->getFormatForJsChart($dt);
		$diff = $starLinker->getTopTenDiff($dt);

		return $this->render(
			'default/index.html.twig',
			[
				'stats' => $stats,
                'toptenDiff' => $diff
			]
		);
	}

    /**
     * @Route("/about", name="about")
     * @return Response
     */
    public function about()
    {
        return $this->render('default/about.html.twig');
	}
}
