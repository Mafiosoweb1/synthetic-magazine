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
		$articles = $this->entityManager->getArticleRepository()->findBy(
			[
				'status' => Article::STATUS_PUBLISHED,
				'categoryId' => Article::CATEGORIES_ENABLED
			],
			[
				'updatedAt' => 'DESC'
			],
			10,
			($page - 1) * 10)
		;
		$articlesCount = $this->entityManager->getArticleRepository()->count(
			[
				'status' => Article::STATUS_PUBLISHED,
				'categoryId' => Article::CATEGORIES_ENABLED
			]);
		$pages = ceil($articlesCount / 10);
		$this->getTemplate()->articles = $articles;
		$this->getTemplate()->articlesCount = $articlesCount;
		$this->getTemplate()->pages = $pages;
		$this->getTemplate()->page = $page;
	}

	/**
	 * @throws NotSupported
	 */
	public function actionArticle(int $id): void
	{
		$article = $this->entityManager->getArticleRepository()->findOneBy(
			[
				'id' => $id,
				'status' => Article::STATUS_PUBLISHED
			]
		);
		if ($article === null) {
			$this->flashError('Článek nebyl nalezen');
			$this->redirect('Home:default');
		}

		//3 související články
		$relatedArticles = $this->entityManager->getArticleRepository()->findBy(
			[
				'status' => Article::STATUS_PUBLISHED,
				'categoryId' => $article->getCategoryId(),
			],
			['updatedAt' => 'DESC'],
			12
		);

		//without this articleId and max 3
		$relatedArticles = array_filter($relatedArticles, function (Article $a) use ($id) {
			return $a->getId() !== $id;
		});
		//random order
		shuffle($relatedArticles);

		//only3
		$relatedArticles = array_slice($relatedArticles, 0, 6);


		$this->getTemplate()->article = $article;
		$this->getTemplate()->relatedArticles = $relatedArticles;
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
