<template>
  <el-card header="用户管理 / 调账">
    <el-form inline @submit.prevent="load">
      <el-form-item><el-input v-model="keyword" placeholder="手机/昵称" /></el-form-item>
      <el-form-item><el-button type="warning" @click="load">搜索</el-button></el-form-item>
    </el-form>
    <el-table :data="list">
      <el-table-column prop="id" label="ID" width="70" />
      <el-table-column prop="phone" label="手机" />
      <el-table-column prop="nickname" label="昵称" />
      <el-table-column prop="balance" label="余额" />
      <el-table-column prop="role" label="角色" width="90" />
      <el-table-column label="操作" width="120">
        <template #default="{ row }">
          <el-button link type="warning" @click="credit(row)">调账</el-button>
        </template>
      </el-table-column>
    </el-table>
  </el-card>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { adminApi } from '@/api'
import { ElMessage, ElMessageBox } from 'element-plus'
const list = ref([])
const keyword = ref('')
async function load() {
  const res = await adminApi.users({ keyword: keyword.value })
  list.value = res.data.list || []
}
async function credit(row) {
  const { value } = await ElMessageBox.prompt('输入金额（正数充值/负数扣款）', `调账 - ${row.nickname}`, { inputValue: '100' })
  await adminApi.credit({ userId: row.id, amount: Number(value), remark: '后台调账' })
  ElMessage.success('调账成功')
  load()
}
onMounted(load)
</script>
