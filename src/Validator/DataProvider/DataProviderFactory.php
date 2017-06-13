<?php

namespace Aaronadal\ValidatorBundle\Validator\DataProvider;


use Symfony\Component\HttpFoundation\Request;

/**
 * @author Aarón Nadal <aaronadal.dev@gmail.com>
 */
class DataProviderFactory extends \Aaronadal\Validator\Validator\DataProvider\DataProviderFactory
{

    /**
     * {@inheritdoc}
     *
     * - If target is an instance of Request, a RequestDataProvider will be instantiated.
     */
    public function newDataProvider($source)
    {
        if($source instanceof Request) {
            return new RequestDataProvider($source);
        }

        return parent::newDataProvider($source);
    }

}
