<?php
declare(strict_types=1);

namespace App\Controller;

use App\Logics\Common\ValidateLogic;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use Hyperf\Di\Annotation\Inject;

class BaseController extends AbstractController
{

    protected $logic;

    /**
     * @Inject()
     * @var ValidatorFactoryInterface
     */
    protected $validationFactory;

    public function __construct()
    {

    }


}