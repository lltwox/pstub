<?php
/**
 * Reflection function with extended functionallity
 *
 * @author lex
 */
class PStub_Reflection_Function extends ReflectionFunction {

    /**
     * List of functions with substituded body
     *
     * @var array
     */
    private static $redefinedFunctionsBodies = array();

    /**
     * Set new body for method.
     * All paramters will be kept the same.
     *
     * @param string $body
     */
    public function setBody($body) {
        $runkit = PStub_RunkitAdapter::getInstance();
        $runkit->redefineFunction(
            $this->name, $this->getParametersList(), $body
        );

        self::$redefinedFunctionsBodies[$this->name] = $body;
    }

    /**
     * Revert body to original
     *
     */
    public function revertBody() {
        // if was not redefined, nothing to do
        if (!isset(self::$redefinedFunctionsBodies[$this->name])) {
            return;
        }
        $runkit = PStub_RunkitAdapter::getInstance();
        try {
            $runkit->revertRedefinedFunction($this->name);
        } catch (PStub_RunkitAdapter_Exception $e) {
            // weird, but nothing criminal
        }

        unset(self::$redefinedFunctionsBodies[$this->name]);
    }

    /**
     * Get parameters in a comma-seperated list
     *
     * @return string
     */
    private function getParametersList() {
        $list = array_map(
            function($parameter) {
                /* @var $parameter ReflectionParameter */
                if ($parameter->isOptional()) {
                    try {
                        $value = $parameter->getDefaultValue();
                    } catch (ReflectionException $e) {
                        $value = null;
                    }

                    if (null == $value) {
                        $value = 'null';
                    }
                    $defaultValue = ' = ' . $value;
                } else {
                    $defaultValue = '';
                }
                return '$' . $parameter->getName() . $defaultValue;
            }, $this->getParameters()
        );
        return implode(', ', $list);
    }

}