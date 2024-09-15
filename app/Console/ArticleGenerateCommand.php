<?php declare(strict_types = 1);

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
		$this->setDescription('Creates article from sourceContent.');
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
		$openAi = new OpenAi(getenv('CHAT_GPT_API_KEY'));

		#$list = $openAi->listModels();
		$model = 'gpt-4o-mini';

		$article = $this->entityManager->getArticleRepository()
			->findOneBy(
				[
					'heading' => '',
					'content' => '',
				]
			);

		if(!$article instanceof Article) {
			$output->writeln('All articles are generated.');
			return 0;
		}

		$complete = $openAi->chat([
			'model' => $model,
			'messages' => [
				[
					"role" => "user",
					"content" =>
						"Ahoj, dělám momentálně článek pro magazín se zajímavostmi a potřebuji vygenerovat
						jeho nadpis a obsah.  Poskytnu ti obsah na toto téma z Wikipedie a ty mi vrať odpověd
						jako JSON soubor, s klíči heading a content, json musi byt validni a nemel by obsahovat
						žádné anotace '```json {' a podobně.
						Pokud budeš vědět nějákou zajímavost, tak ji můžeš přidat do obsahu.
						Cílem je aby článek byl pro lidi zajímavý a přitažlivý.


						------
						" .$article->getSourceContent(),
				],
			],
		]);

		$result = json_decode($complete, true);
		$resultTextJson = $result['choices'][0]['message']['content'];
		$resultText = json_decode($resultTextJson, true);


		$article->setHeading($resultText['heading']);
		$article->setContent($resultText['content']);
		$article->setUpdatedAt();

		$this->entityManager->persist($article);
		$this->entityManager->flush();

		$output->writeln('Article generated.');

		return 0;
	}

}
