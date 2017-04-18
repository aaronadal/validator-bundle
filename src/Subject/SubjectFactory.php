<?php

namespace Aaronadal\ValidatorBundle\Subject;


use Aaronadal\Validator\Subject\SubjectInterface;
use Aaronadal\ValidatorBundle\Validator\DataProvider\RequestDataProvider;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

/**
 * @author Aarón Nadal <aaronadal.dev@gmail.com>
 */
class SubjectFactory extends \Aaronadal\Validator\Subject\SubjectFactory implements SubjectFactoryInterface
{

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var FlashBagInterface
     */
    private $session;

    public function __construct(RequestStack $requestStack, FlashBagInterface $session)
    {
        $this->requestStack = $requestStack;
        $this->session      = $session;
    }

    protected function getSessionSubjectId($id)
    {
        return 'aaronadal.validator.subject.' . $id;
    }

    /**
     * {@inheritdoc}
     */
    public function newRequestProvidedSubject($id = null, $setter = null)
    {
        $request  = $this->requestStack->getCurrentRequest();
        $provider = new RequestDataProvider($request);
        $id       = $id ?: $request->get('_route');

        return $this->newSubject($id, $provider, $setter);
    }

    /**
     * {@inheritdoc}
     */
    public function storeSubject(SubjectInterface $subject)
    {
        $id = $this->getSessionSubjectId($subject->getId());
        $this->session->add($id, $subject);
    }

    /**
     * {@inheritdoc}
     */
    public function restoreSubject($id)
    {
        $id = $this->getSessionSubjectId($id);
        $subjects = $this->session->get($id);
        if(!$subjects) {
            return null;
        }

        // Return the last registered subject for this ID.
        return array_values(array_slice($subjects, -1))[0];
    }
}
