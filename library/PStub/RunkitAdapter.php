<?php
/**
 * PStub adapter for runkit.
 *
 * Warning: use with caution, this class is just adapted interface,
 * not verifier, that actions are allowed.
 *
 * @author lex
 *
 */
class PStub_RunkitAdapter {

    /**
     * Suffix, used to backup entities, when they are modified
     *
     */
    const BACKUP_SUFFIX = '__pstub';

    /**
     * Suffix, used to create temp entities
     *
     */
    const TEMP_SUFFIX = '__tmp';

    /**
     * Name of the default php's constructor
     *
     */
    const CONSTRUCTOR = '__construct';

    /**
     * Singleton
     *
     * @var PStub_RunkitAdapter
     */
    private static $instance = null;

    /**
     * Get instance of the singleton
     *
     * @return PStub_RunkitAdapter
     */
    public static function getInstance() {
        if (null == self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Private constructor to enforce use of singleton
     *
     */
    private function __construct() {}

    /**
     * Redefine class method
     *
     * @param string $class - name of the class to redefine method in
     * @param string $method - method to redefine
     * @param string $args - comma-seperated list of arguments
     * @param string $body -  body of method
     * @param int $access - any of RUNKIT_ACC_* constants
     */
    public function redefineMethod(
        $class, $method, $args, $body, $access = RUNKIT_ACC_PUBLIC
    ) {
        $this->loadClass($class);
        $this->checkMethodDefined($class, $method);

        $backupMethod = $this->getBackupName($method);
        try {
            $this->checkMethodNotDefined($class, $backupMethod);
        } catch (PStub_RunkitAdapter_Exception $e) {
            throw new PStub_RunkitAdapter_Exception(
                'Method ' . $class . '::' . $method
                . ' can not be redefined more than once'
            );
        }

        runkit_method_rename($class, $method, $backupMethod);
        runkit_method_add($class, $method, $args, $body, $access);
    }

    /**
     * Redefine constructor of the class.
     * Note: loading (including) any of classes, extending this one will fail
     * with fatal error, so pre-loading is needed.
     *
     * @param string $class - name of the class to redefine constructor in
     * @param string $args - comma-seperated list of arguments
     * @param string $body -  body of method
     * @param int $access - any of RUNKIT_ACC_* constants
     */
    public function redefineConstructor(
        $class, $args, $code, $access = RUNKIT_ACC_PUBLIC
    ) {
        $this->loadClass($class);

        $backupMethod = $this->getBackupName(self::CONSTRUCTOR);
        try {
            $this->checkMethodNotDefined($class, $backupMethod);
        } catch (PStub_RunkitAdapter_Exception $e) {
            throw new PStub_RunkitAdapter_Exception(
                'Constructor of the class ' . $class
                . ' can not be redefined more than once'
            );
        }
        $tempMethod = $this->getTempName(self::CONSTRUCTOR);

        // this is a workaround for runkit bugs, just redefing won't work
        runkit_method_rename($class, self::CONSTRUCTOR, $backupMethod);
        runkit_method_add($class, $tempMethod, $args, $code, $access);
        runkit_method_rename($class, $tempMethod, self::CONSTRUCTOR);
    }

    /**
     * Redefine function in user space
     *
     * @param string $function
     * @param array $args
     * @param string $body
     */
    public function redefineFunction($function, $args, $body) {
        $this->checkFunctionExists($function);

        $backupFunction = $this->getBackupName($function);
        try {
            $this->checkFunctionDoesNotExist($backupFunction);
        } catch (PStub_RunkitAdapter_Exception $e) {
            throw new PStub_RunkitAdapter_Exception(
                'Function ' . $function . ' can not be redefined more than once'
            );
        }

        runkit_function_rename($function, $backupFunction);
        runkit_function_add($function, $args, $body);
    }

    /**
     * Revert redefenition of the method
     *
     * @param string $class
     * @param string $method
     */
    public function revertRedefinedMethod($class, $method) {
        $this->loadClass($class);
        $this->checkMethodDefined($class, $method);

        $backupMethod = $this->getBackupName($method);
        try {
            $this->checkMethodDefined($class, $backupMethod);
        } catch (PStub_RunkitAdapter_Exception $e) {
            throw new PStub_RunkitAdapter_Exception(
                'Method ' . $class . '::' . $method . ' was never redefined'
            );
        }

        runkit_method_remove($class, $method);
        runkit_method_rename($class, $backupMethod, $method);
    }

    /**
     * Revert redefined constructor
     *
     * @param string $class
     */
    public function revertRedefinedConstructor($class) {
        $this->loadClass($class);

        $backupMethod = $this->getBackupName(self::CONSTRUCTOR);
        try {
            $this->checkMethodDefined($class, $backupMethod);
        } catch (PStub_RunkitAdapter_Exception $e) {
            throw new PStub_RunkitAdapter_Exception(
                'Constructor of the class ' . $class . ' was never redefined'
            );
        }
        $tempMethod = $this->getTempName(self::CONSTRUCTOR);

        runkit_method_rename($class, self::CONSTRUCTOR, $tempMethod);
        runkit_method_rename($class, $backupMethod, self::CONSTRUCTOR);
        runkit_method_remove($class, $tempMethod);
    }

    /**
     * Revert redefenition of the function
     *
     * @param string $function
     */
    public function revertRedefinedFunction($function) {
        $this->checkFunctionExists($function);

        $backupFunction = $this->getBackupName($function);
        try {
            $this->checkFunctionExists($backupFunction);
        } catch (PStub_RunkitAdapter_Exception $e) {
            throw new PStub_RunkitAdapter_Exception(
                'Function ' . $function . ' was never redefined'
            );
        }

        runkit_function_remove($function);
        runkit_function_rename($backupFunction, $function);
    }

    /**
     * Load class from source file, using autoloader
     *
     * @param string $class
     */
    private function loadClass($class) {
        if (!class_exists($class)) {
            throw new PStub_RunkitAdapter_Exception(
                'Class ' . $class . ' not found'
            );
        }
    }

    /**
     * Check that given class has given method defined
     *
     * @param string $class
     * @param string $method
     */
    private function checkMethodDefined($class, $method) {
        if (!method_exists($class, $method)) {
            throw new PStub_RunkitAdapter_Exception(
                'Class ' . $class
                . ' has no method ' . $method . ' defined'
            );
        }
    }

    /**
     * Check that given class has given method not defined
     *
     * @param string $class
     * @param string $method
     */
    private function checkMethodNotDefined($class, $method) {
        if (method_exists($class, $method)) {
            throw new PStub_RunkitAdapter_Exception(
                'Class ' . $class
                . ' has method ' . $method . ' already defined'
            );
        }
    }

    /**
     * Check that function exists
     *
     * @param string $function
     */
    private function checkFunctionExists($function) {
        if (!function_exists($function)) {
            throw new PStub_RunkitAdapter_Exception(
                'Function ' . $function . ' doesn\'t exist'
            );
        }
    }

    /**
     * Check that does not exist
     *
     * @param string $function
     */
    private function checkFunctionDoesNotExist($function) {
        if (function_exists($function)) {
            throw new PStub_RunkitAdapter_Exception(
                'Function ' . $function . ' already exists'
            );
        }
    }

    /**
     * Get name for backup method
     *
     * @param string $method
     * @return string
     */
    private function getBackupName($method) {
        return $method . self::BACKUP_SUFFIX;
    }

    /**
     * Get name for temporary usage
     *
     * @param string $method
     * @return string
     */
    private function getTempName($method) {
        return $method . self::TEMP_SUFFIX;
    }

}