<?php declare(strict_types=1);

namespace App\Console;


use App\Model\Database\Entity\Article;
use App\Model\Database\EntityManagerDecorator;
use Doctrine\ORM\EntityManager;
use Exception;
use Orhanerday\OpenAi\OpenAi;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: self::NAME)]
class ArticleGenerateCommand extends Command
{

	public const NAME = 'article:generate';

	private EntityManagerDecorator $entityManager;

	protected function configure(): void
	{
		$this->setName(self::NAME);
		$this->setDescription('Generates article from sourceContent.');
		$this->addArgument("count", null, "Count of articles to generate", 1);
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
		$count = $input->getArgument('count');
		$openAi = new OpenAi(getenv('CHAT_GPT_API_KEY'));

		#$list = $openAi->listModels();
		$model = 'gpt-4o-mini';


		$articles = $this->entityManager->createQueryBuilder()
			->from(Article::class, 'a')
			->select('a')
			->where('a.heading = :heading')
			->andWhere('a.content = :content')
			->andWhere('a.picture IS NOT null')
			->setParameter('heading', '')
			->setParameter('content', '')
			->setMaxResults($count)
			->getQuery()
			->getResult();


		if (count($articles) == 0) {
			$output->writeln('All articles are generated.');
			return 0;
		}

		$categoryText = "";
		foreach (Article::CATEGORIES_NAMES_GPT as $key => $value) {
			$categoryText .= '"' . $value . '": ' . $key . ", ";
		}
		foreach ($articles as $article) {


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

			$result = json_decode($complete, true);
			$resultTextJson = $result['choices'][0]['message']['content'];
			$resultText = json_decode($resultTextJson, true);

			//if $resultText['heading'] is null, continue
			if ($resultText['heading'] == null) {
				continue;
			}


			$article->setHeading($resultText['heading']);
			$article->setContent($resultText['content']);
			$article->setCategoryId($resultText['categoryId']);
			$article->setStatus(Article::STATUS_PUBLISHED);
			$article->setUpdatedAt();

			$this->entityManager->persist($article);
			$this->entityManager->flush();


			$output->writeln('Article generated.');
		}


		return 0;
	}

}
