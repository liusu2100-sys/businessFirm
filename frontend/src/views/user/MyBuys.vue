<template>
  <el-card header="我的购买">
    <el-table :data="list" v-loading="loading">
      <el-table-column prop="orderNo" label="订单号" min-width="160" />
      <el-table-column label="账号" min-width="100">
        <template #default="{ row }">{{ row.gameAccount?.accountNo }}</template>
      </el-table-column>
      <el-table-column prop="totalAmount" label="支付金额" width="100" />
      <el-table-column label="状态" width="90">
        <template #default="{ row }"><el-tag size="small">{{ statusText(row.status) }}</el-tag></template>
      </el-table-column>
      <el-table-column label="操作" width="240">
        <template #default="{ row }">
          <el-button link type="warning" v-if="row.status===0" @click="pay(row)">支付</el-button>
          <el-button link type="success" v-if="row.status===1" @click="complete(row)">确认完成</el-button>
          <el-button link type="danger" v-if="row.status===0||row.status===1" @click="cancel(row)">取消</el-button>
          <el-button link v-if="row.status>=1 && row.status!==3" @click="showPwd(row)">查看密码</el-button>
        </template>
      </el-table-column>
    </el-table>
  </el-card>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { orderApi } from '@/api'
import { useUserStore } from '@/stores/user'
import { ElMessage, ElMessageBox } from 'element-plus'
const userStore = useUserStore()
const list = ref([])
const loading = ref(false)
const statusText = (s) => ({0:'待支付',1:'交易中',3:'已取消',4:'已退款',5:'已完成'}[s] ?? s)
async function load() {
  loading.value = true
  try { const res = await orderApi.list({ type: 'buy' }); list.value = res.data.list || [] }
  finally { loading.value = false }
}
async function pay(row) { await orderApi.pay(row.id); ElMessage.success('支付成功'); await userStore.refreshProfile(); load() }
async function complete(row) { await orderApi.complete(row.id); ElMessage.success('已完成'); await userStore.refreshProfile(); load() }
async function cancel(row) { await orderApi.cancel(row.id); ElMessage.success('已取消'); await userStore.refreshProfile(); load() }
function showPwd(row) {
  ElMessageBox.alert(`账号：${row.gameAccount?.accountNo || '-'}\n密码：${row.gameAccount?.accountPwd || '（交易后可见）'}`, '账号信息')
}
onMounted(load)
</script>
