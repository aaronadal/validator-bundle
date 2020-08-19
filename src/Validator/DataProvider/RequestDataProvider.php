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
        $parameter = $this->request->request->get($key);
        if($parameter === null) {
            $parameter = $this->request->query->get($key);
            if($parameter === null) {
                $parameter = $this->request->files->get($key);
            }
        }

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
    public function getParameterArray($key, $default = [])
    {
        $parameter = $this->request->request->all($key);
        if($parameter === null) {
            $parameter = $this->request->query->all($key);
            if($parameter === null) {
                $parameter = $this->request->files->all($key);
            }
        }

        if($parameter !== null) {
            return $parameter;
        }

        $result = null;
        if($this->isRecursiveObjectParameter($key)) {
            $result = $this->getRecursiveObjectParameter($key);
        }
        elseif($this->isRecursiveArrayParameter($key)) {
            $result = $this->getRecursiveArrayParameter($key);
        }

        if($result === null) {
            return $default;
        }

        return $result;
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

    /**
     * {@inheritdoc}
     */
    public function getParameterArrayOrFail($key)
    {
        $value = $this->getParameterArray($key, null);
        if($value === null) {
            throw new ParameterNotFoundException("The $key parameter cannot be retrieved by this provider.");
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function allParameters()
    {
        return array_merge(
            $this->request->request->all(),
            $this->request->query->all(),
            $this->request->files->all()
        );
    }

    public function serialize()
    {
        return serialize(
            [
                'query'      => $this->request->query->all(),
                'request'    => $this->request->request->all(),
                'attributes' => $this->request->attributes->all(),
                'cookies'    => $this->request->cookies->all(),
                'server'     => $this->request->server->all(),
                'content'    => $this->request->getContent(),
            ]);
    }

    public function unserialize($serialized)
    {
        $serialized    = unserialize($serialized);
        $this->request = new Request(
            $serialized['query'],
            $serialized['request'],
            $serialized['attributes'],
            $serialized['cookies'],
            [], // Files are not serializable.
            $serialized['server'],
            $serialized['content']);
    }
}
