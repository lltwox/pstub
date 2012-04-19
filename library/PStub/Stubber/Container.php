<?php
/**
 * Class, for storing data, that can be accessed from dynamically created
 * methods and functions by unique string id.
 *
 * @author lex
 *
 */
class PStub_Stubber_Container {

    /**
     * List of values used
     *
     * @var array
     */
    private static $values = array();

    /**
     * List of methods used
     *
     * @var array
     */
    private static $methods = array();

    /**
     * Add value to storage
     *
     * @param mixed $value
     * @return integer - index at which value was added to storege
     */
    public static function addValue($value) {
        self::$values[] = $value;
        return count(self::$values) - 1;
    }

    /**
     * Remove value from storage by index
     *
     * @param integer $index
     */
    public static function removeValue($index) {
        if (isset(self::$values[$index])) {
            self::$values[$index] = null;
        }
    }

    /**
     * Get value by index
     *
     * @param integer $index
     * @throws PStub_Stubber_Exception
     * @return mixed
     */
    public static function getValue($index) {
        if (!isset(self::$values[$index])) {
            throw new PStub_Stubber_Exception(
                'No value in caontainer for index ' . $index
            );
        }

        return self::$values[$index];
    }

    /**
     * Add method to storage
     *
     * @param Closure $method
     * @return integer - index, at which method was added to storage
     */
    public static function addMethod(Closure $method) {
        self::$methods[] = $method;
        return count(self::$methods) - 1;
    }

    /**
     * Remove method from storage by index
     *
     * @param integer $index
     */
    public static function removeMethod($index) {
        if (isset(self::$methods[$index])) {
            self::$methods[$index] = null;
        }
    }

    /**
     * Invoke method by index
     *
     * @param integer $index
     * @param array $args - list of args to pass to method
     * @return mixed -  result of methods execution
     */
    public static function invokeMethod($index, array $args) {
        if (!isset(self::$methods[$index])) {
            throw new PStub_Stubber_Exception(
                'No method in container for index ' . $index
            );
        }

        return call_user_func_array(self::$methods[$index], $args);
    }

}