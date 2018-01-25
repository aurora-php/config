<?php

/*
 * This file is part of the 'octris/config' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Config\Format;

/**
 * Json format encoder/decoder requires pecl yaml extension.
 *
 * @copyright   copyright (c) 2018 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Json implements \Octris\Config\FormatInterface
{
    /**
     * Constructor.
     */
    public function __construct()
    {
    }
    
    /**
     * Yaml decoder.
     * 
     * @param   string          $data           String representation of data.
     * @return  array                           Array representation of data.
     */
    public function decodeData($data)
    {
        return json_decode($data, true)
    }
    
    /**
     * Yaml encoder
     * 
     * @param   array           $data           Array representation of data.
     * @return  string                          String representation of data.
     */
    public function encodeData(array $data)
    {
        return json_encode($data);
    }
}
