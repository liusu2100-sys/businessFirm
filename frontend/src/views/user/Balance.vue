<template>
  <el-card header="余额明细">
    <el-table :data="list" v-loading="loading">
      <el-table-column prop="createdAt" label="时间" width="170" />
      <el-table-column prop="type" label="类型" width="120" />
      <el-table-column label="金额" width="120">
        <template #default="{ row }">
          <span :style="{color: row.amount>=0?'#67c23a':'#f56c6c'}">{{ row.amount }}</span>
        </template>
      </el-table-column>
      <el-table-column prop="balanceAfter" label="余额" width="120" />
      <el-table-column prop="remark" label="备注" />
    </el-table>
  </el-card>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { userApi } from '@/api'
const list = ref([])
const loading = ref(false)
onMounted(async () => {
  loading.value = true
  try { const res = await userApi.balanceLog(); list.value = res.data.list || [] }
  finally { loading.value = false }
})
</script>
