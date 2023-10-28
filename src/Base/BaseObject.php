<?php

namespace Gotyefrid\ComebackpwParser\Base;

use Gotyefrid\ComebackpwParser\Base\Helpers\ObjectHelper;

class BaseObject
{
    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        if (!empty($config)) {
            ObjectHelper::configure($this, $config);
        }
    }
}