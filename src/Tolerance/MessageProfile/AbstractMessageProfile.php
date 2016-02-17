<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\MessageProfile;

/**
 * This class is basically a workaround to the fact that an interface can't hold
 * a discriminator map.
 */
abstract class AbstractMessageProfile implements MessageProfile
{
}
