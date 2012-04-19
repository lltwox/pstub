<?php
/**
 * Suppressor class, that suppresses any method of the class,
 * except for the constructor
 *
 * @author lex
 *
 */
class PStub_Suppressor_Method extends PStub_Suppressor {

    /**
     * List of classes, where methods were suppressed
     *
     * @var array
     */
    private $classesWithSuppressions = array();

    /**
     * Suppress entity in class
     *
     * @param string $classname
     */
    protected function suppress($classname) {
        $methodname = $this->entity()->getMethod();
        $method = $this->getReflectionMethodObject($classname, $methodname);

        $declaringClassname = $method->getDeclaringClass()->getName();
        $this->suppressMethodInClass($declaringClassname, $methodname);
        $this->classesWithSuppressions[] = $declaringClassname;
    }

    /**
     * Revert suppression of the entity in class
     *
     */
    protected function revertSuppression() {
        $runkit = PStub_RunkitAdapter::getInstance();
        $methodname = $this->entity()->getMethod();

        try {
            foreach ($this->classesWithSuppressions as $classname) {
                $runkit->revertRedefinedMethod($classname, $methodname);
            }
        } catch (PStub_RunkitAdapter_Exception $e) {
            throw new PStub_Suppressor_Exception(
                'Revertion of method suppression failed: '
                . $e->getMessage()
            );
        }

        $this->classesWithSuppressions = array();
    }

    /**
     * Get reflection method object for given class
     *
     * @param string $classname
     * @param string $methodname
     * @return ReflectionMethod
     */
    private function getReflectionMethodObject($classname, $methodname) {
        $reflectionMethod = null;
        try {
            $reflectionMethod = new PStub_Reflection_Method(
                $classname, $methodname
            );
        } catch (ReflectionException $e) {
            throw new PStub_Suppressor_Exception(
                'Class ' . $classname
                . ' has no method ' . $methodname . 'defined'
            );
        }

        return $reflectionMethod;
    }

    /**
     * Suppress method in class
     *
     * @param string $classname
     * @param string $methodname
     * @throws PStub_Suppressor_Exception
     */
    private function suppressMethodInClass($classname, $methodname) {
        $runkit = PStub_RunkitAdapter::getInstance();
        try {
            $runkit->redefineMethod($classname, $methodname, '', '');
        } catch (PStub_RunkitAdapter_Exception $e) {
            throw new PStub_Suppressor_Exception(
                'Method suppression failed: '. $e->getMessage()
            );
        }
    }

}