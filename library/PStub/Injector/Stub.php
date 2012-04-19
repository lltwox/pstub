<?php
/**
 * Class, that represent a `stub` entity - classes, that needed to be injected.
 *
 * @author lex
 *
 */
class PStub_Injector_Stub {

    /**
     * Default stub suffix
     *
     * @var string
     */
    private static $defaultStubSuffix = 'Stub';

    /**
     * Stub, used to form stub name, based on class name
     *
     * @var string
     */
    private $stubSuffix = null;

    /**
     * List of classes, to be replaced
     *
     * @var array
     */
    private $stubsList = null;

    /**
     * Set default stub suffix to be used in all stubs
     *
     * @param string $suffix
     */
    public static function setDefaultStubSuffix($suffix) {
        self::$defaultStubSuffix = $suffix;
    }

    /**
     * Set stub suffix to be used with this instance of stub
     *
     * @param string $suffix
     * @return PStub_Injector_Stub
     */
    public function usingSuffix($suffix) {
        $this->stubSuffix = $suffix;
        return $this;
    }

    /**
     * @todo
     *
     * @param string $classname
     * @return PStub_Injector
     */
    public function stubFor($classname) {
        $stubname = $this->getStubname($classname);
        $this->stubsList = array($classname => $stubname);

        return $this->createInjector();
    }

    /**
     * @todo
     *
     * @param array $classes
     * @return PStub_Injector
     */
    public function stubsFor(array $classes) {
        $replacement = array();
        foreach ($classes as $classname) {
            $stubname = $this->getStubname($classname);
            $replacement[$classname] = $stubname;
        }
        $this->stubsList = $replacement;

        return $this->createInjector();
    }

    /**
     * @todo
     *
     * @param string $stubname
     * @param string $classname
     * @return PStub_Injector
     */
    public function stub($stubname, $classname = null) {
        if (null == $classname) {
            $classname = $this->getClassname($stubname);
        }
        $this->stubsList = array($classname => $stubname);

        return $this->createInjector();
    }

    /**
     * @todo
     *
     * @param array $classes
     * @return PStub_Injector
     */
    public function stubs(array $classes) {
        $replacement = array();
        foreach ($classes as $classname => $stubname) {
            if (is_string($classname)) {
                $replacement[$classname] = $stubname;
            } else {
                $classname = $this->getClassname($stubname);
                $replacement[$classname] = $stubname;
            }
        }
        $this->stubsList = $replacement;

        return $this->createInjector();
    }

    /**
     * Get replacement array.
     *
     * @return array
     */
    public function getStubsList() {
        return $this->stubsList;
    }

    /**
     * Create injector instance
     *
     * @return PStub_Injector
     */
    private function createInjector() {
        return PStub_Injector::create($this);
    }

    /**
     * Get stub class name, according to current naming conventions
     *
     * @param string $classname
     * @return string
     */
    private function getStubname($classname) {
        return $classname . $this->getStubSuffix();
    }

    /**
     * Get classname from stubname, according to current naming conventions
     *
     * @param string $stubname
     * @return string
     */
    private function getClassname($stubname) {
        $stubSuffix = $this->getStubSuffix();
        $suffixInStub = substr($stubname, -1 * strlen($stubSuffix));
        if ($suffixInStub != $stubSuffix) {
            throw new PStub_Injector_Exception(
                'Invalid stubname, can\'t determine classname from it'
            );
        }

        return substr($stubname, 0, -1 * strlen($stubSuffix));
    }

    /**
     * Get stub suffix for current stub
     *
     * @return string
     */
    private function getStubSuffix() {
        if (null != $this->stubSuffix) {
            return $this->stubSuffix;
        }

        return self::$defaultStubSuffix;
    }

}