<template>
  <div class="page">
    <el-card>
      <div class="me">
        <el-avatar :size="64" :src="userStore.user?.avatar">{{ userStore.user?.nickname?.[0] }}</el-avatar>
        <div>
          <h2>{{ userStore.user?.nickname }}</h2>
          <p>手机：{{ userStore.user?.phone }} · 余额：¥{{ userStore.user?.balance }}</p>
        </div>
      </div>
      <el-row :gutter="12" style="margin-top:20px">
        <el-col :span="8" v-for="l in links" :key="l.path">
          <el-card shadow="hover" class="link" @click="$router.push(l.path)">{{ l.title }}</el-card>
        </el-col>
      </el-row>
    </el-card>
  </div>
</template>

<script setup>
import { useUserStore } from '@/stores/user'
const userStore = useUserStore()
const links = [
  { title: '个人资料', path: '/user/center/profile' },
  { title: '我的账号', path: '/user/center/accounts' },
  { title: '我的购买', path: '/user/center/buys' },
  { title: '我的出售', path: '/user/center/sells' },
  { title: '余额明细', path: '/user/center/balance' },
  { title: '提现', path: '/user/center/withdrawal' },
  { title: '消息', path: '/user/center/messages' },
  { title: '投诉', path: '/user/center/complaints' },
  { title: '发布账号', path: '/publish' },
]
</script>

<style scoped>
.me { display:flex; gap:16px; align-items:center; }
.link { text-align:center; cursor:pointer; margin-bottom:12px; }
</style>
