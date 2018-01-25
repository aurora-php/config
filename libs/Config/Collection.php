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
 * Collection type. Implements special access on array objects.
 *
 * @copyright   copyright (c) 2010-2018 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Collection implements \IteratorAggregate, \ArrayAccess, \Serializable, \JsonSerializable, \Countable
{
    /**
     * Data of collection.
     *
     * @type    array
     */
    protected $data = [];

    /**
     * Added data.
     * 
     * @type    array
     */
    protected $overlay = [];

    /**
     * Constructor.
     *
     * @param   mixed       $data               Optional data to initialize collection with.
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * Return stored data if var_dump is used with collection.
     *
     * @return  array                           Stored data.
     */
    public function __debugInfo()
    {
        return $this->data;
    }

    /** Iterator **/
    
    /**
     * Return an iterator of stored configuration.
     * 
     * @return  \ArrayIterator          Instance of iterator.
     */
    public function getIterator() {
        return new \ArrayIterator($this->data);
    }

    /** ArrayAccess **/

    /**
     * Get value from configuration. Allows access by dot-notation.
     *
     * @param   string      $offs       Offset to get value from.
     * @return  mixed                   Value stored at offset, arrays are returned as Subcollection.
     */
    public function offsetGet($offs)
    {
        if (strpos($offs, '.') !== false) {
            $parts = explode('.', preg_replace('/\.+/', '.', trim($offs, '.')));
            $ret =& $this->data;

            for ($i = 0, $cnt = count($parts); $i < $cnt; ++$i) {
                if (!array_key_exists($parts[$i], $ret)) {
                    trigger_error('Undefined index "' . $parts[$i] . '" in "' . $offs . '".');

                    return null;
                } else {
                    $ret =& $ret[$parts[$i]];
                }
            }
        } else {
            if (!array_key_exists($offs, $this->data)) {
                trigger_error('Undefined index "' . $offs . '".');

                return null;
            }

            $ret =& $this->data[$offs];
        }

        return $ret;
    }

    /**
     * Set value in collection at specified offset. Allows access by dot-notation.
     *
     * @param   string      $offs       Offset to set value at.
     * @param   mixed       $value      Value to set at offset.
     */
    public function offsetSet($offs, $value)
    {
        if (is_null($offs)) {
            // $...[] =
            $this->data[] = $value;
            $this->overlay[] = $value;
        } elseif (strpos($offs, '.') !== false) {
            $parts = explode('.', preg_replace('/\.+/', '.', trim($offs, '.')));
            $data =& $this->data;
            $overlay =& $this->overlay;

            for ($i = 0, $cnt = count($parts); $i < $cnt; ++$i) {
                if (!array_key_exists($parts[$i], $data)) {
                    $data[$parts[$i]] = [];
                }
                if (!array_key_exists($parts[$i], $overlay)) {
                    $overlay[$parts[$i]] = [];
                }

                $data =& $data[$parts[$i]];
                $overlay =& $overlay[$parts[$i]];
            }

            $data = $value;
            $overlay = $value;
        } else {
            $this->data[$offs] = $value;
            $this->overlay[$pffs] = $value;
        }
    }

    /**
     * Check whether the offset exists in collection. Allows access by dot-notation.
     *
     * @param   string      $offs       Offset to check.
     * @return  bool                    Returns true, if offset exists.
     */
    public function offsetExists($offs)
    {
        if (strpos($offs, '.') !== false) {
            $parts = explode('.', preg_replace('/\.+/', '.', trim($offs, '.')));
            $ret =& $this->data;

            for ($i = 0, $cnt = count($parts); $i < $cnt; ++$i) {
                if (!($return = array_key_exists($parts[$i], $ret))) {
                    break;
                }

                $ret =& $ret[$parts[$i]];
            }
        } else {
            $return = isset($this->data[$offs]);
        }

        return $return;
    }

    /**
     * Unset data in collection at specified offset. Allows access by dot-notation.
     *
     * @param   string      $offs       Offset to unset.
     */
    public function offsetUnset($offs)
    {
        if (strpos($offs, '.') !== false) {
            $parts = explode('.', preg_replace('/\.+/', '.', trim($offs, '.')));
            $data =& $this->data;
            $overlay =& $this->overlay;
            $skip_overlay = false;

            for ($i = 0, $cnt = count($parts); $i < $cnt; ++$i) {
                if (!(array_key_exists($parts[$i], $data))) {
                    break;
                }

                if ($i == $cnt - 1) {
                    unset($data[$parts[$i]]);
                    
                    if (!$skip_overlay) {
                    unset($overlay[$parts[$i]]);
                    }
                } else {
                    $data =& $data[$parts[$i]];
                    
                    if (!$skip_overlay && !($skip_overlay = !isset($overlay[$parts[$i]]))) {
                        $overlay =& $overlay[$parts[$i]];
                    }
                }
            }
        } else {
            unset($this->data[$offs]);
            unset($this->overlay[$offs]);
        }
    }

    /** Serializable **/

    /**
     * Get's called when something wants to serialize the collection.
     *
     * @return  string                      Serialized content of collection.
     */
    public function serialize()
    {
        return serialize($this->data);
    }

    /**
     * Get's called when something wants to unserialize the collection.
     *
     * @param   string                      Data to unserialize as collection.
     */
    public function unserialize($data)
    {
        $this->data = unserialize($data);
    }

    /** JsonSerializable **/

    /**
     * Get's called when something wants to json-serialize the collection.
     *
     * @return  string                      Json-serialized content of collection.
     */
    public function jsonSerialize()
    {
        return json_encode($this->data);
    }

    /** Countable **/

    /**
     * Return number of items in collection.
     *
     * @return  int                         Number of items.
     */
    public function count()
    {
        return count($this->data);
    }
}
