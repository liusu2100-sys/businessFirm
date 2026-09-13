<template>
  <el-card header="订单管理">
    <el-table :data="list">
      <el-table-column prop="orderNo" label="订单号" min-width="160" />
      <el-table-column label="买家"><template #default="{row}">{{ row.buyer?.nickname }}</template></el-table-column>
      <el-table-column label="卖家"><template #default="{row}">{{ row.seller?.nickname }}</template></el-table-column>
      <el-table-column prop="totalAmount" label="金额" width="100" />
      <el-table-column prop="status" label="状态" width="80" />
      <el-table-column prop="createdAt" label="时间" width="170" />
    </el-table>
  </el-card>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { adminApi } from '@/api'
const list = ref([])
onMounted(async () => { const res = await adminApi.orders(); list.value = res.data.list || [] })
</script>
