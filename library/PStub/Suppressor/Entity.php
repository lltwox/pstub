<?php
/**
 * Class representing an entity, that will be supressed.
 * It can be constructor of a class or some other method.
 *
 * @author lex
 *
 */
class PStub_Suppressor_Entity {

    /**
     * PHP's constructor's default name
     *
     */
    const CONSTRUCTOR = '__construct';

    /**
     * Method to suppress
     *
     * @var string
     */
    private $methodname = null;

    /**
     * Flag, showing if constructor is needed to be suppressed
     *
     * @var boolean
     */
    private $isConstructor = false;

    /**
     * @todo
     *
     * @return PStub_Suppressor
     */
    public function constructor() {
        return $this->method(self::CONSTRUCTOR);
    }

    /**
     * @todo
     *
     * @param string $methodname
     * @return PStub_Suppressor
     */
    public function method($methodname) {
        if ($methodname == self::CONSTRUCTOR) {
            $this->isConstructor = true;
        }
        $this->methodname = $methodname;

        return PStub_Suppressor::create($this);
    }

    /**
     * Get method name to suppress
     *
     * @return string
     */
    public function getMethod() {
        return $this->methodname;
    }

    /**
     * Check if constructor is selected to be suppressed
     *
     * @return boolean
     */
    public function isConstructor() {
        return $this->isConstructor;
    }

}