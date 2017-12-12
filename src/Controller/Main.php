<?php
/**
 * Created by PhpStorm.
 * User: test
 * Date: 12.12.17
 * Time: 08:15
 */

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Main
{
	/**
	 * @Route("/")
	 */
	public function main()
	{
		return new Response('hello');
	}

}
