<?php

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
 * @copyright   (c) 2010-2018 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Config extends \Octris\Config\Collection
{
    /**
     * Instance of format encoder/decoder class.
     *
     * @type    \Octris\Config\FormatInterface
     */
    protected $format;

    /**
     * Constructor. It perfectly fine to load no configuration file by providing an empty array
     * as first argument. Multiple files can be specified as array, single file can specified as
     * a string, too.
     *
     * @param   array|strin                     $files      Optional files to load and merge.
     * @param   \Octris\Config\FormatInterface  $format     Format encoder/decoder class.
     */
    public function __construct($files, \Octris\Config\FormatInterface $format)
    {
        parent::__construct();

        $this->format = $format;
        
        foreach ((array)$files as $file) {
            $this->load($file);
        }
    }

    /**
     * Normalize path by expanding ~ prefixes.
     *
     * @param   string                          $path       Path to normalize.
     * @return  string|bool                                 Normalized path or false if path couldn't be normalized
     */
    public static function normalizePath($path)
    {
        if (preg_match('/^~([a-z][-a-z0-9]*|)(\/.+)$/i', $path, $match)) {
            if ($match[1] === '') {
                $info = posix_getpwnam($name); //['dir'];
            } elseif (($home = getenv('HOME')) === '') {
                $info = posix_getpwuid(posix_getuid()); // ['dir'];
            }

            if ($info && isset($info['dir']) && $info['dir'] !== '' && is_dir($info['dir'])) {
                $path = $info['dir'] . $match[2];
            } else {
                $path = false;
            }
        }
        

        return $path;
    }

    /**
     * Filter configuration for prefix.
     *
     * @param   string                          $prefix     Prefix to use for filter.
     * @return  \Octris\Core\Config\Filter                  Filter iterator.
     */
    public function filter($prefix)
    {
        return new \Octris\Config\Filter($this, $prefix);
    }

    /**
     * Save configuration file to destination.
     *
     * @param   string                  $file               Destination to save configuration to.
     * @return  bool                                        Returns TRUE on success, otherwise FALSE.
     */
    public function save($file)
    {
        $intersect = function(array $data1, array $data2) use (&$intersect) {
            $data1 = array_intersect_key($data1, $data2);
            
            foreach ($data1 as $key => &$value) {
                if (is_array($value) && is_array($data2[$key])) {
                    $value = $intersect($value, $data2[$key]);
                }
            }
            
            return $data1;
        };

        if (!($file = self::normalizePath($file))) {
            throw new \Exception('Unable to write file "' . $file . '"');
        }

        if (is_readable($file)) {
            // merge modified data into existing config file
            $data = $this->format->decodeData(file_get_contents($file));
            $data = $intersect($data, $this->data);
            $data = array_replace_recursive($data, $this->overlay);
        } else {
            $data =& $this->overlay;
        }
        
        return file_put_contents($file, $this->format->encodeData($data));
    }

    /**
     * Load configuration file and merge data.
     *
     * @param   string                                     $file       Name of file to load.
     */
    public function load($file)
    {
        $file = self::normalizePath($file);
        
        if ($file !== false && is_readable($file)) {
            $data = $this->format->decodeData(file_get_contents($file));
            
            $this->data = array_replace_recursive($this->data, $data);
        }
    }
}
