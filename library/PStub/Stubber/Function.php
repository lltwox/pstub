<?php
/**
 * Stubber implementation allowing to stub global functions
 *
 * @author lex
 *
 */
class PStub_Stubber_Function extends PStub_Stubber {

    /**
     * Make entity return link to self ($this).
     *
     * @return PStub_Stubber
     */
    public function returnSelf() {
        throw new PStub_Stubber_Exception(
            '$this is not defined in function\' scope'
        );
    }

    /**
     * Stub method of the class with provided code
     *
     * @param string $code
     */
    protected function stub($code) {
        $function = $this->getReflectionFunctionObject();
        $function->setBody($code);
    }

    /**
     * Revert all stubs done
     *
     */
    protected function revertStubs() {
        $function = $this->getReflectionFunctionObject();
        $function->revertBody();
        $this->cleanContainer();
    }

    /**
     * Get reflection method object from entity
     *
     * @return ReflectionMethod
     */
    private function getReflectionFunctionObject() {
        $functionname = $this->entity()->getData();

        $reflectionFunction = null;
        try {
            $reflectionFunction = new PStub_Reflection_Function(
                $functionname
            );
        } catch (ReflectionException $e) {
            throw new PStub_Stubber_Exception(
                'Function ' . $functionname . 'is not found'
            );
        }

        return $reflectionFunction;
    }

}