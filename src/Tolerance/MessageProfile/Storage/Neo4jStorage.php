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
use Tolerance\MessageProfile\MessageProfile;
use Tolerance\MessageProfile\Peer\ArbitraryPeer;
use Tolerance\MessageProfile\Peer\MessagePeer;
use Tolerance\MessageProfile\Storage\Normalizer\ProfileNormalizer;

class Neo4jStorage implements ProfileStorage
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var ProfileNormalizer
     */
    private $profileNormalizer;

    /**
     * @param Client            $client
     * @param ProfileNormalizer $profileNormalizer
     */
    public function __construct(Client $client, ProfileNormalizer $profileNormalizer)
    {
        $this->client = $client;
        $this->profileNormalizer = $profileNormalizer;
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
        if (null === ($timing = $profile->getTiming())) {
            return;
        }

        $transaction->pushQuery(
            sprintf(
                'MATCH (p:Peer {id: {peerId} }), (m:Message { identifier: {messageId} }) '.
                'MERGE (p)-[r:%s]->(m) '.
                'SET r += {relationProperties} ',
                $relationType
            ),
            [
                'peerId' => $peer->getIdentifier(),
                'messageId' => (string) $profile->getIdentifier(),
                'relationProperties' => [
                    'start' => (int) ($this->getUnixTimestamp($timing->getStart()) * 1000),
                    'end' => (int) ($this->getUnixTimestamp($timing->getEnd()) * 1000),
                ],
            ]
        );
    }

    /**
     * @param Transaction $transaction
     * @param MessagePeer $peer
     */
    private function createPeer(Transaction $transaction, MessagePeer $peer)
    {
        $peerProperties = [
            'identifier' => $peer->getIdentifier(),
        ];

        if ($peer instanceof ArbitraryPeer) {
            $peerProperties = array_merge($peerProperties, $peer->getArray());
        }

        $transaction->pushQuery(
            'MERGE (p:Peer {id: {identifier} }) ON CREATE SET p += {peerProperties} ',
            [
                'identifier' => $peer->getIdentifier(),
                'peerProperties' => $peerProperties,
            ]
        );
    }

    /**
     * @param Transaction    $transaction
     * @param MessageProfile $profile
     *
     * @throws \Neoxygen\NeoClient\Exception\Neo4jException
     */
    private function createMessage(Transaction $transaction, MessageProfile $profile)
    {
        $message = $this->profileNormalizer->normalize($profile);

        $transaction->pushQuery(
            'MERGE (m:Message {identifier: {identifier} }) SET m += {messageProperties}',
            [
                'identifier' => (string) $profile->getIdentifier(),
                'messageProperties' => $message,
            ]
        );

        if (null !== ($parentIdentifier = $profile->getParentIdentifier()) && $parentIdentifier != $profile->getIdentifier()) {
            $transaction->pushQuery('MERGE (p:Message {identifier: {parentId} })', ['parentId' => (string) $parentIdentifier]);

            $transaction->pushQuery(
                'MATCH (p:Message { identifier: {parentId} }), (m:Message { identifier: {messageId} }) '.
                'MERGE (m)-[r:PARENT_MESSAGE]->(p) ',
                [
                    'parentId' => (string) $parentIdentifier,
                    'messageId' => (string) $profile->getIdentifier(),
                ]
            );
        }
    }

    /**
     * @param \DateTimeInterface $date
     *
     * @return float
     */
    private function getUnixTimestamp(\DateTimeInterface $date)
    {
        return (float) $date->format('U.u');
    }
}
