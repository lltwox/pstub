<?php
/**
 * Reflection method with extended functionallity
 *
 * @author lex
 */
class PStub_Reflection_Method extends ReflectionMethod {

    /**
     * List of methods with substituded body
     *
     * @var array
     */
    private static $redefinedMethodsBodies = array();

    /**
     * Get method body.
     * It is retreived, from the file source.
     *
     * @todo Fix behaviou for case, when there more symbols on the same line
     *       with ending curly brace
     * @return string
     */
    public function getBody() {
        $classname = $this->getDeclaringClass()->getName();
        if (isset(self::$redefinedMethodsBodies[$classname][$this->name])) {
            return self::$redefinedMethodsBodies[$classname][$this->name];
        }

        $source = $this->getSource();
        // extracting body from source
        $body = trim($source);
        $body = strstr($body, $this->name);
        $body = strstr($body, '{');
        $body = substr($body, 1, -1);

        return $body;
    }

    /**
     * Set new body for method.
     * All paramters and access flags will be kept the same.
     *
     * @param string $body
     */
    public function setBody($body) {
        $classname = $this->getDeclaringClass()->getName();
        $runkit = PStub_RunkitAdapter::getInstance();
        $runkit->redefineMethod(
            $classname,
            $this->name,
            $this->getParametersList(),
            $body,
            $this->getRunkitAccessFlag()
        );

        self::$redefinedMethodsBodies[$classname][$this->name] = $body;
    }

    /**
     * Revert body to original
     *
     */
    public function revertBody() {
        $classname = $this->getDeclaringClass()->getName();
        // if was not redefined, nothing to do
        if (!isset(self::$redefinedMethodsBodies[$classname][$this->name])) {
            return;
        }
        $runkit = PStub_RunkitAdapter::getInstance();
        try {
            $runkit->revertRedefinedMethod($classname, $this->name);
        } catch (PStub_RunkitAdapter_Exception $e) {
            // weird, but nothing criminal
        }

        unset(self::$redefinedMethodsBodies[$classname][$this->name]);
    }

    /**
     * Get source of the method body (as it is in the source file)
     *
     * @return string
     */
    private function getSource() {
        $start = $this->getStartLine();
        $end = $this->getEndLine();

        $filename = $this->getFileName();
        if (!file_exists($filename)) {
            return '';
        }
        // get all contents of the source file
        $source = file($filename);
        // extract and implode needed lines
        $body = array_reduce(
            array_slice($source, $start - 1, $end - $start + 1),
            function ($result, $element) {
                return $result .= $element;
            }
        );

        return $body;
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

    /**
     * Get runkit constant representing method's access modifier
     *
     * @return integer
     */
    private function getRunkitAccessFlag() {
        if ($this->isPublic()) {
            return RUNKIT_ACC_PUBLIC;
        }

        if ($this->isProtected()) {
            return RUNKIT_ACC_PROTECTED;
        }

        return RUNKIT_ACC_PRIVATE;
    }

}