<template>
  <el-card header="提现审核">
    <el-table :data="list">
      <el-table-column label="用户"><template #default="{row}">{{ row.user?.nickname }} {{ row.user?.phone }}</template></el-table-column>
      <el-table-column prop="amount" label="金额" width="100" />
      <el-table-column prop="fee" label="手续费" width="90" />
      <el-table-column prop="actualAmount" label="到账" width="100" />
      <el-table-column prop="status" label="状态" width="80" />
      <el-table-column label="操作" width="180">
        <template #default="{ row }">
          <template v-if="row.status===0">
            <el-button link type="success" @click="proc(row,1)">通过</el-button>
            <el-button link type="danger" @click="proc(row,2)">拒绝</el-button>
          </template>
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
async function load() { const res = await adminApi.withdrawals(); list.value = res.data.list || [] }
async function proc(row, status) {
  await adminApi.processWithdrawal(row.id, { status, remark: status===1?'已打款':'不符合条件' })
  ElMessage.success('已处理')
  load()
}
onMounted(load)
</script>
