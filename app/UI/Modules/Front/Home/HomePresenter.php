<?php declare(strict_types=1);

namespace App\UI\Modules\Front\Home;

use App\Model\Database\Entity\Article;
use App\UI\Modules\Front\BaseFrontPresenter;
use Doctrine\ORM\Exception\NotSupported;

final class HomePresenter extends BaseFrontPresenter
{
	/**
	 * @throws NotSupported
	 */
	public function actionDefault(): void
	{
		$articles = $this->entityManager->getArticleRepository()->findBy(['status' => Article::STATUS_PUBLISHED], ['updatedAt' => 'DESC'], 10);
		$this->getTemplate()->articles = $articles;
	}
	/**
	 * @throws NotSupported
	 */
	public function actionCategory(int $categoryId): void
	{
		$articles = $this->entityManager->getArticleRepository()->findBy(
			[
				'status' => Article::STATUS_PUBLISHED,
				'categoryId' => $categoryId
			],
			['updatedAt' => 'DESC'],
			10
		);
		$this->getTemplate()->articles = $articles;
		$this->getTemplate()->categoryName = Article::CATEGORIES_NAMES[$categoryId];
	}

}
