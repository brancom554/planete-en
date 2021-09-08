<?php

require __DIR__ . "/../../vendor/autoload.php";

/**
 * A modified version of PHPUnit's TestCase to rid ourselves of deprecation
 * warnings since we're using two different versions of PHPUnit in this branch
 * (PHPUnit 4 and 5).
 */
class BC_PHPUnit_Framework_TestCase extends \PHPUnit_Framework_TestCase {
    // A BC hack to get handle the deprecation of this method in PHPUnit
    public function bc_getMock($originalClassName, $methods = array(), array $arguments = array(), $mockClassName = '', $callOriginalConstructor = true, $callOriginalClone = true, $callAutoload = true, $cloneArguments = false, $callOriginalMethods = false, $proxyTarget = null)
    {
        if (method_exists($this, "getMockBuilder")) {
            return $this
                ->getMockBuilder($originalClassName)
                ->setMethods($methods)
                ->getMock()
            ;
        }

        return parent::getMock($originalClassName, $methods, $arguments, $mockClassName, $callOriginalConstructor, $callOriginalClone, $callAutoload, $cloneArguments, $callOriginalMethods, $proxyTarget);
    }
}

// The only deprecation warnings we need to ignore/handle are in PHP 7.4 so far
if (PHP_VERSION_ID >= 70400) {
    function customErrorHandler($errno, $errstr, $errfile, $errline) {
        // We know about this deprecation warning exists and it's already been
        // fixed in the 2.x branch. For BC reasons in the 1.x branch, we'll
        // ignore this warning to let tests pass.
        if ($errno === E_DEPRECATED) {
            if ($errstr === "Function ReflectionType::__toString() is deprecated") {
                return true;
            }
        }

        // Any other error should be left up to PHPUnit to handle
        return \PHPUnit_Util_ErrorHandler::handleError($errno, $errstr, $errfile, $errline);
    }

    set_error_handler("customErrorHandler");
}
