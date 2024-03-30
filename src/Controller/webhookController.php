<?php
namespace App\Controller;

// use GhostZero\Tmi\Client;
// use GhostZero\Tmi\ClientOptions;
// use GhostZero\Tmi\Events\Twitch\MessageEvent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class webhookController extends AbstractController
{
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
		die('HERE');
		// TODO: Verify the sender of the message
		// https://dev.twitch.tv/docs/eventsub/handling-webhook-events/#verifying-the-event-message


		return new Response($request->getBaseUrl());
	}
}