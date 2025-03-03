<?php namespace Controllers;

use Zephyrus\Application\Controller as BaseController;
use Zephyrus\Network\Response;

abstract class Controller extends BaseController
{
    public function before(): ?Response
    {
        return parent::before();
    }

}
