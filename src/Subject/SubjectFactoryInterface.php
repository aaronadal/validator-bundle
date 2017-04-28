<?php

namespace Aaronadal\ValidatorBundle\Subject;


use Aaronadal\Validator\Subject\SubjectInterface;

/**
 * Interface for instantiate new subjects.
 *
 * @author Aarón Nadal <aaronadal.dev@gmail.com>
 */
interface SubjectFactoryInterface extends \Aaronadal\Validator\Subject\SubjectFactoryInterface
{

    /**
     * Creates and initializes a new SubjectInterface instance with a RequestDataProvider
     * that wraps the current master request as a data setter.
     *
     * @param string|null $id     Unique identifier for the new subject instance. 
     *                            If null, the current route identifier will be used.
     * @param mixed       $setter The data setter.
     *
     * @return SubjectInterface The new subject.
     */
    public function newRequestProvidedSubject($id = null, $setter = null);

    /**
     * Stores a subject.
     *
     * @param SubjectInterface $subject The subject to store.
     */
    public function storeSubject(SubjectInterface $subject);

    /**
     * Restores a previously stored subject.
     *
     * @param string|null $id The subject ID. If null, the current route identifier will be used.
     *
     * @return SubjectInterface|null The restored subject.
     */
    public function restoreSubject($id = null);

}
