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

namespace Octris\Config\Reader;

use \Octris\Config\AbstractReader;
use \Octris\Config\Exception;

/**
 * Yaml format reader.
 *
 * @copyright   copyright (c) 2020-present by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Yaml extends AbstractReader
{
    /**
     * {@inheritDoc}
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * {@inheritDoc}
     */
    public function loadFile(string $filename): array
    {
        if (!file_exists($filename)) {
            throw new Exception\FileNotFoundException(sprintf('File not found "%s".', $filename));
        }

        $data = yaml_parse_file($filename);

        if (!$data) {
            throw new Exception\FormatException(sprintf('Unable to parse ini file "%s".', $filename));
        }

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function loadString(string $in): array
    {
        $data = yaml_parse($in);

        if (!$data) {
            throw new Exception\FormatException(sprintf('Unable to parse ini string.'));
        }

        return $data;
    }
}
