<template>
  <el-card header="运营后台">
    <el-row :gutter="12">
      <el-col :span="6" v-for="(v,k) in cards" :key="k">
        <el-statistic :title="v.label" :value="data[k] || 0" style="background:#fff;padding:16px;border-radius:8px;margin-bottom:12px" />
      </el-col>
    </el-row>
  </el-card>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { adminApi } from '@/api'
const data = ref({})
const cards = {
  users: { label: '用户数' },
  accountsPending: { label: '待审账号' },
  accountsOnSale: { label: '在售账号' },
  ordersTrading: { label: '交易中订单' },
  ordersToday: { label: '今日订单' },
  withdrawalsPending: { label: '待审提现' },
  csOpen: { label: '客服会话' },
  complaintsOpen: { label: '待处理投诉' },
}
onMounted(async () => { const res = await adminApi.dashboard(); data.value = res.data || {} })
</script>
