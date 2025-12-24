<script setup>
const form = inject("settingComponentForm")
const row = inject("settingComponentRow")
const add = () => {
  form.value.collection.push({ label: '', value: ''})
}
const del = (idx) => {
  form.value.collection.splice(idx, 1)
}
</script>

<template>
  <div>
    <el-form-item label="是否多选" prop="multiple" label-width="80px" v-if="row.view_type == 'select'">
      <el-radio-group v-model="form.multiple">
        <el-radio :value="true">是</el-radio>
        <el-radio :value="false">否</el-radio>
      </el-radio-group>
    </el-form-item>
    <el-form-item label="数据来源" prop="sourceType" label-width="80px">
      <el-radio-group v-model="form.sourceType">
        <el-radio value="dict">字典</el-radio>
        <el-radio value="api">接口</el-radio>
      </el-radio-group>
    </el-form-item>
    <div v-if="form.sourceType === 'dict'">
      <el-divider>设置数据 <el-link class="ml-2" @click="add"><i class="el-icon-plus" /> 添加</el-link></el-divider>
      <el-card v-for="(item, idx) in form.collection" :key="idx" class="mt-2 relative">
        <el-row justify="end" class="mt-2 mb-2">
          <el-link type="danger" @click="del(idx)" class="setdata-button" icon="el-icon-delete">删除</el-link>
        </el-row>
        <el-form-item label="label" prop="label" label-width="80px">
          <el-input v-model="item.label" />
        </el-form-item>
        <el-form-item label="value" prop="value" label-width="80px">
          <el-input v-model="item.value" />
        </el-form-item>
      </el-card>
    </div>
    <div v-else-if="form.sourceType === 'api'">
      <el-card class="mt-2 relative">
        <el-form-item label="选择表：">
          <el-select
            v-model="form.api_table"
            placeholder="请选择表"
            @change="changeApiTable()"
            size="large"
            style="width: 240px"
          >
            <el-option
              v-for="item in easyGeneratorList"
              :key="item.id"
              :label="item.id + '_' + item.table_name + '_' + item.table_comment "
              :value="item.id"
            />
          </el-select>
        </el-form-item>
        <el-card class="mt-2 relative">
          <el-form-item label="显示字段(label)：">
            <el-select
              v-model="form.api_label"
              placeholder="请选择选择值字段(value)"
              size="large"
              style="width: 240px"
            >
              <el-option
                v-for="item in easyGeneratorFieldList"
                :key="item.id"
                :label="item.column_name + (item.column_comment == '' ?  '': '_' + item.column_comment)"
                :value="item.id"
              />
            </el-select>
          </el-form-item>
          <el-form-item label="值字段(value)：">
            <el-select
              v-model="form.api_value"
              placeholder="请选择值字段(value)"
              size="large"
              style="width: 240px"
            >
              <el-option
                v-for="item in easyGeneratorFieldList"
                :key="item.id"
                :label="item.column_name + (item.column_comment == '' ?  '': '_' + item.column_comment)"
                :value="item.id"
              />
            </el-select>
          </el-form-item>

        </el-card>

      </el-card>
    </div>
  </div>
</template>

<style scoped>

</style>
