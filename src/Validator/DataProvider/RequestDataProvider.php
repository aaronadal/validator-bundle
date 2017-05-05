<?php

namespace Aaronadal\ValidatorBundle\Validator\DataProvider;


use Aaronadal\Validator\Exception\ParameterNotFoundException;
use Aaronadal\Validator\Validator\DataProvider\DataProviderInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Aarón Nadal <aaronadal.dev@gmail.com>
 */
class RequestDataProvider implements DataProviderInterface
{

    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param Request $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function getParameter($key, $default = null)
    {
        return $this->request->get($key, null) ?: $this->request->files->get($key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function getParameterOrFail($key)
    {
        $value = $this->getParameter($key, null);
        if($value === null) {
            throw new ParameterNotFoundException("The $key parameter cannot be retrieved by this provider.");
        }

        return $value;
    }
}
