<?php declare(strict_types = 1);

namespace App\UI\Modules\Front;

use App\Model\Database\EntityManagerDecorator;
use App\UI\Modules\Base\UnsecuredPresenter;
use Doctrine\ORM\EntityManager;

abstract class BaseFrontPresenter extends UnsecuredPresenter
{

	protected EntityManagerDecorator $entityManager;

	public function __construct(EntityManagerDecorator $entityManager)
	{
		parent::__construct();
		$this->entityManager = $entityManager;
	}
}
