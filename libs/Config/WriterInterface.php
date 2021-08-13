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

use Octris\Config\Exception\CannotWriteFileException;

/**
 * Interface for implementing config writers.
 *
 * @copyright   copyright (c) 2018-present by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
interface WriterInterface
{
    /**
     * Save data to file.
     *
     * @param   string      $filename
     * @param   array       $data
     * @throws  CannotWriteFileException
     */
    public function saveFile(string $filename, array $data): void;

    /**
     * Save data to string.
     *
     * @param   array       $data
     * @return  string
     */
    public function saveString(array $data): string;
}
