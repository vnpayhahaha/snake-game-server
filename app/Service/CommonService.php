<?php

namespace App\Service;

class CommonService extends IService
{

    public function selectOption(string $table_name, string $list_name): array
    {
        $configData = config('constants', []);
        $constants = array_keys($configData);
        if (in_array($table_name, $constants, true)) {
            $constantClass = $configData[$table_name] ?? null;
            if (is_null($constantClass)) {
                return [];
            }
            if (!property_exists($constantClass, $list_name)) {
                return [];
            }
            try {
                $listArr = $constantClass::${$list_name};
                $list = $constantClass::getOptionMap($listArr);
            } catch (\Throwable $e) {
                return [];
            }
            return $list;
        }
        return [];
    }
}