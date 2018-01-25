<?php

/*
 * This file is part of the 'octris/config' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Config;

/**
 * Interface for implementing format backends.
 *
 * @copyright   copyright (c) 2018 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
interface FormatInterface
{
    /**
     * Decode data and return array representation.
     * 
     * @param   string          $data           String representation of data.
     * @return  array                           Array representation of data.
     */
    public function decodeData($data);
    
    /**
     * Encode data and return string representation.
     * 
     * @param   array           $data           Array representation of data.
     * @return  string                          String representation of data.
     */
    public function encodeData(array $data);
}
