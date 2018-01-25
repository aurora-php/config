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
        parent::__construct([]);

        $this->format = $format;
        
        foreach ((array)$files as $file) {
            if (preg_match('/^~([a-z][-a-z0-9]*|)(\/.+)$/i', $file, $match)) {
                $file = $this->getHome($match[1]) . $match[2];
            }
            
            if (file_exists($file)) {
                $this->load($file);
            }
        }
    }

    /**
     * Determine HOME directory.
     *
     * @param   string                          $name       Optional name of user to return home directory of.
     * @return  string                                      Home directory.
     */
    protected function getHome($name = '')
    {
        if ($name === '') {
            $home = posix_getpwnam($name)['dir'];
        } elseif (($home = getenv('HOME')) === '') {
            $home = posix_getpwuid(posix_getuid())['dir'];
        }

        return $home;
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
        if (is_readable($file)) {
            $data = $this->format->decodeData(file_get_contents($file));
            
            $this->data = array_replace_recursive($this->data, $data);
        }
    }
}
