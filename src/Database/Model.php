<?php
/**
 * @package LaravelBoot
 *
 * @internal
 *
 * @author mawenpei
 * @date 2017/7/17 10:48
 * @version
 */
namespace LaravelBoot\Foundation\Database;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use LaravelBoot\Foundation\Database\Traits\Extendable;

/**
 * Class Model.
 */
class Model extends EloquentModel
{
    use Extendable;
}
