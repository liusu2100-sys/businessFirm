<template>
  <el-card>
    <template #header>
      <div style="display:flex;justify-content:space-between;align-items:center">
        <span>我的账号</span>
        <el-button type="warning" size="small" @click="$router.push('/publish')">发布</el-button>
      </div>
    </template>
    <el-table :data="list" v-loading="loading">
      <el-table-column prop="accountNo" label="编号" width="100" />
      <el-table-column prop="loginType" label="登录" width="80" />
      <el-table-column prop="price" label="租金" width="90" />
      <el-table-column prop="havCoin" label="哈夫币" />
      <el-table-column label="状态" width="90">
        <template #default="{ row }">
          <el-tag :type="statusType(row.status)" size="small">{{ statusText(row.status) }}</el-tag>
        </template>
      </el-table-column>
      <el-table-column label="操作" width="160">
        <template #default="{ row }">
          <el-button link type="warning" @click="$router.push(`/product/${row.id}`)">查看</el-button>
          <el-button link type="danger" v-if="row.status===1" @click="off(row)">下架</el-button>
        </template>
      </el-table-column>
    </el-table>
  </el-card>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { accountApi } from '@/api'
import { ElMessage } from 'element-plus'
const list = ref([])
const loading = ref(false)
const statusText = (s) => ({0:'待审',1:'上架',2:'下架'}[s] ?? s)
const statusType = (s) => ({0:'info',1:'success',2:'danger'}[s] ?? 'info')
async function load() {
  loading.value = true
  try {
    const res = await accountApi.myList()
    list.value = res.data.list || []
  } finally { loading.value = false }
}
async function off(row) {
  await accountApi.offShelf(row.id)
  ElMessage.success('已下架')
  load()
}
onMounted(load)
</script>
