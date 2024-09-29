<?php declare(strict_types = 1);

namespace App\Model\Router;

use App\Model\Database\Entity\Article;
use Nette\Application\Routers\RouteList;
use Nette\Utils\Strings;

final class RouterFactory
{

	private RouteList $router;

	public function __construct()
	{
		$this->router = new RouteList();
	}

	public function create(): RouteList
	{
		$this->buildAdmin();
		$this->buildFront();

		return $this->router;
	}

	protected function buildAdmin(): void
	{
		$this->router[] = $list = new RouteList('Admin');
		$list->addRoute('admin/<presenter>/<action>[/<id>]', 'Home:default');
	}

	protected function buildFront(): void
	{
		$this->router[] = $list = new RouteList('Front');
		foreach (Article::CATEGORIES_ENABLED as $categoryId) {
			$list->addRoute('/' . Strings::webalize(Article::CATEGORIES_NAMES[$categoryId]) . '[/<page>]', [
				'presenter' => 'Home',
				'action' => 'category',
				'categoryId' => $categoryId,
			]);
		}

		$list->addRoute('<presenter>/<action>[/<id>]', 'Home:default');
	}

}
