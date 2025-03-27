<?php declare(strict_types=1);

namespace App\Console;

use App\Model\Database\Entity\Article;
use App\Model\Database\EntityManagerDecorator;
use Doctrine\ORM\EntityManager;
use Exception;
use Orhanerday\OpenAi\OpenAi;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'article:generate')]
class ArticleGenerateCommand extends Command
{
	//entityManager slouží pro práci s databází
	private EntityManagerDecorator $entityManager;

	// Konfigurace příkazu/commandu
	protected function configure(): void
	{
		$this->setDescription('Generates article from sourceContent.');
		$this->addArgument(
			"count",
			InputArgument::OPTIONAL,
			"Count of articles to generate",
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
		//vytáhne všechny články, které mají prázdný nadpis, obsah a mají obrázek
		$articles = $this->entityManager->createQueryBuilder()
			->from(Article::class, 'a')
			->select('a')
			->where('a.heading = :heading')->setParameter('heading', '')
			->andWhere('a.content = :content')->setParameter('content', '')
			->andWhere('a.picture IS NOT null')
			->setMaxResults($input->getArgument('count')) //počet článků
			->getQuery()
			->getResult();

		if (count($articles) == 0) {  //pokud nejsou žádné články, končí
			$output->writeln('All articles are generated.');
			return 0;
		}

		foreach ($articles as $article) {
			$result = $this->callChatGPT($article);
			// test if $resultText['heading'] is null, continue
			if ($result['heading'] == null) {
				continue;
			}
			// uložení hodnot do článku
			$article->setHeading($result['heading']);
			$article->setContent($result['content']);
			$article->setCategoryId($result['categoryId']);
			$article->setStatus(Article::STATUS_PUBLISHED);
			$article->setUpdatedAt();
			//uložení článku, nejdříve do php, poté do databáze
			$this->entityManager->persist($article);
			$this->entityManager->flush();

			$output->writeln('Article generated.');
		}
		$output->writeln('All articles generated.');
		return 0;
	}

	/**
	 * @param Article $article
	 * @return mixed
	 * @throws Exception
	 */
	public function callChatGPT(Article $article): mixed
	{
		//vytvoření textu pro kategorie z Article::CATEGORIES_NAMES_GPT
		//kde je pole s kategoriemi a jejich čísly
		$categoryText = "";
		foreach (Article::CATEGORIES_NAMES_GPT as $key => $value) {
			$categoryText .= '"' . $value . '": ' . $key . ", ";
		}

		$openAi = new OpenAi(getenv('CHAT_GPT_API_KEY'));
		$model = 'gpt-4o-mini';
		$complete = $openAi->chat([
			'model' => $model,
			'messages' => [
				[
					"role" => "user",
					"content" =>
						"Vygeneruj článek pro magazín se zajímavostmi, konkrétně jeho nadpis a obsah.
	Poskytnu ti obsah na toto téma z Wikipedie a ty mi vrať odpověd
	jako JSON soubor, s klíči heading, content a categoryId.
	Json musi byt validni a neměl by obsahovat žádné anotace '```json {' a podobně.
	Do categoryId vyplň číslo kategorie, která odpovídá obsahu: " . $categoryText . "
	a to dle tvého vygenerovaného obsahu.
	Potřebuji aby ta kategorie seděla co nejpřesněji podle toho obsahu.
	Pokud budeš vědět nějákou zajímavost, tak ji můžeš přidat do obsahu.
	Cílem je aby článek byl pro lidi zajímavý a přitažlivý, může být i lehce vtipný pokud to dané téma dovolí.
	Do hlavního textu můžeš použít smajlíky a emotikony, ale ne příliš, jen jako zajimavý element pro oko.
	----------
	Nadpis: " . $article->getSourceHeading() . "
	Text: " . $article->getSourceContent(),
				],
			],
		]);
		$data = json_decode($complete, true);
		//v $result['choices'][0]['message']['content'] je odpověď z chatGPT
		return json_decode($data['choices'][0]['message']['content'], true);
	}

}
