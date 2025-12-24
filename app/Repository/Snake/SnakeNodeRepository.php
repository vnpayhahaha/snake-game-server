<?php

declare(strict_types=1);

namespace App\Repository\Snake;

use App\Model\Snake\SnakeNode;
use App\Repository\IRepository;
use Hyperf\Database\Model\Builder;

class SnakeNodeRepository extends IRepository
{
    public function __construct(
        protected readonly SnakeNode $model
    ) {}

    public function handleSearch(Builder $query, array $params): Builder
    {
        // 可以在这里添加针对 snake_node 表的搜索条件
        return $query;
    }
}
