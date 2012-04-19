<?php
/**
 * Class, acting as a intermediate object in a chained calls.
 *
 * @author lex
 *
 */
class PStub_Chain_Link {

    /**
     * Method, that should be chained
     *
     * @var string
     */
    private $methodname = null;

    /**
     * Function to call on chained method invokation
     *
     * @var Closure
     */
    private $closure = null;

    /**
     * Constructor
     *
     * @param string $methodname - methodname, that object should have
     * @param Closure $closure
     */
    public function __construct($methodname, Closure $closure) {
        $this->methodname = $methodname;
        $this->closure = $closure;
    }

    /**
     * Magic method, allowing to call closure function instead of chained method
     *
     * @param string $name
     * @param arguments $arguments
     * @return mixed
     */
    public function __call($name, $arguments) {
        if ($name != $this->methodname) {
            throw new PStub_Chain_Exception(
                'Invalid method chained: expected '
                . $this->methodname . ' but got ' . $name
            );
        }

        return call_user_func_array($this->closure, $arguments);
    }

}