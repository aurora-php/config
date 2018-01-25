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
 * Implements FilterIterator for filtering configuration.
 *
 * @copyright   copyright (c) 2010-2018 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Filter extends \FilterIterator
{
    /**
     * Prefix to use as filter.
     *
     * @type    string
     */
    private $prefix = '';
    
    /**
     * Remove prefix from key.
     *
     * @type    bool
     */
    private $clean = true;
    
    /**
     * Constructor.
     *
     * @param   Iterator    $config     Config object to filter.
     * @param   string      $prefix     Prefix to filter for.
     * @param   bool        $clean      Optional remove prefix from key.
     */
    public function __construct(\Octris\Config $config, $prefix, $clean = true)
    {
        $this->prefix = rtrim($prefix, '.');
        $this->clean = $clean;

        if (isset($config[$this->prefix])) {
            $tmp = new \ArrayIterator($config[$this->prefix]);
        } else {
            $tmp = new \ArrayIterator();
        }

        parent::__construct($tmp);

        $this->rewind();
    }

    /**
     * Return key of current item.
     *
     * @return  mixed                   Key of current item.
     */
    public function key()
    {
        return (!$this->clean ? $this->prefix . '.' : '') . parent::key();
    }

    /**
     * Filter implementation.
     *
     * @return  bool        Returns TRUE, if element should be part of result.
     */
    public function accept()
    {
        return true;
    }
}
