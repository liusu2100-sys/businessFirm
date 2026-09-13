<template>
  <el-card header="我的出售">
    <el-table :data="list" v-loading="loading">
      <el-table-column prop="orderNo" label="订单号" min-width="160" />
      <el-table-column label="账号"><template #default="{ row }">{{ row.gameAccount?.accountNo }}</template></el-table-column>
      <el-table-column prop="price" label="租金" width="90" />
      <el-table-column label="买家"><template #default="{ row }">{{ row.buyer?.nickname }}</template></el-table-column>
      <el-table-column label="状态" width="90">
        <template #default="{ row }"><el-tag size="small">{{ ({0:'待支付',1:'交易中',3:'已取消',4:'已退款',5:'已完成'}[row.status]) }}</el-tag></template>
      </el-table-column>
      <el-table-column label="操作" width="140">
        <template #default="{ row }">
          <el-button link type="success" v-if="row.status===1" @click="complete(row)">确认完成</el-button>
          <el-button link type="warning" v-if="row.status===1" @click="settle(row)">提前结算</el-button>
        </template>
      </el-table-column>
    </el-table>
  </el-card>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { orderApi } from '@/api'
import { useUserStore } from '@/stores/user'
import { ElMessage } from 'element-plus'
const userStore = useUserStore()
const list = ref([])
const loading = ref(false)
async function load() {
  loading.value = true
  try { const res = await orderApi.list({ type: 'sell' }); list.value = res.data.list || [] }
  finally { loading.value = false }
}
async function complete(row) { await orderApi.complete(row.id); ElMessage.success('已完成'); await userStore.refreshProfile(); load() }
async function settle(row) { await orderApi.earlySettle(row.id); ElMessage.success('已结算'); await userStore.refreshProfile(); load() }
onMounted(load)
</script>
