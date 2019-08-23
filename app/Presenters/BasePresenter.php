<?php
namespace App\Presenters;

use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class FractalPresenter
 * @package Prettus\Repository\Presenter
 * @author Anderson Andrade <contato@andersonandra.de>
 */
abstract class BasePresenter extends FractalPresenter
{
    public function __construct()
    {
        parent::__construct();
        $this->fractal->setSerializer(new \League\Fractal\Serializer\JsonApiSerializer('http://localhost:9000'));
    }
}
