<?php
/**
 * Class, allowing to inject own class implementations
 * instead of not-replacable dependencies
 *
 * @author lex
 *
 */
class PStub_Injector implements PStub_Revertable {

    /**
     * Stub object
     *
     * @var PStub_Injector_Stub
     */
    private $stub = null;

    /**
     * List of methods, that were injected with stubs
     *
     * @var array
     */
    private $methodsWithRedefinitions = array();

    /**
     * Create instance of injector
     *
     * @param PStub_Injector_Stub $stub
     * @return PStub_Injector
     */
    public static function create(PStub_Injector_Stub $stub) {
        $injector = new PStub_Injector($stub);
        PStub_Registry::register($injector);

        return $injector;
    }

    /**
     * Private constructor to enforce use of factory method
     *
     */
    private function __construct(PStub_Injector_Stub $stub) {
        $this->stub = $stub;
    }

    /**
     * Inject stubs into all methods of one class
     *
     * @param string $classname
     * @return PStub_Injector
     */
    public function intoClass($classname) {
        return $this->process(array($classname));
    }

    /**
     * Inject stubs into all methods of list of classes
     *
     * @param array $classes
     * @return PStub_Injector
     */
    public function intoClasses(array $classes) {
        return $this->process($classes);
    }

    /**
     * Inject stubs into one method of the class
     *
     * @param string $methodname
     * @param string $classname
     * @return PStub_Injector
     */
    public function intoMethod($methodname, $classname) {
        return $this->process(array($classname), array($methodname));
    }

    /**
     * Inject stubs into list of methods of the class
     *
     * @param array $methods
     * @param string $classname
     * @return PStub_Injector
     */
    public function intoMethods(array $methods, $classname) {
        if (empty($methods)) {
            throw new PStub_Injector_Exception(
                'Provided list of methods to inject stub to is empty'
            );
        }

        return $this->process(array($classname), $methods);
    }

    /**
     * Revert all injections.
     *
     * Note: if there were than one injection in same method of class, all will
     * be reverted.
     *
     */
    public function revert() {
        /* @var $method PStub_Reflection_Method */
        foreach ($this->methodsWithRedefinitions as $classname => $methods) {
            foreach ($methods as $methodname) {
                $method = new PStub_Reflection_Method(
                    $classname, $methodname
                );
                $method->revertBody();
            }
        }
        $this->methodsWithRedefinitions = array();
    }

    /**
     * Process list of classes and methods and inject stubs in them
     *
     * @param array $classes
     * @param array $methods
     * @return PStub_Injector
     */
    private function process(array $classes, array $methods = array()) {
        foreach ($classes as $classname) {
            $class = new PStub_Reflection_Class($classname);
            $classMethods = $class->getMethods();
            $filteredMethods = $this->filterMethods($classMethods, $methods);
            /* @var $method PStub_Reflection_Method */
            foreach ($filteredMethods as $method) {
                $body = $method->getBody();
                $newBody = $this->replaceWithStubsIn($body);
                if ($newBody) {
                    $method->setBody($newBody);
                }
                $this->methodsWithRedefinitions[$classname][]
                    = $method->getName()
                ;
            }
        }

        return $this;
    }

    /**
     * Replace all stubs in method's body
     *
     * @param string $body
     * @return string|boolean - new body, or false, if no replacement was needed
     */
    private function replaceWithStubsIn($body) {
        $stubsList = $this->stub->getStubsList();
        $classes = array_keys($stubsList);
        $stubs = array_values($stubsList);

        $needReplacement = false;
        foreach ($classes as $stubbedClass) {
            if (strpos($body, $stubbedClass) !== false) {
                $needReplacement = true;
                break;
            }
        }
        if (!$needReplacement) {
            return false;
        }

        $newBody = str_replace($classes, $stubs, $body);

        return $newBody;
    }

    /**
     * Filter array of reflection methods
     *
     * @param array $methods - list of reflection methods
     * @param array $neededMethods - list of needed method names, if empty, all
     *                               specified methods are returned
     */
    private function filterMethods(array $methods, array $neededMethods) {
        if (empty($neededMethods)) {
            return $methods;
        }

        $result = array();
        /* @var $method PStub_Reflection_Method */
        foreach ($methods as $method) {
            if (in_array($method->getName(), $neededMethods)) {
                $result[] = $method;
            }
        }

        return $result;
    }

}