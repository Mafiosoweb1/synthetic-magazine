<?php declare(strict_types = 1);

namespace App\Model\Database;

use App\Model\Database\Entity\Article;
use App\Model\Database\Repository\ArticleRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\NotSupported;

/**
 * @mixin EntityManager
 */
trait TRepositories
{

	/**
	 * @throws NotSupported
	 */
	public function getArticleRepository(): ArticleRepository
	{
		return $this->getRepository(Article::class);
	}

}
