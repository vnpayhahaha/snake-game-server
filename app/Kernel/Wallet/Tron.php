<?php

declare(strict_types=1);

namespace App\Kernel\Wallet;

use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Di\Annotation\Inject;
use Throwable;

class Tron
{
    #[Inject]
    private StdoutLoggerInterface $logger;

    /**
     * 从TRON网络获取指定地址的新交易
     * 这里只是一个骨架，实际需要调用TronGrid或TRON节点API
     *
     * @param string $address 钱包地址
     * @param int $startBlockHeight 起始区块高度
     * @return array 交易列表，每项包含tx_hash, from_address, to_address, amount, status, type, block_height等
     */
    public function getTransactionsByAddress(string $address, int $startBlockHeight): array
    {
        $this->logger->info(sprintf('Calling TronGrid API for address %s from block %d.', $address, $startBlockHeight));

        $client = new \GuzzleHttp\Client([
            'base_uri' => 'https://api.trongrid.io',
            'timeout' => 10.0,
        ]);

        try {
            $response = $client->get("/v1/accounts/{$address}/transactions", [
                'query' => [
                    'only_to' => 'true',
                    'min_block_timestamp' => $startBlockHeight > 0 ? $startBlockHeight * 1000 : 0, // 使用时间戳
                    'limit' => 50,
                    'order_by' => 'block_timestamp,asc',
                ],
                'headers' => [
                    'Accept' => 'application/json',
                    'TRON-PRO-API-KEY' => env('TRONGRID_API_KEY', ''), // 从 .env 文件获取 API Key
                ],
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            if (empty($body['data'])) {
                return [];
            }

            $transactions = [];
            foreach ($body['data'] as $item) {
                // 确保是 TRX 转账
                if (
                    isset($item['raw_data']['contract'][0]['type']) &&
                    $item['raw_data']['contract'][0]['type'] === 'TransferContract'
                ) {
                    $contractData = $item['raw_data']['contract'][0]['parameter']['value'];
                    $transactions[] = [
                        'tx_hash' => $item['txID'],
                        'from_address' => $this->base58checkDecode($contractData['owner_address']),
                        'to_address' => $this->base58checkDecode($contractData['to_address']),
                        'amount' => $contractData['amount'],
                        'status' => $item['ret'][0]['contractRet'] ?? 'FAILED',
                        'type' => 'TransferContract',
                        'block_height' => $item['blockNumber'],
                        'block_timestamp' => $item['block_timestamp'],
                    ];
                }
            }
            return $transactions;
        } catch (Throwable $e) {
            $this->logger->error('Failed to get transactions from TronGrid API: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * 获取钱包余额
     * @param string $address
     * @return string 余额（TRX）
     */
    public function getBalance(string $address): string
    {
        $client = new \GuzzleHttp\Client([
            'base_uri' => 'https://api.trongrid.io',
            'timeout' => 5.0,
        ]);

        try {
            $response = $client->get("/v1/accounts/{$address}", [
                'headers' => [
                    'Accept' => 'application/json',
                    'TRON-PRO-API-KEY' => env('TRONGRID_API_KEY', ''),
                ],
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            if (isset($body['data'][0]['balance'])) {
                // TRON API 返回的余额单位是 sun (1 TRX = 1,000,000 sun)
                $balanceInSun = $body['data'][0]['balance'];
                return bcdiv((string)$balanceInSun, '1000000', 6);
            }

            return '0.000000';
        } catch (Throwable $e) {
            $this->logger->error(sprintf('Failed to get balance for address %s: %s', $address, $e->getMessage()));
            return '0.000000';
        }
    }

    /**
     * 使用私钥发送TRON交易
     * @param string $privateKey 私钥（加密存储的需要先解密）
     * @param string $fromAddress 发送地址
     * @param string $toAddress 接收地址
     * @param string $amount 金额（TRX）
     * @return string|null 交易哈希
     */
    public function sendTransactionWithPrivateKey(string $privateKey, string $fromAddress, string $toAddress, string $amount): ?string
    {
        $this->logger->info(sprintf('Sending %s TRX from %s to %s.', $amount, $fromAddress, $toAddress));

        // 解密私钥（如果是加密存储的）
        $decryptedPrivateKey = $this->decryptPrivateKey($privateKey);

        try {
            // 这里需要使用真正的TRON PHP SDK
            // 推荐使用 iexbase/tron-api 或类似的库
            
            // 示例代码（需要安装相应的TRON SDK）:
            /*
            $tron = new \IEXBase\TronAPI\Tron();
            $tron->setPrivateKey($decryptedPrivateKey);
            $tron->setAddress($fromAddress);
            
            // 将TRX转换为sun
            $amountInSun = bcmul($amount, '1000000', 0);
            
            $transaction = $tron->sendTransaction($toAddress, (float)$amountInSun);
            
            if (isset($transaction['txid'])) {
                return $transaction['txid'];
            }
            */

            // 临时模拟实现（生产环境需要替换为真实的TRON SDK调用）
            if ($this->validateTransactionParams($fromAddress, $toAddress, $amount)) {
                // 模拟交易哈希
                $mockTxHash = 'mock_tx_' . uniqid() . '_' . time();
                $this->logger->info(sprintf('Mock transaction created: %s', $mockTxHash));
                return $mockTxHash;
            }

            return null;
        } catch (Throwable $e) {
            $this->logger->error(sprintf('Failed to send transaction: %s', $e->getMessage()));
            throw $e;
        }
    }

    /**
     * 验证交易参数
     */
    private function validateTransactionParams(string $fromAddress, string $toAddress, string $amount): bool
    {
        if (!self::isAddress($fromAddress) || !self::isAddress($toAddress)) {
            throw new \InvalidArgumentException('Invalid TRON address format');
        }

        if (bccomp($amount, '0', 6) <= 0) {
            throw new \InvalidArgumentException('Amount must be greater than 0');
        }

        return true;
    }

    /**
     * 解密私钥
     * @param string $encryptedPrivateKey
     * @return string
     */
    private function decryptPrivateKey(string $encryptedPrivateKey): string
    {
        // 如果私钥是加密存储的，在这里解密
        // 这里假设使用简单的base64编码，实际应该使用更安全的加密方式
        
        // 检查是否是加密的（简单判断）
        if (strlen($encryptedPrivateKey) === 64 && ctype_xdigit($encryptedPrivateKey)) {
            // 看起来像是原始私钥（64位十六进制）
            return $encryptedPrivateKey;
        }

        // 尝试base64解码
        $decoded = base64_decode($encryptedPrivateKey, true);
        if ($decoded !== false && strlen($decoded) === 32) {
            return bin2hex($decoded);
        }

        // 如果都不是，可能需要其他解密方式
        // 这里应该根据实际的加密方案来实现
        throw new \InvalidArgumentException('Unable to decrypt private key');
    }

    /**
     * 加密私钥（用于存储到数据库）
     * @param string $privateKey
     * @return string
     */
    public static function encryptPrivateKey(string $privateKey): string
    {
        // 简单的base64编码，实际应该使用更安全的加密方式
        // 推荐使用 Laravel 的 Crypt 或其他加密库
        return base64_encode(hex2bin($privateKey));
    }

    /**
     * Helper function to decode TRON base58check address
     * (This is a simplified version, a dedicated library is recommended for production)
     */
    private function base58checkDecode(string $hexAddress): string
    {
        if (strlen($hexAddress) === 34 && str_starts_with($hexAddress, 'T')) {
            return $hexAddress; // Already a base58 address
        }
        // This is a placeholder for actual hex to base58 conversion
        // In a real application, use a library like 'iexbase/tron-api'
        return $hexAddress;
    }

    /**
     * 验证是否为有效的TRON地址
     * @param string $address
     * @return bool
     */
    public static function isAddress(string $address): bool
    {
        // TRON地址：以T开头，34位字符
        return preg_match('/^T[1-9A-HJ-NP-Za-km-z]{33}$/', $address) === 1;
    }

    /**
     * 发送TRON交易（兼容旧接口）
     * @param string $toAddress
     * @param string $amount
     * @return string|null
     * @deprecated 使用 sendTransactionWithPrivateKey 替代
     */
    public function sendTransaction(string $toAddress, string $amount): ?string
    {
        $this->logger->warning('Using deprecated sendTransaction method. Please use sendTransactionWithPrivateKey instead.');
        
        // 这个方法保留是为了兼容性，但不推荐使用
        // 因为它无法指定发送地址和私钥
        return 'deprecated_mock_tx_' . uniqid();
    }

    /**
     * 验证交易是否已确认
     * @param string $txHash
     * @return array ['confirmed' => bool, 'confirmations' => int, 'status' => string]
     */
    public function getTransactionStatus(string $txHash): array
    {
        $client = new \GuzzleHttp\Client([
            'base_uri' => 'https://api.trongrid.io',
            'timeout' => 5.0,
        ]);

        try {
            $response = $client->get("/v1/transactions/{$txHash}", [
                'headers' => [
                    'Accept' => 'application/json',
                    'TRON-PRO-API-KEY' => env('TRONGRID_API_KEY', ''),
                ],
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            if (isset($body['ret'][0]['contractRet'])) {
                $status = $body['ret'][0]['contractRet'];
                $blockNumber = $body['blockNumber'] ?? 0;
                
                // 获取当前最新区块高度来计算确认数
                $latestBlock = $this->getLatestBlockNumber();
                $confirmations = $latestBlock > $blockNumber ? $latestBlock - $blockNumber : 0;

                return [
                    'confirmed' => $confirmations >= 19, // TRON建议19个确认
                    'confirmations' => $confirmations,
                    'status' => $status,
                    'block_number' => $blockNumber
                ];
            }

            return [
                'confirmed' => false,
                'confirmations' => 0,
                'status' => 'NOT_FOUND',
                'block_number' => 0
            ];
        } catch (Throwable $e) {
            $this->logger->error(sprintf('Failed to get transaction status for %s: %s', $txHash, $e->getMessage()));
            return [
                'confirmed' => false,
                'confirmations' => 0,
                'status' => 'ERROR',
                'block_number' => 0
            ];
        }
    }

    /**
     * 获取最新区块高度
     */
    private function getLatestBlockNumber(): int
    {
        $client = new \GuzzleHttp\Client([
            'base_uri' => 'https://api.trongrid.io',
            'timeout' => 5.0,
        ]);

        try {
            $response = $client->get('/wallet/getnowblock', [
                'headers' => [
                    'Accept' => 'application/json',
                    'TRON-PRO-API-KEY' => env('TRONGRID_API_KEY', ''),
                ],
            ]);

            $body = json_decode($response->getBody()->getContents(), true);
            return $body['block_header']['raw_data']['number'] ?? 0;
        } catch (Throwable $e) {
            $this->logger->error(sprintf('Failed to get latest block number: %s', $e->getMessage()));
            return 0;
        }
    }
}
