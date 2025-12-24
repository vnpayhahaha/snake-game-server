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

namespace App\Service\Player;

use App\Kernel\Wallet\Tron;
use App\Model\Player\PlayerWalletBinding;
use App\Repository\Player\PlayerWalletBindingRepository;
use App\Service\IService;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Di\Annotation\Inject;

/**
 * 玩家钱包绑定表服务类
 */
final class PlayerWalletBindingService extends IService
{
    #[Inject]
    private StdoutLoggerInterface $logger;

    public function __construct(
        protected readonly PlayerWalletBindingRepository $repository
    ) {}

    /**
     * 绑定钱包
     */
    public function bindWallet(int $groupId, int $tgUserId, string $tgUsername, string $firstName, string $lastName, string $walletAddress): array
    {
        try {
            // 验证钱包地址格式
            if (!Tron::isAddress($walletAddress)) {
                return ['❌ Invalid TRON wallet address format'];
            }

            // 检查是否已经绑定了其他钱包
            $existingBinding = $this->repository->query()
                ->where('group_id', $groupId)
                ->where('tg_user_id', $tgUserId)
                ->first();

            if ($existingBinding) {
                if ($existingBinding->wallet_address === $walletAddress) {
                    return ['ℹ️ This wallet address is already bound to your account'];
                }
                
                // 更新绑定
                $this->repository->update($existingBinding->id, [
                    'wallet_address' => $walletAddress,
                    'updated_at' => now(),
                ]);
                
                $this->logger->info('Updated wallet binding', [
                    'group_id' => $groupId,
                    'tg_user_id' => $tgUserId,
                    'old_address' => $existingBinding->wallet_address,
                    'new_address' => $walletAddress
                ]);
                
                return [
                    '✅ Wallet address updated successfully!',
                    sprintf('New address: %s', $walletAddress)
                ];
            }

            // 检查钱包地址是否已被其他用户绑定
            $addressBinding = $this->repository->query()
                ->where('group_id', $groupId)
                ->where('wallet_address', $walletAddress)
                ->first();

            if ($addressBinding) {
                return ['❌ This wallet address is already bound to another user'];
            }

            // 创建新绑定
            $this->repository->save([
                'group_id' => $groupId,
                'tg_user_id' => $tgUserId,
                'tg_username' => $tgUsername,
                'tg_first_name' => $firstName,
                'tg_last_name' => $lastName,
                'wallet_address' => $walletAddress,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->logger->info('Created new wallet binding', [
                'group_id' => $groupId,
                'tg_user_id' => $tgUserId,
                'wallet_address' => $walletAddress
            ]);

            return [
                '✅ Wallet address bound successfully!',
                sprintf('Address: %s', $walletAddress),
                '',
                '💡 You can now participate in the game by sending TRX to the group payment address'
            ];
        } catch (\Throwable $e) {
            $this->logger->error('Bind wallet failed', [
                'group_id' => $groupId,
                'tg_user_id' => $tgUserId,
                'wallet_address' => $walletAddress,
                'error' => $e->getMessage()
            ]);
            return ['❌ Failed to bind wallet address, please try again later'];
        }
    }

    /**
     * 解绑钱包
     */
    public function unbindWallet(int $groupId, int $tgUserId): array
    {
        try {
            $binding = $this->repository->query()
                ->where('group_id', $groupId)
                ->where('tg_user_id', $tgUserId)
                ->first();

            if (!$binding) {
                return ['ℹ️ No wallet address bound to your account in this group'];
            }

            $this->repository->delete($binding->id);

            $this->logger->info('Unbound wallet', [
                'group_id' => $groupId,
                'tg_user_id' => $tgUserId,
                'wallet_address' => $binding->wallet_address
            ]);

            return [
                '✅ Wallet address unbound successfully!',
                sprintf('Unbound address: %s', $binding->wallet_address)
            ];
        } catch (\Throwable $e) {
            $this->logger->error('Unbind wallet failed', [
                'group_id' => $groupId,
                'tg_user_id' => $tgUserId,
                'error' => $e->getMessage()
            ]);
            return ['❌ Failed to unbind wallet address, please try again later'];
        }
    }

    /**
     * 查看我的钱包
     */
    public function getMyWallet(int $groupId, int $tgUserId): array
    {
        try {
            $binding = $this->repository->query()
                ->where('group_id', $groupId)
                ->where('tg_user_id', $tgUserId)
                ->first();

            if (!$binding) {
                return [
                    'ℹ️ No wallet address bound to your account',
                    '',
                    'Use /bindwallet <address> to bind your TRON wallet'
                ];
            }

            return [
                '💰 Your Wallet Information:',
                '--------------------',
                sprintf('Address: %s', $binding->wallet_address),
                sprintf('Bound at: %s', $binding->created_at->format('Y-m-d H:i:s')),
                '',
                '💡 You can use /unbindwallet to unbind this address'
            ];
        } catch (\Throwable $e) {
            $this->logger->error('Get my wallet failed', [
                'group_id' => $groupId,
                'tg_user_id' => $tgUserId,
                'error' => $e->getMessage()
            ]);
            return ['❌ Failed to query wallet information, please try again later'];
        }
    }
}