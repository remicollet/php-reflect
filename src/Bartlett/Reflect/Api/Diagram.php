<?php
/**
 * Diagram generator API
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  GIT: $Id$
 * @link     http://php5.laurent-laville.org/reflect/
 */

namespace Bartlett\Reflect\Api;

/**
 * Diagram generator API
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 3.0.0-beta3
 */
class Diagram extends BaseApi
{
    /**
     * Generates diagram about namespaces in a data source.
     *
     * @param string $argument Name of the namespace to inspect.
     * @param string $source   Path to the data source or its alias.
     * @param mixed  $alias    If set, the source refers to its alias.
     * @param string $engine   Graphical syntax.
     *
     * @return mixed
     */
    public function package($argument, $source, $alias = null, $engine = 'plantuml')
    {
        return $this->request('diagram/package', 'POST', array($argument, $source, $alias, $engine));
    }

    /**
     * Generates diagram about a user class present in a data source.
     *
     * @param string $argument Name of the class to inspect.
     * @param string $source   Path to the data source or its alias.
     * @param mixed  $alias    If set, the source refers to its alias.
     * @param string $engine   Graphical syntax.
     *
     * @return mixed
     * @alias  class
     */
    public function class_($argument, $source, $alias = null, $engine = 'plantuml')
    {
        return $this->request('diagram/class', 'POST', array($argument, $source, $alias, $engine));
    }
}
