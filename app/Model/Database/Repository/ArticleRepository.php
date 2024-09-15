<?php declare(strict_types=1);

namespace App\Model\Database\Repository;

use App\Model\Database\Entity\Article;

/**
 * @method Article|NULL find($id, ?int $lockMode = null, ?int $lockVersion = null)
 * @method Article|NULL findOneBy(array $criteria, array $orderBy = null)
 * @method Article[] findAll()
 * @method Article[] findBy(array $criteria, array $orderBy = null, ?int $limit = null, ?int $offset = null)
 * @extends AbstractRepository<Article>
 */
class ArticleRepository extends AbstractRepository
{

}
