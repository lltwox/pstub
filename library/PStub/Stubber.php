<?php
/**
 * Class, allowing to stub methods implementation with one of pre-defined ones
 *
 * Real stubbing actions differs for different entities, so
 * logic for stubbing was separated into two classes, that extends this one.
 *
 * @author lex
 *
 */
abstract class PStub_Stubber implements PStub_Revertable {

    /**
     * Entity object, describing type and data of entity to be stubbed
     *
     * @var PStub_Stubber_Entity
     */
    private $entity = null;

    /**
     * Entity class object, describing class in which entity should be stubed.
     *
     * @var PStub_Stubber_Entity_Class
     */
    private $entityClass = null;

    /**
     * List of used indexes in container
     *
     * @var array
     */
    private $usedIndexes = array(
        'value' => array(),
        'callback' => array(),
    );

    /**
     * Code templates to be used in stubbed functions
     *
     * @var array
     */
    protected static $templates = array(
        'self' => 'return $this;',
        'value' => 'return PStub_Stubber_Container::getValue(%index%);',
        'callback' => '
            $args = func_get_args();
            return PStub_Stubber_Container::invokeMethod(%index%, $args);
        ',
    );

    /**
     * Create instance of stubber class
     *
     * @param PStub_Stubber_Entity $entity
     * @param PStub_Stubber_Entity_Class $entityClass
     * @return PStub_Stubber
     */
    public static function create(
        PStub_Stubber_Entity $entity,
        PStub_Stubber_Entity_Class $entityClass = null
    ) {
        $type = $entity->getType();
        $stubberClass = get_class() . '_' . ucfirst($type);
        $stubber = new $stubberClass($entity, $entityClass);
        PStub_Registry::register($stubber);

        return $stubber;
    }

    /**
     * Constructor
     *
     * @param PStub_Stubber_Entity $entity
     * @param PStub_Stubber_Entity_Class $class
     */
    public function __construct(
        PStub_Stubber_Entity $entity,
        PStub_Stubber_Entity_Class $class = null
    ) {
        $this->entity = $entity;
        $this->entityClass = $class;
    }

    /**
     * Make entity return link to self ($this).
     *
     * @return PStub_Stubber
     */
    public function returnSelf() {
        $code = self::$templates['self'];
        $this->stub($code);

        return $this;
    }

    /**
     * Make entity return pre-defined value
     *
     * @param mixed $value
     * @return PStub_Stubber
     */
    public function returnValue($value) {
        $index = PStub_Stubber_Container::addValue($value);
        $this->usedIndexes['value'][] = $index;
        $code = str_replace('%index%', $index, self::$templates['value']);
        $this->stub($code);

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
        $index = PStub_Stubber_Container::addMethod($callback);
        $this->usedIndexes['callback'][] = $index;
        $code = str_replace('%index%', $index, self::$templates['callback']);
        $this->stub($code);

        return $this;
    }

    /**
     * Revert all stubs
     *
     */
    public function revert() {
        $this->revertStubs();
    }

    /**
     * Stub entity with provided code
     *
     * @param string $code
     */
    abstract protected function stub($code);

    /**
     * Revert all stubs done
     *
     */
    abstract protected function revertStubs();

    /**
     * Clean used indexed in container
     *
     */
    protected function cleanContainer() {
        foreach ($this->usedIndexes['value'] as $index) {
            PStub_Stubber_Container::removeValue($index);
        }
        $this->usedIndexes['value'] = array();

        foreach ($this->usedIndexes['callback'] as $index) {
            PStub_Stubber_Container::removeMethod($index);
        }
        $this->usedIndexes['callback'] = array();
    }

    /**
     * Accessor for entity
     *
     * @return PStub_Stubber_Entity
     */
    protected function entity() {
        return $this->entity;
    }

    /**
     * Accessor for entity class
     *
     * @return PStub_Stubber_Entity_Class
     */
    protected function entityClass() {
        return $this->entityClass;
    }

}