<template>
  <el-card header="提现">
    <el-form label-width="100px" style="max-width:480px" @submit.prevent="submit">
      <el-form-item label="当前余额"><b>¥{{ userStore.user?.balance }}</b></el-form-item>
      <el-form-item label="收款账户">
        <el-select v-model="paymentAccountId" style="width:100%" placeholder="请选择">
          <el-option v-for="p in accounts" :key="p.id" :label="`${p.accountName} ${p.accountNo}`" :value="p.id" />
        </el-select>
      </el-form-item>
      <el-form-item label="提现金额"><el-input-number v-model="amount" :min="1" :precision="2" style="width:100%" /></el-form-item>
      <el-form-item label="备注"><el-input v-model="remark" /></el-form-item>
      <el-form-item><el-button type="warning" native-type="submit" :loading="loading">提交申请</el-button></el-form-item>
    </el-form>
    <el-divider />
    <h4>提现记录</h4>
    <el-table :data="list">
      <el-table-column prop="createdAt" label="时间" width="170" />
      <el-table-column prop="amount" label="金额" width="100" />
      <el-table-column prop="fee" label="手续费" width="90" />
      <el-table-column prop="actualAmount" label="到账" width="100" />
      <el-table-column label="状态" width="100">
        <template #default="{ row }">{{ ({0:'待审',1:'已打款',2:'已拒绝'}[row.status]) }}</template>
      </el-table-column>
    </el-table>
  </el-card>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { userApi, paymentApi } from '@/api'
import { useUserStore } from '@/stores/user'
import { ElMessage } from 'element-plus'
const userStore = useUserStore()
const accounts = ref([])
const list = ref([])
const paymentAccountId = ref(null)
const amount = ref(10)
const remark = ref('')
const loading = ref(false)
async function load() {
  const [a, w] = await Promise.all([paymentApi.list(), userApi.withdrawalList()])
  accounts.value = a.data || []
  list.value = w.data.list || []
  const def = accounts.value.find(x => x.isDefault) || accounts.value[0]
  paymentAccountId.value = def?.id || null
}
async function submit() {
  loading.value = true
  try {
    await userApi.withdrawal({ amount: amount.value, paymentAccountId: paymentAccountId.value, remark: remark.value })
    ElMessage.success('已提交')
    await userStore.refreshProfile()
    load()
  } finally { loading.value = false }
}
onMounted(load)
</script>
