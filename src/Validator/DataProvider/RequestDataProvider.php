<?php

namespace Aaronadal\ValidatorBundle\Validator\DataProvider;


use Aaronadal\Validator\Exception\ParameterNotFoundException;
use Aaronadal\Validator\Validator\DataProvider\DataProviderInterface;
use Aaronadal\Validator\Validator\DataProvider\RecursiveArrayProviderTrait;
use Aaronadal\Validator\Validator\DataProvider\RecursiveObjectProviderTrait;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Aarón Nadal <aaronadal.dev@gmail.com>
 */
class RequestDataProvider implements DataProviderInterface, \Serializable
{
    use RecursiveArrayProviderTrait;
    use RecursiveObjectProviderTrait;

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
        $parameter = $this->request->request->get($key) ?: $this->request->query->get($key) ?: $this->request->files->get($key);
        if($parameter !== null) {
            return $parameter;
        }

        if($this->isRecursiveObjectParameter($key)) {
            return $this->getRecursiveObjectParameter($key);
        }

        if($this->isRecursiveArrayParameter($key)) {
            return $this->getRecursiveArrayParameter($key);
        }

        return $default;
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

    public function serialize()
    {
        return serialize([
            'query' => $this->request->query->all(),
            'request' => $this->request->request->all(),
            'attributes' => $this->request->attributes->all(),
            'cookies' => $this->request->cookies->all(),
            'server' => $this->request->server->all(),
            'content' => $this->request->getContent(),
        ]);
    }

    public function unserialize($serialized)
    {
        $serialized = unserialize($serialized);
        $this->request = new Request(
            $serialized['query'],
            $serialized['request'],
            $serialized['attributes'],
            $serialized['cookies'],
            [], // Files are not serializable.
            $serialized['server'],
            $serialized['content']
        );
    }
}
