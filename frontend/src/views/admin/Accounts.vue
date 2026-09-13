<template>
  <el-card header="账号审核">
    <el-radio-group v-model="status" @change="load" style="margin-bottom:12px">
      <el-radio-button label="">全部</el-radio-button>
      <el-radio-button :label="0">待审</el-radio-button>
      <el-radio-button :label="1">上架</el-radio-button>
      <el-radio-button :label="2">下架</el-radio-button>
    </el-radio-group>
    <el-table :data="list">
      <el-table-column prop="id" label="ID" width="70" />
      <el-table-column prop="accountNo" label="编号" />
      <el-table-column prop="price" label="租金" width="90" />
      <el-table-column prop="sellerNickname" label="卖家" />
      <el-table-column prop="status" label="状态" width="80" />
      <el-table-column label="操作" width="180">
        <template #default="{ row }">
          <el-button link type="success" v-if="row.status!==1" @click="audit(row,1)">通过上架</el-button>
          <el-button link type="danger" @click="audit(row,2)">下架/拒绝</el-button>
        </template>
      </el-table-column>
    </el-table>
  </el-card>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { adminApi } from '@/api'
import { ElMessage } from 'element-plus'
const list = ref([])
const status = ref(0)
async function load() {
  const params = {}
  if (status.value !== '') params.status = status.value
  const res = await adminApi.accounts(params)
  list.value = res.data.list || []
}
async function audit(row, s) {
  await adminApi.auditAccount(row.id, s)
  ElMessage.success('已处理')
  load()
}
onMounted(load)
</script>
