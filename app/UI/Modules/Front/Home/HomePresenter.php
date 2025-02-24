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
	public function actionDefault(int $page=1): void
	{
		$articles = $this->entityManager->getArticleRepository()->findBy(['status' => Article::STATUS_PUBLISHED], ['updatedAt' => 'DESC'], 10, ($page - 1) * 10);
		$articlesCount = $this->entityManager->getArticleRepository()->count(['status' => Article::STATUS_PUBLISHED]);
		$pages = ceil($articlesCount / 10);
		$this->getTemplate()->articles = $articles;
		$this->getTemplate()->articlesCount = $articlesCount;
		$this->getTemplate()->pages = $pages;
		$this->getTemplate()->page = $page;
	}
	public function actionArticle(int $id): void
	{
		$article = $this->entityManager->getArticleRepository()->find($id);
		if ($article === null) {
			$this->flashError('Článek nebyl nalezen');
			$this->redirect('Home:default');
		}
		$this->getTemplate()->article = $article;
	}
	/**
	 * @throws NotSupported
	 */
	public function actionCategory(int $categoryId, int $page = 1): void
	{
		$articles = $this->entityManager->getArticleRepository()->findBy(
			[
				'status' => Article::STATUS_PUBLISHED,
				'categoryId' => $categoryId
			],
			['updatedAt' => 'DESC'],
			10,
			($page - 1) * 10
		);
		$articlesCount = $this->entityManager->getArticleRepository()->count(
			[
				'status' => Article::STATUS_PUBLISHED,
				'categoryId' => $categoryId
			]
		);
		$pages = ceil($articlesCount / 10);
		$this->getTemplate()->articles = $articles;
		$this->getTemplate()->categoryName = Article::CATEGORIES_NAMES[$categoryId];
		$this->getTemplate()->articlesCount = $articlesCount;
		$this->getTemplate()->pages = $pages;
		$this->getTemplate()->page = $page;

	}

}
