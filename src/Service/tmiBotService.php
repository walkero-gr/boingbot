<?php
namespace App\Service;

use Exception;
use GhostZero\Tmi\Client;
use GhostZero\Tmi\ClientOptions;
use GhostZero\Tmi\Events\Irc\JoinEvent;
use GhostZero\Tmi\Events\Irc\PartEvent;
use GhostZero\Tmi\Events\Twitch\MessageEvent;
use Psr\Log\LoggerInterface;

class tmiBotService
{
	public function __construct(private LoggerInterface $logger) {}

	public function setupClient(
		string $uname,
		string $token,
		string $channels
	): Client
	{
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

	public function disConnectClient(Client $client): void
	{
		if ($client->isConnected()) {
			var_dump('Client is connected');
		} else {
			var_dump('Client is NOT connected');
		}
		// try {
		// 	$client->close();
		// } catch (Exception $e) {
		// 	$this->logger->error($e->getMessage());
		// 	throw $e;
		// }
	}

	public function setupEvents(Client $client, string $uname): void
	{
		$client->on(MessageEvent::class, function (MessageEvent $e) use ($client) {
			// if ($e->self) return;
		
			if (strtolower($e->message) === '!hello') {
				$client->say($e->channel->getName(), "@{$e->user}, heya!");
			}

			if (in_array(strtolower($e->user), ['walkerogr'])) {
				if (strtolower($e->message) === '!killbot') {
					$client->say($e->channel->getName(), "👋 I am out of here...");
					$client->getLoop()->addTimer(3, fn ()  => $client->close());
				}
			}
		});

		$client->on(JoinEvent::class, function (JoinEvent $e) use ($client, $uname) {
			if (strtolower($e->user) === $uname) {
				$client->say($e->channel->getName(), "🤖 I am here to serve you...");
			}
		});

		// $client->on(PartEvent::class, function (PartEvent $e) use ($client, $uname) {
		// 	// if (strtolower($e->user) === $uname) {
		// 		$client->say($e->channel->getName(), "👋 I am out of here...");
		// 	// }
		// });
	}
}