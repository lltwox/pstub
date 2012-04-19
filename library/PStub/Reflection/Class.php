<?php
/**
 * Reflection class with extended functionallity
 *
 * @author lex
 *
 */
class PStub_Reflection_Class extends ReflectionClass {

    /**
     * Return list of methods.
     * Overriden to return array of PStub_Reflection_Method
     *
     * @return array
     */
    public function getMethods($filter = null) {
        $methods = parent::getMethods();
        $pstubMethods = array();
        foreach ($methods as $method) {
            $pstubMethods[] = new PStub_Reflection_Method(
                $this->name, $method->name
            );
        }

        return $pstubMethods;
    }

}