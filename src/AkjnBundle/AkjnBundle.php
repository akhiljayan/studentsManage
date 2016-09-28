<?php

namespace AkjnBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AkjnBundle extends Bundle
{
    
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
