<?php

namespace Aaronadal\ValidatorBundle\Subject;


use Aaronadal\Validator\Subject\SubjectInterface;
use Aaronadal\Validator\Validator\DataProvider\DataProviderFactoryInterface;
use Aaronadal\Validator\Validator\DataSetter\DataSetterFactoryInterface;
use Aaronadal\Validator\Validator\ErrorCollector\ErrorCollectorInterface;
use Aaronadal\ValidatorBundle\Validator\DataProvider\RequestDataProvider;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @author Aarón Nadal <aaronadal.dev@gmail.com>
 */
class SubjectFactory extends \Aaronadal\Validator\Subject\SubjectFactory implements SubjectFactoryInterface
{

    private $requestStack;
    private $session;

    public function __construct(
        DataProviderFactoryInterface $dataProviderFactory,
        DataSetterFactoryInterface $dataSetterFactory,
        RequestStack $requestStack,
        Session $session
    ) {
        parent::__construct($dataProviderFactory, $dataSetterFactory);

        $this->requestStack = $requestStack;
        $this->session      = $session->getFlashBag();
    }

    protected function getSessionSubjectId($id)
    {
        return 'aaronadal.validator.subject.' . $id;
    }

    /**
     * {@inheritdoc}
     */
    public function newRequestProvidedSubject($id = null, $setter = null, ErrorCollectorInterface $errorCollector = null)
    {
        $request  = $this->requestStack->getCurrentRequest();
        $provider = new RequestDataProvider($request);
        $id       = $id ?: $request->get('_route');

        return $this->newSubject($id, $provider, $setter, $errorCollector);
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
    public function restoreSubject($id = null)
    {
        if(!$id) {
            $request = $this->requestStack->getCurrentRequest();
            $id      = $request->get('_route');
        }

        $id       = $this->getSessionSubjectId($id);
        $subjects = $this->session->get($id);
        if(!$subjects) {
            return null;
        }

        // Return the last registered subject for this ID.
        return array_values(array_slice($subjects, -1))[0];
    }
}
