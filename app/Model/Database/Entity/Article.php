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


	/** @ORM\Column(type="string", length=255, nullable=FALSE, unique=false) */
	private string $heading;

	/** @ORM\Column(type="text", nullable=FALSE, unique=false) */
	private string $content;

	/** @ORM\Column(type="text", nullable=FALSE, unique=TRUE) */
	private string $sourceContent;

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


}
