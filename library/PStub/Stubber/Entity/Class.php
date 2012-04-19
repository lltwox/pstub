<?php
/**
 * Class, representing class, in which method can be stubbed
 *
 * @author lex
 *
 */
class PStub_Stubber_Entity_Class {

    /**
     * Link to an entity, specifying method to be stubbed
     *
     * @var PStub_Stubber_Entity
     */
    private $entity = null;

    /**
     * Name of the class to stub method in
     *
     * @var string
     */
    private $name = null;

    /**
     * Constructor
     *
     * @param PStub_Stubber_Entity $entity
     */
    public function __construct(PStub_Stubber_Entity $entity) {
        $this->entity = $entity;
    }

    /**
     * Stub entity in class
     *
     * @param string $name
     * @return PStub_Stubber
     */
    public function inClass($name) {
        $this->name = $name;
        return PStub_Stubber::create($this->entity, $this);
    }

    /**
     * Get class name
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

}