<?php
/**
 * Registry of all stub objects, that need to be reverted at some point
 *
 * @author lex
 *
 */
class PStub_Registry {

    /**
     * List of all registered objects
     *
     * @var array
     */
    private static $registry = array();

    /**
     * Register revertable object, to keep track of all changes and to be able
     * to revert them all at once
     *
     * @param PStub_Revertable $revertable
     */
    public static function register(PStub_Revertable $revertable) {
        self::$registry[] = $revertable;
    }

    /**
     * Revert all changes
     *
     */
    public static function revertAll() {
        /* @var $revertable PStub_Revertable */
        foreach (self::$registry as $revertable) {
            $revertable->revert();
        }

        self::$registry = array();
    }

}