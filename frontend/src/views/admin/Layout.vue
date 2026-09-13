<template>
  <div>
    <el-alert v-if="!userStore.isAdmin" type="error" title="需要管理员权限" :closable="false" />
    <el-row :gutter="16" v-else>
      <el-col :md="4" :xs="24">
        <el-menu :default-active="$route.path" router>
          <el-menu-item index="/admin">仪表盘</el-menu-item>
          <el-menu-item index="/admin/users">用户调账</el-menu-item>
          <el-menu-item index="/admin/accounts">账号审核</el-menu-item>
          <el-menu-item index="/admin/orders">订单管理</el-menu-item>
          <el-menu-item index="/admin/withdrawals">提现审核</el-menu-item>
          <el-menu-item index="/admin/cs">客服会话</el-menu-item>
          <el-menu-item index="/admin/configs">站点配置</el-menu-item>
        </el-menu>
      </el-col>
      <el-col :md="20" :xs="24"><router-view /></el-col>
    </el-row>
  </div>
</template>

<script setup>
import { useUserStore } from '@/stores/user'
const userStore = useUserStore()
</script>
