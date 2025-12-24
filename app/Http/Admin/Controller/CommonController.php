<?php

declare(strict_types=1);
/**
 * This file is part of MineAdmin.
 *
 * @link     https://www.mineadmin.com
 * @document https://doc.mineadmin.com
 * @contact  root@imoi.cn
 * @license  https://github.com/mineadmin/MineAdmin/blob/master/LICENSE
 */

namespace App\Http\Admin\Controller;

use App\Http\Admin\Request\CommonRequest;
use App\Http\Common\Result;
use App\Service\CommonService;
use Hyperf\Swagger\Annotation\Get;
use Hyperf\Swagger\Annotation\HyperfServer;
use Mine\Support\Request;
use Mine\Swagger\Attributes\ResultResponse;
use Plugin\MineAdmin\Tenant\Annotation\TenantIgnore;
use Plugin\MineAdmin\Tenant\Service\TenantService;

/**
 * 公共接口控制器
 * 不需要认证的公共接口
 */
#[HyperfServer(name: 'http')]
final class CommonController extends AbstractController
{
    public function __construct(
        private readonly CommonService $commonService,
        private readonly TenantService $tenantService,
    ) {}

    /**
     * 获取下拉选项
     * 根据表名和列表名获取对应的下拉选项数据
     */
    #[Get(
        path: '/admin/common/selectOption',
        operationId: 'CommonSelectOption',
        summary: '获取下拉选项',
        tags: ['公共接口']
    )]
    #[ResultResponse(instance: new Result())]
    public function selectOption(CommonRequest $request): Result
    {
        $tableName = $request->input('table_name');
        $listName = $request->input('list_name');
        $data = $this->commonService->selectOption(strtolower($tableName), strtolower($listName));

        return $this->success($data);
    }

    #[Get('/admin/tenant_dict/remote')]
    #[ResultResponse(instance: new Result())]
    public function remote(Request $request): Result
    {
        $fields = [
            'id',
            'name',
            'bind_domain',
        ];
        return $this->success(
            $this->tenantService->getList($request->all())->map(static fn($model) => $model->only($fields))
        );
    }


}