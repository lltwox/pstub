<?php
/**
 * Class, allowing to create chains call
 *
 * @author lex
 *
 */
class PStub_Chain {

    /**
     * Create an object, that provided list of methods
     * can be called on in a chain
     *
     * @param array $methodsList - list of methods
     * @param Closure $callback - function that will be invoked on the last
     *                            call in chain
     */
    public static function create(array $methodsList, Closure $callback) {
        self::checkMethodsList($methodsList);

        $lastMethod = array_pop($methodsList);
        $reversedList = array_reverse($methodsList);

        $lastLink = new PStub_Chain_Link($lastMethod, $callback);
        $firstLink = $lastLink;

        foreach ($reversedList as $method) {
            $firstLink = new PStub_Chain_Link(
                $method,
                function() use ($firstLink) {
                    return $firstLink;
                }
            );
        }

        return $firstLink;
    }

    /**
     * Check, that provided methods list is correct
     *
     * @param array $methodsList
     */
    private static function checkMethodsList(array $methodsList) {
        foreach ($methodsList as $method) {
            if (!is_string($method)) {
                throw new PStub_Chain_Exception(
                    'Method list array should consist of names of methods'
                );
            }
        }
    }

}