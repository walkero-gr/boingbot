<?php
namespace App\Controller;

use App\Service\tmiBotService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class webhookController extends AbstractController
{
	public function __construct(private tmiBotService $tmiBotService) {}

	// TODO: Need to subscribe to the events
	// https://dev.twitch.tv/docs/api/reference/#create-eventsub-subscription
	
	#[Route('/evsub', name: 'eventhandler')]
	#[Route('/evsub/', name: 'event_handler')]
	public function handleEvents(Request $request): Response
	{
		var_dump($request->headers->get('Twitch-Eventsub-Message-Id', ''));
		var_dump($request->headers->get('Twitch-Eventsub-Message-Type', ''));
		var_dump($request->headers->get('Twitch-Eventsub-Message-Signature', ''));
		var_dump($request->headers->get('Twitch-Eventsub-Message-Timestamp', ''));
		var_dump($request->headers->get('Twitch-Eventsub-Subscription-Type', ''));
		var_dump($request->headers->get('Twitch-Eventsub-Subscription-Version', ''));
		var_dump($request->getContent());
		// die('HERE');
		
		switch($request->headers->get('Twitch-Eventsub-Subscription-Type', '')) {
			case 'stream.online':
				// TODO: connect the bot in the chat
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

				// TODO: Update the token
				break;
			case 'stream.offline':
				// TODO: disconnect the bot in the chat
				// $client = $this->tmiBotService->setupClient(
				// 	$this->getParameter('app.twitch_uname'),
				// 	$this->getParameter('app.twitch_token'),
				// 	$this->getParameter('app.twitch_channels')
				// );
				// $this->tmiBotService->disConnectClient($client);
				break;
		}
		
		// TODO: Verify the sender of the message
		// https://dev.twitch.tv/docs/eventsub/handling-webhook-events/#verifying-the-event-message


		return new Response($request->getBaseUrl());
	}
}