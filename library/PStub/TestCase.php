<?php
/**
 * Basic testcase for tests, using PSTub lib
 *
 * @author lex
 *
 */
class PStub_TestCase extends PHPUnit_Framework_TestCase {

    /**
     * Suffixes for test cases, based on current naming conventions
     *
     */
    const TESTCLASS_SUFFIX = 'Test';

    /**
     * After test-case clean up
     *
     */
    public static function tearDownAfterClass() {
        PStub_Registry::revertAll();
    }

    /**
     * Inject stub implementation of the class instead of the real one in
     * places, where it is not possible to do with usual dependency injecton
     * methods.
     *
     * Note: After using this method it is necessary to explicitly revert the
     * injections, by calling either PStub::revertAll() (called automatically
     * after testcase is finished, unless overriden) or by calling revert()
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
     * suppressions, by calling either PStub::revertAll() (called automatically
     * after testcase is finished, unless overriden) or by calling revert()
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
     * suppressions, by calling either PStub::revertAll() (called automatically
     * after testcase is finished, unless overriden) or by calling revert()
     * method on the suppressor object.
     *
     * @return PStub_Stubber_Entity
     */
    public static function stub() {
        return new PStub_Stubber_Entity();
    }

    /**
     * Get name of the class, that current test case covers,
     * based on the naming conventions.
     *
     * @return string
     */
    protected static function getTestedClass() {
        $testClassname = get_called_class();
        $testedClassname = strstr($testClassname, self::TESTCLASS_SUFFIX, true);
        if (empty($testedClassname) || $testedClassname == $testClassname) {
            throw new PStub_Exception(
                'Test class is not named, according to naming'
                . ' conventions - does not end with '
                . self::TESTCLASS_SUFFIX.' suffix'
            );
        }

        return $testedClassname;
    }

}