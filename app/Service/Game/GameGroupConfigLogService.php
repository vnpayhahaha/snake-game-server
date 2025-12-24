<?php
declare(strict_types=1);
/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://gitee.com/xmo/MineAdmin
 */

namespace App\Service\Game;

use App\Repository\Game\GameGroupConfigLogRepository;
use App\Service\IService;

/**
 * 游戏群组配置变更记录表服务类
 */
final class GameGroupConfigLogService extends IService
{
    public function __construct(
        protected readonly GameGroupConfigLogRepository $repository
    ) {}
}