<?php
/**
 * Class, allowing to suppress any method of a class, like heavy constructors in
 * parent classes, or any other type of methods, that have hard-resolving
 * dependencies.
 *
 * Real suppression actions differs for normal methods and constructors, so
 * logic for suppression was separated into two classes, that extends this one.
 *
 * @author lex
 *
 */
abstract class PStub_Suppressor implements PStub_Revertable {

    /**
     * Entity object
     *
     * @var PStub_Suppressor_Entity
     */
    private $entity = null;

    /**
     * Create instance of suppressor object
     *
     * @param PStub_Suppressor_Entity $entity
     * @return PStub_Suppressor
     */
    public static function create(PStub_Suppressor_Entity $entity) {
        if ($entity->isConstructor()) {
            $suppressor = new PStub_Suppressor_Constructor($entity);
        } else {
            $suppressor = new PStub_Suppressor_Method($entity);
        }
        PStub_Registry::register($suppressor);

        return $suppressor;
    }

    /**
     * Constructor
     *
     * @param PStub_Suppressor_Entity $entity
     */
    private  function __construct(PStub_Suppressor_Entity $entity) {
        $this->entity = $entity;
    }

    /**
     * @todo
     *
     * @param string $classname
     * @return PStub_Suppressor
     */
    public function inClass($classname) {
        $this->suppress($classname);
        return $this;
    }

    /**
     * Revert all suppressions
     *
     */
    public function revert() {
        $this->revertSuppression();
    }

    /**
     * Suppress entity in class
     *
     * @param string $classname
     */
    abstract protected function suppress($classname);

    /**
     * Revert suppression of the entity in class
     *
     */
    abstract protected function revertSuppression();

    /**
     * Accessor to private entity field
     *
     * @return PStub_Suppressor_Entity
     */
    protected function entity() {
        return $this->entity;
    }

}