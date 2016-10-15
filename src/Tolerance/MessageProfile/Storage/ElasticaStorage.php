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

use Elastica\Document;
use Elastica\Type;
use Ramsey\Uuid\Uuid;
use Tolerance\MessageProfile\MessageProfile;
use Tolerance\MessageProfile\Storage\Normalizer\ProfileNormalizer;

class ElasticaStorage implements ProfileStorage
{
    /**
     * @var ProfileNormalizer
     */
    private $normalizer;

    /**
     * @var Type
     */
    private $type;

    /**
     * @param ProfileNormalizer $normalizer
     * @param Type              $type
     */
    public function __construct(ProfileNormalizer $normalizer, Type $type)
    {
        $this->type = $type;
        $this->normalizer = $normalizer;
    }

    /**
     * {@inheritdoc}
     */
    public function store(MessageProfile $profile)
    {
        $documentUuid = Uuid::uuid4()->toString();
        $normalized = $this->normalizer->normalize($profile);
        $normalized['@timestamp'] = (int) ((float) $profile->getTiming()->getStart()->format('U.u')) * 1000;

        $this->type->addDocument(new Document($documentUuid, $normalized));
    }
}
