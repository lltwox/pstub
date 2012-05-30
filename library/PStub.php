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
     * Note: After using this method it is necessary to explicitly revert the
     * injections, by calling either PStub::revertAll() or by calling revert()
     * method on the injector object.
     *
     * @return PStub_Injector_Stub
     */
    public static function inject() {
        return new PStub_Injector_Stub();
    }

    /**
     * Suppress method of the class. After suppressing method's code won't
     * execute, when invoked.
     *
     * Note: If suppressing constructor in class, all classes extending it, that
     * are going to be used later in code should be pre-loaded into userspace
     * (in other words included), otherwise a fatal error will occur (this
     * limitation exists due to current implementation of runkit).
     *
     * Note: After using this method it is necessary to explicitly revert the
     * suppressions, by calling either PStub::revertAll() or by calling revert()
     * method on the suppressor object.
     *
     * @return PStub_Suppressor_Entity
     */
    public static function suppress() {
        return new PStub_Suppressor_Entity();
    }

    /**
     * Replace method od function implementation with stubbed one.
     *
     * Note: After using this method it is necessary to explicitly revert the
     * suppressions, by calling either PStub::revertAll() or by calling revert()
     * method on the suppressor object.
     *
     * @return PStub_Stubber_Entity
     */
    public static function stub() {
        return new PStub_Stubber_Entity();
    }

}