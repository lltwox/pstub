<?php
/**
 * Stubber implementation, allowing to stub method in class
 *
 * @author lex
 *
 */
class PStub_Stubber_Method extends PStub_Stubber {

    /**
     * Stub method of the class with provided code
     *
     * @param string $code
     */
    protected function stub($code) {
        $this->checkEntity();
        $method = $this->getReflectionMethodObject();
        $method->setBody($code);
    }

    /**
     * Revert all stubs done
     *
     */
    protected function revertStubs() {
        $this->checkEntity();
        $method = $this->getReflectionMethodObject();
        $method->revertBody();
        $this->cleanContainer();
    }

    /**
     * Check that entity is correct
     *
     */
    private function checkEntity() {
        if (null == $this->entityClass()) {
            throw new PStub_Stubber_Exception(
                'To stub method in class, entity class object is needed'
            );
        }
    }

    /**
     * Get reflection method object from entity
     *
     * @return ReflectionMethod
     */
    private function getReflectionMethodObject() {
        $methodname = $this->entity()->getData();
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