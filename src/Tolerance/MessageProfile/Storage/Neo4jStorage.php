<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\MessageProfile\Storage;

use Neoxygen\NeoClient\Client;
use Tolerance\MessageProfile\HttpRequest\HttpMessageProfile;
use Tolerance\MessageProfile\MessageProfile;
use Tolerance\MessageProfile\Peer\MessagePeer;

class Neo4jStorage implements ProfileStorage
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function store(MessageProfile $profile)
    {
        $this->createMessage($profile);

        if (null !== ($recipient = $profile->getRecipient())) {
            $this->createPeer($recipient);
            $this->createPeerMessageRelation($recipient, $profile, 'RECEIVED_MESSAGE');
        }

        if (null !== ($sender = $profile->getSender())) {
            $this->createPeer($sender);
            $this->createPeerMessageRelation($sender, $profile, 'SENT_MESSAGE');
        }
    }

    /**
     * @param MessagePeer    $peer
     * @param MessageProfile $profile
     * @param string         $relationType
     */
    private function createPeerMessageRelation(MessagePeer $peer, MessageProfile $profile, $relationType)
    {
        $this->client->sendCypherQuery(
            sprintf(
                'MATCH (p:Peer %s), (m:Message %s)'.
                'MERGE (p)-[:%s %s]->(m)',
                $this->arrayToAttributes($peer->getArray()),
                $this->arrayToAttributes([
                    'identifier' => (string) $profile->getIdentifier(),
                ]),
                $relationType,
                null !== $profile->getTiming() ? $this->arrayToAttributes([
                    'start' => $profile->getTiming()->getStart()->format('Y-m-d\TH:i:s.uO'),
                    'end' => $profile->getTiming()->getEnd()->format('Y-m-d\TH:i:s.uO'),
                ]) : ''
            )
        );
    }

    /**
     * @param MessagePeer $peer
     */
    private function createPeer(MessagePeer $peer)
    {
        $this->client->sendCypherQuery(
            sprintf(
                'MERGE (p:Peer %s) RETURN p',
                $this->arrayToAttributes($peer->getArray())
            )
        );
    }

    /**
     * @param MessageProfile $profile
     */
    private function createMessage(MessageProfile $profile)
    {
        $message = $this->normalizeMessage($profile);

        $this->client->sendCypherQuery(
            sprintf(
                'CREATE (m:Message %s) RETURN m',
                $this->arrayToAttributes($message)
            )
        );
    }

    /**
     * @param MessageProfile $profile
     *
     * @return array
     */
    private function normalizeMessage(MessageProfile $profile)
    {
        $normalized = [
            'identifier' => (string) $profile->getIdentifier(),
            'context' => $profile->getContext(),
        ];

        if ($profile instanceof HttpMessageProfile) {
            $normalized['method'] = $profile->getMethod();
            $normalized['path'] = $profile->getPath();
        }

        return $normalized;
    }

    /**
     * @param array $array
     *
     * @return string
     */
    private function arrayToAttributes(array $array)
    {
        $array = $this->flattenArray($array);
        $pairs = array_map(function ($key, $value) {
            return sprintf('%s: \'%s\'', $key, $value);
        }, array_keys($array), $array);

        return '{'.implode(', ', $pairs).'}';
    }

    /**
     * @param array $array
     *
     * @return array
     */
    private function flattenArray(array $array)
    {
        $flatten = [];

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                foreach ($this->flattenArray($value) as $subKey => $subValue) {
                    $flatten[$key.'.'.$subKey] = $subValue;
                }
            } else {
                $flatten[$key] = $value;
            }
        }

        return $flatten;
    }
}
