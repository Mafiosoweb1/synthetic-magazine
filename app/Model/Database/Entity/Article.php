<?php declare(strict_types=1);

namespace App\Model\Database\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Model\Database\Repository\ArticleRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Article extends AbstractEntity
{

	use TId;
	use TCreatedAt;
	use TUpdatedAt;

	public const STATUS_CONCEPT = 0;
	public const STATUS_CANCEL = 8;
	public const STATUS_PUBLISHED = 9;

	public const CATEGORY_NATURE = 1;
	public const CATEGORY_TECHNOLOGY = 2;
	public const CATEGORY_SPACE = 3;
	public const CATEGORY_HISTORY = 4;
	public const CATEGORY_PEOPLE = 5;
	public const CATEGORY_CULTURE = 6;
	public const CATEGORY_SPORT = 7;
	public const CATEGORY_OTHER = 8;

	public const CATEGORIES_NAMES_GPT = [
		self::CATEGORY_NATURE => 'Příroda, životní prostředí, ekologie, zvířata, rostliny',
		self::CATEGORY_TECHNOLOGY => 'Technologie, věda, vynálezy, technické obory',
		self::CATEGORY_SPACE => 'Vesmír, astronomie, astrofyzika, kosmonautika',
		self::CATEGORY_PEOPLE => 'Lidé, osobnosti, osobnosti historie',
		self::CATEGORY_CULTURE => 'Kultura, umění, literatura, hudba, film, divadlo',
		self::CATEGORY_SPORT => 'Sport, sportovci, sportovní události',
		self::CATEGORY_HISTORY => 'Historie, události, historické období',
		self::CATEGORY_OTHER => 'Ostatní, nezařaditelné, náhodné, zeměpisné pojmy',
	];

	public const CATEGORIES_NAMES = [
		self::CATEGORY_NATURE => 'Příroda',
		self::CATEGORY_TECHNOLOGY => 'Technologie',
		self::CATEGORY_SPACE => 'Vesmír',
		self::CATEGORY_HISTORY => 'Historie',
		self::CATEGORY_PEOPLE => 'Lidé',
		self::CATEGORY_CULTURE => 'Kultura',
		self::CATEGORY_SPORT => 'Sport',
		self::CATEGORY_OTHER => 'Ostatní',
	];

	public const CATEGORIES_COLORS = [
		self::CATEGORY_NATURE => 'green',
		self::CATEGORY_TECHNOLOGY => 'blue',
		self::CATEGORY_SPACE => 'purple',
		self::CATEGORY_HISTORY => 'orange',
		self::CATEGORY_PEOPLE => 'red',
		self::CATEGORY_CULTURE => 'yellow',
		self::CATEGORY_SPORT => 'pink',
		self::CATEGORY_OTHER => 'grey',
	];

	public const CATEGORIES_ICONS = [
		self::CATEGORY_NATURE => 'leaf',
		self::CATEGORY_TECHNOLOGY => 'cogs',
		self::CATEGORY_SPACE => 'satellite-dish',
		self::CATEGORY_HISTORY => 'landmark',
		self::CATEGORY_PEOPLE => 'user',
		self::CATEGORY_CULTURE => 'palette',
		self::CATEGORY_SPORT => 'futbol',
		self::CATEGORY_OTHER => 'question',
	];

	public const CATEGORIES_ENABLED = [
		self::CATEGORY_NATURE,
		self::CATEGORY_TECHNOLOGY,
		self::CATEGORY_SPACE,
		self::CATEGORY_HISTORY,
		self::CATEGORY_PEOPLE,
		self::CATEGORY_CULTURE,
	];

	/** @ORM\Column(type="integer", unique=true) */
	private int $wikiId;

	/** @ORM\Column(type="integer", unique=false, nullable=TRUE) */
	private ?int $categoryId=null;

	/** @ORM\Column(type="string", length=255, nullable=FALSE, unique=false) */
	private string $heading;

	/** @ORM\Column(type="text", nullable=FALSE, unique=false) */
	private string $content;


	/** @ORM\Column(type="string", length=255, nullable=FALSE, unique=false) */
	private string $sourceHeading;

	/** @ORM\Column(type="text", nullable=FALSE, unique=false) */
	private string $sourceContent;

	/** @ORM\Column(type="string", nullable=TRUE, unique=false) */
	private ?string $picture=null;

	/** @ORM\Column(type="integer", nullable=FALSE, unique=false, options={"default":0}) */
	private int $status = 0;

	public function getWikiId(): int
	{
		return $this->wikiId;
	}
	public function getWikiUrl(): string
	{
		return 'https://cs.wikipedia.org/?curid=' . $this->wikiId;
	}

	public function setWikiId(int $wikiId): Article
	{
		$this->wikiId = $wikiId;
		return $this;
	}

	public function getCategoryId(): ?int
	{
		return $this->categoryId;
	}

	public function setCategoryId(?int $categoryId): Article
	{
		$this->categoryId = $categoryId;
		return $this;
	}





	public function getHeading(): string
	{
		return $this->heading;
	}

	public function setHeading(string $heading): Article
	{
		$this->heading = $heading;
		return $this;
	}

	public function getContent(): string
	{
		return $this->content;
	}

	public function setContent(string $content): Article
	{
		$this->content = $content;
		return $this;
	}

	public function getSourceHeading(): string
	{
		return $this->sourceHeading;
	}

	public function setSourceHeading(string $sourceHeading): Article
	{
		$this->sourceHeading = $sourceHeading;
		return $this;
	}



	public function getSourceContent(): string
	{
		return $this->sourceContent;
	}

	public function setSourceContent(string $sourceContent): Article
	{
		$this->sourceContent = $sourceContent;
		return $this;
	}

	public function getStatus(): int
	{
		return $this->status;
	}

	public function setStatus(int $status): Article
	{
		$this->status = $status;
		return $this;
	}

	public function getPicture(): ?string
	{
		return $this->picture;
	}

	public function setPicture(?string $picture): Article
	{
		$this->picture = $picture;
		return $this;
	}





}
