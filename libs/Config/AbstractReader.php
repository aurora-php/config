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

namespace Octris\Config;

use Octris\Config\Exception\FileNotFoundException;
use Octris\Config\Exception\FormatException;

/**
 * Abstract class for implementing config readers.
 *
 * @copyright   copyright (c) 2018-present by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
abstract class AbstractReader
{
    /**
     * Constructor.
     */
    public function __construct()
    {
    }

    /**
     * Load configuration from file, if file exists.
     *
     * @param   string          $filename       Name of file to load.
     * @return  array                           Array representation of data.
     * @throws  FormatException
     */
    public function loadFileIfExists(string $filename): array
    {
        if (file_exists($filename) && is_readable($filename)) {
            $data = $this->loadFile($filename);
        } else {
            $data = [];
        }

        return $data;
    }

    /**
     * Load configuration from file.
     *
     * @param   string          $filename       Name of file to load.
     * @return  array                           Array representation of data.
     * @throws  FileNotFoundException
     * @throws  FormatException
     */
    abstract public function loadFile(string $filename): array;

    /**
     * Load configuration from string.
     * 
     * @param   string          $in             String representation of data.
     * @return  array                           Array representation of data.
     * @throws  FormatException
     */
    abstract public function loadString(string $in): array;
}
