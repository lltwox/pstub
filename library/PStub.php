<?php
/**
 * Access point for all library's functionality
 *
 * @author lex
 *
 */
class PStub {

    /**
     * Clean up all stubbed/injected or suppressed things
     *
     */
    public static function cleanUp() {
        PStub_Registry::revertAll();
    }

    /**
     * Inject stub implementation of the class instead of the real one in
     * places, where it is not possible to do with usual dependency injecton
     * methods.
     *
     * @return PStub_Injector_Stub
     */
    public static function inject() {
        return new PStub_Injector_Stub();
    }

    /**
     * Suppress method of the class.
     *
     * @return PStub_Suppressor_Entity
     */
    public static function suppress() {
        return new PStub_Suppressor_Entity();
    }

    /**
     * Replace real implementation with the stub
     *
     * @return PStub_Stubber_Entity
     */
    public static function stub() {
        return new PStub_Stubber_Entity();
    }

}