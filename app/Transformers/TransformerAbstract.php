<?php


namespace App\Transformers;

use App\Traits\TimeHelper;
use League\Fractal\TransformerAbstract as Transformer;


abstract class TransformerAbstract extends Transformer
{
    use TimeHelper;
}
