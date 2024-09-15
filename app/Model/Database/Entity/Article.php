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



	/** @ORM\Column(type="string", length=255, nullable=FALSE, unique=false) */
	private string $heading;

	/** @ORM\Column(type="text", nullable=FALSE, unique=false) */
	private string $content;

	/** @ORM\Column(type="text", nullable=FALSE, unique=false) */
	private string $sourceContent;

	/** @ORM\Column(type="integer", nullable=FALSE, unique=false, options={"default":0}) */
	private int $status = 0;

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

}
