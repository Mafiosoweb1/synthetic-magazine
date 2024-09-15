<?php declare(strict_types = 1);

namespace App\Console;


use App\Model\Database\Entity\Article;
use App\Model\Database\EntityManagerDecorator;
use App\Model\Utils\FileSystem;
use Doctrine\ORM\EntityManager;
use Exception;
use Nette\Http\Url;
use Nette\Utils\Strings;
use Orhanerday\OpenAi\OpenAi;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: self::NAME)]
class ArticleCreateCommand extends Command
{

	public const NAME = 'article:create';

	private EntityManagerDecorator $entityManager;

	protected function configure(): void
	{
		$this->setName(self::NAME);
		$this->setDescription('Creates article from wiki.');
	}

	public function __construct(EntityManagerDecorator $entityManager)
	{
		parent::__construct();
		$this->entityManager = $entityManager;
	}

	/**
	 * @throws Exception
	 */
	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		//nacteme clanky z wiki API
		$url = new Url('https://cs.wikipedia.org/w/api.php');
		$url->setQueryParameter('action', 'query');
		$url->setQueryParameter('generator', 'random');
		$url->setQueryParameter('grnnamespace', '0');
		$url->setQueryParameter('grnlimit', '10');
		$url->setQueryParameter('prop', 'extracts');
		$url->setQueryParameter('exintro', '');
		$url->setQueryParameter('explaintext', '');
		$url->setQueryParameter('format', 'json');

		$wikiData = FileSystem::read($url->getAbsoluteUrl());
		$wikiList = json_decode($wikiData, true);

		foreach ($wikiList['query']['pages'] as $page) {

			//test quality of article

			//1/ is short
			if (strlen($page['extract']) < 100) {
				continue;
			}
			//2/ contains "Rozcestník" in title or extract - use str_contains
			if (str_contains($page['title'], 'Rozcestník') || str_contains($page['extract'], 'Rozcestník')) {
				continue;
			}

			$article = new Article();
			$article->setHeading('');
			$article->setContent('');
			$article->setSourceContent($page['extract']);
			$article->setStatus(Article::STATUS_CONCEPT);
			$this->entityManager->persist($article);
		}
		$this->entityManager->flush();

		$output->writeln('Article created.');

		return 0;
	}

}
