<?php

declare(strict_types=1);

/**
 * Analyser manager
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  GIT: $Id$
 * @link     http://php5.laurent-laville.org/reflect/
 */

namespace Bartlett\Reflect\Analyser;

use Bartlett\Reflect\Application\Command\ConfigValidateCommand;
use Bartlett\Reflect\Application\Command\ConfigValidateHandler;

/**
 * Analyser manager
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 3.0.0-alpha2+1
 */
class AnalyserManager
{
    protected $analysers = [];
    protected $configFilename;

    /**
     * Initializes analyser manager
     *
     * @param array  $namespaces      Other analysers location to grab
     * @param string $configFilename  Path to json configuration file
     */
    public function __construct(array $namespaces, string $configFilename)
    {
        $defaultNamespace = array(
            __NAMESPACE__ => __DIR__,
        );
        $namespaces = array_merge($defaultNamespace, $namespaces);
        $this->configFilename = $configFilename;

        foreach ($namespaces as $ns => $path) {
            if (\Phar::running(false)) {
                $iterator = new \Phar($path);
            } else {
                $iterator = new \DirectoryIterator($path);
            }

            foreach ($iterator as $file) {
                if (fnmatch('*Analyser.php', $file->getPathName())) {
                    $name = basename(str_replace('Analyser.php', '', $file->getPathName()));
                    if (strpos($name, 'Abstract') !== 0) {
                        $class    = rtrim($ns, '\\') . '\\' . $file->getBasename('.php');
                        $analyser = new $class;

                        if ($analyser instanceof AnalyserInterface) {
                            $this->addAnalyser($analyser);
                        }
                    }
                }
            }
        }
    }

    /**
     * Loads all analysers declared in the JSON config file.
     *
     * @return void
     */
    public function registerAnalysers()
    {
        $command = new ConfigValidateCommand($this->configFilename);
        $configValidateHandler = new ConfigValidateHandler();
        $var = $configValidateHandler($command);

        foreach ($var['analysers'] as $analyser) {
            if (class_exists($analyser['class'])) {
                $analyser = new $analyser['class'];

                if ($analyser instanceof AnalyserInterface) {
                    $this->addAnalyser($analyser);
                }
            }
        }
    }

    /**
     * Adds an analyser
     *
     * @param AnalyserInterface $analyser Plugin instance
     *
     * @return void
     */
    public function addAnalyser(AnalyserInterface $analyser)
    {
        $this->analysers[] = $analyser;
    }

    /**
     * Gets all currently active analyser instances
     *
     * @return array analysers
     */
    public function getAnalysers()
    {
        return $this->analysers;
    }

    /**
     * Array representation of all analysers registered.
     *
     * @return array
     */
    public function toArray()
    {
        $array = [];

        foreach ($this->analysers as $analyser) {
            $array[$analyser->getShortName()] = get_class($analyser);
        }
        return $array;
    }
}
