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
     * @param string $id       Unique identifier for the new subject instance.
     * @param mixed  $setter   The data setter.
     *
     * @return SubjectInterface The new subject.
     */
    public function newRequestProvidedSubject($id, $setter = null);

    /**
     * Stores a subject.
     *
     * @param SubjectInterface $subject
     */
    public function storeSubject(SubjectInterface $subject);

    /**
     * Restores a previously stored subject.
     *
     * @param string $id The subject ID.
     *
     * @return SubjectInterface|null
     */
    public function restoreSubject($id);

}
