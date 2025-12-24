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

namespace App\Service\Prize;

use App\Repository\Prize\PrizeRecordRepository;
use App\Service\IService;

/**
 * 中奖记录表服务类
 */
final class PrizeRecordService extends IService
{
    public function __construct(
        protected readonly PrizeRecordRepository $repository
    ) {}
}