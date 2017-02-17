<?php
namespace MyApp;

use Exception;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use SplObjectStorage;

/**
 * Class Chat
 *
 * @package MyApp
 */
class Chat implements MessageComponentInterface
{
	const SYSTEM = 'system';

    /**
     * @var SplObjectStorage
     */
    protected $clients;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->clients = new SplObjectStorage;
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function onOpen(ConnectionInterface $conn)
    {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);

		foreach ($this->clients as $client) {
			if ($conn !== $client) {
				$client->send(json_encode(
					[
						'user'    => static::SYSTEM,
						'message' => "New connection! ({$conn->resourceId})"
					]
				));
			}
		}

        echo "New connection! ({$conn->resourceId})" . PHP_EOL;
    }

    /**
     * @param ConnectionInterface $from
     * @param string              $msg
     */
    public function onMessage(ConnectionInterface $from, $msg)
    {
        $numRecv = count($this->clients) - 1;

        echo sprintf(
            'Connection %d sending message "%s" to %d other connection %s' . "\n",
            $from->resourceId,
            $msg,
            $numRecv,
            $numRecv == 1 ? '' : 's'
        );

        foreach ($this->clients as $client) {
            if ($from !== $client) {
                // The sender is not the receiver, send to each client connected
                $client->send($msg);
            }
        }
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function onClose(ConnectionInterface $conn)
    {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

		foreach ($this->clients as $client) {
			if ($conn !== $client) {
				// The sender is not the receiver, send to each client connected
				$client->send(json_encode(
					[
						'user'    => static::SYSTEM,
						'message' => "Connection {$conn->resourceId} has disconnected"
					]
				));
			}
		}

        echo "Connection {$conn->resourceId} has disconnected" . PHP_EOL;
    }

    /**
     * @param ConnectionInterface $conn
     * @param Exception           $e
     */
    public function onError(ConnectionInterface $conn, Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";

		$conn->send(json_encode(
			[
				'user'    => static::SYSTEM,
				'message' => $e->getMessage()
			]
		));

        $conn->close();
    }
}
