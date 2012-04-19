<?php
/**
 * Suppressor class, that suppresses constructor of the class.
 *
 * @author lex
 *
 */
class PStub_Suppressor_Constructor extends PStub_Suppressor {

    /**
     * List of classes, where constructors were suppressed by redefining
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
        $constructor = $this->getReflectionConstructorObject($classname);
        $declaringClassname = $constructor->getDeclaringClass()->getName();

        $this->suppressConstructorInClass($declaringClassname);
        $this->classesWithSuppressions[] = $declaringClassname;
    }

    /**
     * Revert suppression of the entity in class
     *
     */
    protected function revertSuppression() {
        $runkit = PStub_RunkitAdapter::getInstance();

        try {
            foreach ($this->classesWithSuppressions as $classname) {
                $runkit->revertRedefinedConstructor($classname);
            }
        } catch (PStub_RunkitAdapter_Exception $e) {
            throw new PStub_Suppressor_Exception(
                'Revertion of constructor suppression failed: '
                . $e->getMessage()
            );
        }

        $this->classesWithSuppressions = array();
    }

    /**
     * Get reflection constructor object for given class
     *
     * @param string $classname
     * @return ReflectionMethod
     */
    private function getReflectionConstructorObject($classname) {
        $reflectionClass = new ReflectionClass($classname);
        /* @var $constructor ReflectionMethod */
        $constructor = $reflectionClass->getConstructor();
        if (null == $constructor) {
            throw new PStub_Suppressor_Exception(
                'No constructor defined, nothing to suppress'
            );
        }

        return $constructor;
    }

    /**
     * Suppress constructor in class
     *
     * @param string $classname
     * @throws PStub_Suppressor_Exception
     */
    private function suppressConstructorInClass($classname) {
        $runkit = PStub_RunkitAdapter::getInstance();
        try {
            $runkit->redefineConstructor($classname, '', '');
        } catch (PStub_RunkitAdapter_Exception $e) {
            throw new PStub_Suppressor_Exception(
                'Constructor suppression failed: ' . $e->getMessage()
            );
        }
    }

}