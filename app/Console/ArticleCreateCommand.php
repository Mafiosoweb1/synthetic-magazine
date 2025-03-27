<?php declare(strict_types=1);

namespace App\Console;

use App\Model\Database\Entity\Article;
use App\Model\Database\EntityManagerDecorator;
use App\Model\Utils\FileSystem;
use Exception;
use Nette\Http\Url;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'article:create')]
class ArticleCreateCommand extends Command
{
	//entityManager slouží pro práci s databází
	private EntityManagerDecorator $entityManager;

	// Konfigurace příkazu/commandu
	protected function configure(): void
	{
		$this->setDescription('Creates article from wiki.');
		$this->addArgument(
			"count",
			InputArgument::OPTIONAL,
			"Number of articles to create",
			1
		);
	}

	// Konstruktor třídy - Naplní EntityManager do třídy
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
		$count = $input->getArgument('count');
		for ($i = 1; $i <= $count; $i++) { //cyklus pro vytvoření článků
			$wikiList = $this->getWikiData(); //získání dat z wiki
			foreach ($wikiList['query']['pages'] as $page) { // Iterace přes články

				// test 1 Má článek z wiki méně než 100 znaků - pokračuj na další iteraci
				if (strlen($page['extract']) < 100) { continue; }
				// test 2 obsahuje v názvu slovo Rozcestník - pokračuj na další iteraci
				if (str_contains($page['title'], 'Rozcestník')) { continue; }

				$pictureUrl = $this->findImage($page); //nalezení obrázku

				$article = new Article(); //vytvoření nového článku
				$article->setCreatedAt();
				$article->setHeading('');
				$article->setContent('');
				$article->setWikiId($page['pageid']);
				$article->setPicture($pictureUrl); //nastavení obrázku
				$article->setSourceHeading($page['title']);
				$article->setSourceContent($page['extract']);
				$article->setStatus(Article::STATUS_CONCEPT);
				$this->entityManager->persist($article); //uložení článku

				try {
					$this->entityManager->flush(); //uložení do databáze
				} catch (Exception $e) { //chyba při ukládání
					$output->writeln('Error: ' . $e->getMessage());
					continue;
				}
			} //konec foreach,když je continue tak se přeskočí na další iteraci
			$output->writeln('Article created.');
		}
		$output->writeln('All articles created.');
		return 0;
	}

	/**
	 * @return mixed
	 */
	public function getWikiData(): mixed
	{
		//nacteme clanky z wiki API
		$url = new Url('https://cs.wikipedia.org/w/api.php'); //sestavení url
		$url->setQueryParameter('action', 'query');
		$url->setQueryParameter('generator', 'random');
		$url->setQueryParameter('grnnamespace', '0');
		$url->setQueryParameter('grnlimit', '1');
		$url->setQueryParameter('prop', 'extracts|pageimages');
		$url->setQueryParameter('exintro', '');
		$url->setQueryParameter('explaintext', '');
		$url->setQueryParameter('piprop', 'original');
		$url->setQueryParameter('format', 'json');

		//načtení dat z url(absolutní url)
		$wikiData = FileSystem::read($url->getAbsoluteUrl());
		return json_decode($wikiData, true); //dekódování jsonu
	}

	/**
	 * @param mixed $page
	 * @return string|null
	 */
	public function findImage(mixed $page): ?string
	{
		$exceptions = ['.svg','.gif','.jpeg','.webp','.png','.jpg']; //koncovka musí být obrázek
		$pictureUrl = null;  //url obrázku, defaultně null
		if (isset($page['original']) && isset($page['original']['source'])) { //pokud existuje obrázek
			$foundValidImage = false; //obrázku nevěříme
			foreach ($exceptions as $exception) {
				if (str_ends_with($page['original']['source'], $exception)) {
					$foundValidImage = true; //obrázek je validní
					break; //ukončení foreach
				}
			} //konec break
			if ($foundValidImage) { //pokud je obrázek validní
				$pictureUrl = $page['original']['source']; //nastavení obrázku
			}
		}
		return $pictureUrl;
	}

}
