<?php
/**
 * Interface for objects, who can apply changes to running code,
 * that should be reverted.
 *
 * @author lex
 *
 */
interface PStub_Revertable {

    /**
     * Revert all changes, made by the object
     *
     */
    public function revert();

}
