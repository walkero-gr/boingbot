<?php
namespace App\Service;

use Exception;
use GhostZero\Tmi\Client;
use GhostZero\Tmi\ClientOptions;
use GhostZero\Tmi\Events\Irc\JoinEvent;
use GhostZero\Tmi\Events\Twitch\MessageEvent;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Log\Logger;
use Symfony\Component\Routing\Attribute\Route;

class tmiBotService
{
	public function __construct(private LoggerInterface $logger) {}

	public function setupClient(
		string $uname,
		string $token,
		string $channels
	): Client
	{
		die($uname);
		return new Client(new ClientOptions([
			'options' => ['debug' => true],
			'connection' => [
				'secure' => true,
				'reconnect' => true,
				'rejoin' => true,
			],
			'identity' => [
				'username' => $uname,
				'password' => $token,
			],
			'channels' => [$channels]
		]));
	}

	public function connectClient(Client $client): void
	{
		try {
			$client->connect();
		} catch (Exception $e) {
			$this->logger->error($e->getMessage());
			throw $e;
		}
	}

	public function setupEvents(Client $client, string $uname): void
	{
		$client->on(MessageEvent::class, function (MessageEvent $e) use ($client) {
			// if ($e->self) return;
		
			if (strtolower($e->message) === '!hello') {
				$client->say($e->channel->getName(), "@{$e->user}, heya!");
			}
		});

		$client->on(JoinEvent::class, function (JoinEvent $e) use ($client, $uname) {
			if (strtolower($e->user) === $uname) {
				$client->say($e->channel->getName(), "🤖 I am here to serve you...");
			}
		});
	}
}