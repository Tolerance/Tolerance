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
use Neoxygen\NeoClient\Transaction\Transaction;
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
        $transaction = $this->client->createTransaction();

        $this->createMessage($transaction, $profile);

        if (null !== ($recipient = $profile->getRecipient())) {
            $this->createPeer($transaction, $recipient);
            $this->createPeerMessageRelation($transaction, $recipient, $profile, 'RECEIVED_MESSAGE');
        }

        if (null !== ($sender = $profile->getSender())) {
            $this->createPeer($transaction, $sender);
            $this->createPeerMessageRelation($transaction, $sender, $profile, 'SENT_MESSAGE');
        }

        $transaction->commit();
    }

    /**
     * @param Transaction    $transaction
     * @param MessagePeer    $peer
     * @param MessageProfile $profile
     * @param string         $relationType
     */
    private function createPeerMessageRelation(Transaction $transaction, MessagePeer $peer, MessageProfile $profile, $relationType)
    {
        $peerArray = $peer->getArray();
        $peerId = isset($peerArray['id']) ? $peerArray['id'] : md5(json_encode($peerArray));
        $relProps = null !== $profile->getTiming() ? [
            'start' => $profile->getTiming()->getStart()->format('Y-m-d\TH:i:s.uO'),
            'end' => $profile->getTiming()->getEnd()->format('Y-m-d\TH:i:s.uO'),
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
            'relProps' => $relProps,
        ];

        $transaction->pushQuery($query, $params);
    }

    /**
     * @param Transaction $transaction
     * @param MessagePeer $peer
     */
    private function createPeer(Transaction $transaction, MessagePeer $peer)
    {
        $array = $peer->getArray();
        $identifier = isset($array['id']) ? $array['id'] : md5(json_encode($array));
        $query = sprintf(
            'MERGE (p:%s {id: {identifier} }) '.
            'ON CREATE SET p += {props} ',
            Labels::PEER);

        $transaction->pushQuery($query, ['identifier' => $identifier, 'props' => $array]);
    }

    /**
     * @param Transaction    $transaction
     * @param MessageProfile $profile
     *
     * @throws \Neoxygen\NeoClient\Exception\Neo4jException
     */
    private function createMessage(Transaction $transaction, MessageProfile $profile)
    {
        $message = $this->normalizeMessage($profile);

        $transaction->pushQuery(sprintf(
            'MERGE (m:%s {identifier: {identifier} }) '.
            'SET m += {props}',
            Labels::MESSAGE
        ), [
            'identifier' => (string) $profile->getIdentifier(),
            'props' => $message,
        ]);

        if (null !== ($parentIdentifier = $profile->getParentIdentifier()) && $parentIdentifier != $profile->getIdentifier()) {
            $transaction->pushQuery(sprintf(
                'MERGE (p:%s {identifier: {parentId} })',
                Labels::MESSAGE
            ), [
                'parentId' => (string) $parentIdentifier,
            ]);

            $transaction->pushQuery(sprintf(
                'MATCH (p:%s { identifier: {parentId} }), (m:%s { identifier: {messageId} }) '.
                'MERGE (m)-[r:%s]->(p) ',
                Labels::MESSAGE,
                Labels::MESSAGE,
                RelationshipTypes::PARENT_MESSAGE
            ), [
                'parentId' => (string) $parentIdentifier,
                'messageId' => (string) $profile->getIdentifier(),
            ]);
        }
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
}
