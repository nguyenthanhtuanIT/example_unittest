<?php

namespace App\Presenters;

use App\Transformers\CinemaTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class CinemaPresenter.
 *
 * @package namespace App\Presenters;
 */
class CinemaPresenter extends FractalPresenter
{
    protected $resourceKeyItem = 'Cinema';
    protected $resourceKeyCollection = 'Cinema';
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new CinemaTransformer();
    }

    public function setupSerializer()
    {
        $this->fractal->setSerializer(new \League\Fractal\Serializer\JsonApiSerializer('http://localhost:9000/api/v1'));
    }
}
