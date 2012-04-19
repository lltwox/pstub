<?php
/**
 * Class, that represent an entity that can be stubbed
 *
 * @author lex
 *
 */
class PStub_Stubber_Entity {

    /**
     * Types of entities
     *
     */
    const TYPE_METHOD = 'method';
    const TYPE_CHAIN = 'chain';
    const TYPE_FUNCTION = 'function';

    /**
     * Entity data, can name of the method or function or array with names
     *
     * @var string|array
     */
    private $data = null;

    /**
     * Stub method
     *
     * @param string $name
     * @return PStub_Stubber_Entity_Class
     */
    public function method($name) {
        $this->data = $name;
        $this->type = self::TYPE_METHOD;

        return new PStub_Stubber_Entity_Class($this);
    }

    /**
     * Stub chain of methods
     * E.g.: $object->method1()->method2()->method3();
     *
     * @param array $methodList
     * @return PStub_Stubber_Entity_Class
     */
    public function chain(array $methodList) {
        $this->checkChainValid($methodList);
        $this->data = $methodList;
        $this->type = self::TYPE_CHAIN;

        return new PStub_Stubber_Entity_Class($this);
    }

    /**
     * Stub function from user space (global one).
     *
     * @param string $name
     */
    public function userFunction($name) {
        $this->data = $name;
        $this->type = self::TYPE_FUNCTION;

        return PStub_Stubber::create($this);
    }

    /**
     * Get type of the entity
     *
     * @throws PStub_Stubber_Exception
     * @return string
     */
    public function getType() {
        if (null == $this->type) {
            throw new PStub_Stubber_Exception(
                'Entity type is not set'
            );
        }
        return $this->type;
    }

    /**
     * Get entity data
     *
     * @return string|array
     */
    public function getData() {
        return $this->data;
    }

    /**
     * Check, that chain is valid
     *
     * @param array $methodList
     */
    private function checkChainValid(array $methodList) {
        if (count($methodList) <= 1) {
            throw new PStub_Stubber_Exception(
                'Chain should contain at least two methods'
            );
        }
    }

}