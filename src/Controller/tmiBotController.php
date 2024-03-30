<?php
namespace App\Controller;

use App\Service\tmiBotService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class tmiBotController extends AbstractController
{
	public function __construct(private tmiBotService $tmiBotService) {}

	#[Route('/run', name: 'run_bot')]
	public function run_bot(): Response
	{
		$client = $this->tmiBotService->setupClient(
			$this->getParameter('app.twitch_uname'),
			$this->getParameter('app.twitch_token'),
			$this->getParameter('app.twitch_channels')
		);
		$this->tmiBotService->setupEvents($client, $this->getParameter('app.twitch_uname'));
		try {
			$this->tmiBotService->connectClient($client);
		} catch (Exception $e) {
			return new Response('Client failed to connect with error', 500);
		}
		return new Response('All good');
	}
}