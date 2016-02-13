<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\MessageProfile\Storage\Normalizer;

use Tolerance\MessageProfile\HttpRequest\HttpMessageProfile;
use Tolerance\MessageProfile\MessageProfile;

final class SimpleProfileNormalizer implements ProfileNormalizer
{
    /**
     * {@inheritdoc}
     */
    public function normalize(MessageProfile $profile)
    {
        $normalized = [
            'identifier' => (string) $profile->getIdentifier(),
            'context' => $profile->getContext(),
        ];

        if ($profile instanceof HttpMessageProfile) {
            $normalized['type'] = 'http';
            $normalized['method'] = $profile->getMethod();
            $normalized['path'] = $profile->getPath();
        }

        return $normalized;
    }
}
