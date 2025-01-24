<?php

namespace neverstale\api\exceptions;

/**
 * Neverstale Unknown Property Exception
 *
 * @author Neverstale
 * @package neverstale/api
 * @since 1.0.0
 * @see http://docs.neverstale.io/api/
 */
class UnknownPropertyException extends \Exception
{
    public function getName(): string
    {
        return 'Unknown Property';
    }
}
