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
use Tolerance\MessageProfile\Storage\Neo4j\Labels;
use Tolerance\MessageProfile\Storage\Neo4j\RelationshipTypes;

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
        $peerArray = $peer->getArray();
        $peerId = isset($peerArray['id']) ? $peerArray['id'] : md5(json_encode($peerArray));
        $relProps = null !== $profile->getTiming() ? [
            'start' => $profile->getTiming()->getStart()->format('Y-m-d\TH:i:s.uO'),
            'end' => $profile->getTiming()->getEnd()->format('Y-m-d\TH:i:s.uO')
        ] : [];

        $query = sprintf(
            'MATCH (p:%s {id: {peerId} }), (m:%s { identifier: {messageId} }) '.
            'MERGE (p)-[r:%s]->(m) '.
            'SET r += {relProps} ',
            Labels::PEER,
            Labels::MESSAGE,
            $relationType
        );
        $params = [
            'peerId' => $peerId,
            'messageId' => (string) $profile->getIdentifier(),
            'relProps' => $relProps
        ];
        $this->client->sendCypherQuery($query, $params);
    }

    /**
     * @param MessagePeer $peer
     */
    private function createPeer(MessagePeer $peer)
    {
        $array = $peer->getArray();
        $identifier = isset($array['id']) ? $array['id'] : md5(json_encode($array));
        $query = sprintf(
            'MERGE (p:%s {id: {identifier} }) '.
            'ON CREATE SET p += {props} '.
            'RETURN p',
            Labels::PEER);

        $this->client->sendCypherQuery($query, ['identifier' => $identifier, 'props' => $array]);
    }

    /**
     * @param MessageProfile $profile
     */
    private function createMessage(MessageProfile $profile)
    {
        $message = $this->normalizeMessage($profile);

        $query = sprintf('CREATE (m:%s) SET m += {props}', Labels::MESSAGE);
        $this->client->sendCypherQuery($query, ['props' => $message]);
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
