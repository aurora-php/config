<?php

declare(strict_types=1);

/*
 * This file is part of the 'octris/config' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris;

/**
 * Application configuration library for the OCTRiS framework supporting 
 * multiple formats.
 *
 * @copyright   (c) 2010-present by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Config extends \Octris\PropertyCollection
{
    /**
     * {@inheritDoc}
     */
    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }

    /**
     * Merge configuration data.
     *
     * @param   array   $data
     */
    public function merge(array $data)
    {
        $this->data = array_replace_recursive($this->data, $data);
    }
}
