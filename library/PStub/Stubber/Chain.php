<?php
/**
 * Stubber implementation, allowing to stub a chained method calls
 *
 * @author lex
 *
 */
class PStub_Stubber_Chain extends PStub_Stubber {

    /**
     * Make entity return link to self ($this).
     *
     * @return PStub_Stubber
     */
    public function returnSelf() {
        throw new PStub_Stubber_Exception(
            '$this is not available for chain stubbing'
        );
    }

    /**
     * Make entity return pre-defined value
     *
     * @param mixed $value
     * @return PStub_Stubber
     */
    public function returnValue($value) {
        $this->stub(function() use($value) { return $value; });
        return $this;
    }

    /**
     * Make entity return callback function.
     * Callback function upon invoking will receive same list of parameters
     * as original one would.
     *
     * @param Closure $callback
     * @return PStub_Stubber
     */
    public function returnCallback(Closure $callback) {
        $this->stub($callback);
        return $this;
    }

    /**
     * Stub chain of methods
     *
     * @param string $code
     */
    protected function stub($code) {
        $this->checkEntity();
        if (!$code instanceof Closure) {
            throw new PStub_Stubber_Exception(
                'Invalid use of chain stubber'
            );
        }
        $callback = $code;

        $chain = $this->createChain($callback);
        $index = PStub_Stubber_Container::addValue($chain);
        $this->usedIndexes['value'][] = $index;
        $stubCode = str_replace('%index%', $index, self::$templates['value']);

        $method = $this->getReflectionMethodObject();
        $method->setBody($stubCode);
    }

    /**
     * Check that entity is correct
     *
     */
    private function checkEntity() {
        if (null == $this->entityClass()) {
            throw new PStub_Stubber_Exception(
                'To stub chain of methods, entity class object is needed'
            );
        }
    }

    /**
     * Revert chain
     *
     */
    protected function revertStubs() {
        $method = $this->getReflectionMethodObject();
        $method->revertBody();
        $this->cleanContainer();
    }

    /**
     * Create chain object to be returned by first method in chain
     *
     * @param Closure $callback
     */
    private function createChain($callback) {
        $methodsList = $this->entity()->getData();
        array_shift($methodsList);

        return PStub_Chain::create($methodsList, $callback);
    }

    /**
     * Get reflection method object from entity
     *
     * @return ReflectionMethod
     */
    private function getReflectionMethodObject() {
        $methodsList = $this->entity()->getData();
        $methodname = array_shift($methodsList);
        $classname = $this->entityClass()->getName();

        $reflectionMethod = null;
        try {
            $reflectionMethod = new PStub_Reflection_Method(
                $classname, $methodname
            );
        } catch (ReflectionException $e) {
            throw new PStub_Stubber_Exception(
                'Class ' . $classname
                . ' has no method ' . $methodname . 'defined'
            );
        }

        return $reflectionMethod;
    }

}