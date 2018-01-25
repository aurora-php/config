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
     * Modified data.
     * 
     * @type    array
     */
    protected $overlay = [];

    /**
     * Constructor.
     *
     * @param   array                           $files      One or multiple files to load and merge.
     * @param   \Octris\Config\FormatInterface  $format     Format encoder/decoder class.
     */
    public function __construct(array $files, \Octris\Config\FormatInterface $format)
    {
        parent::__construct([]);

        $this->format = $format;
        
        foreach ($this->files as $file) {
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
     * @param   bool                    $create_dir         Whether to create directory if it does not exist.
     * @return  bool                                        Returns TRUE on success, otherwise FALSE.
     */
    public function save($file, $create_dir = false)
    {
        $path = dirname($file);

        if (!is_dir($path)) {
            if ($create_dir) {
                mkdir($path, 0755, true);
            } else {
                return false;
            }
        }

        return file_put_contents($file, $this->format->encodeData($this->data));
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
