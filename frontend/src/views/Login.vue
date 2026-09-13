<template>
  <div class="auth-page">
    <el-card class="auth-card">
      <div class="brand">🐭 鼠鼠商行</div>
      <h2>登录</h2>
      <el-form :model="form" @submit.prevent="submit">
        <el-form-item><el-input v-model="form.phone" placeholder="手机号" prefix-icon="Phone" /></el-form-item>
        <el-form-item><el-input v-model="form.password" type="password" placeholder="密码" show-password prefix-icon="Lock" /></el-form-item>
        <el-button type="warning" style="width:100%" :loading="loading" native-type="submit">登录</el-button>
      </el-form>
      <div class="links">
        <router-link to="/register">没有账号？去注册</router-link>
        <router-link to="/">返回首页</router-link>
      </div>
      <el-alert style="margin-top:16px" type="info" :closable="false" title="演示账号" description="管理员 13800000000 / password123 · 买家 13800000002 / password123 · 卖家 13800000001 / password123" />
    </el-card>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useUserStore } from '@/stores/user'
import { ElMessage } from 'element-plus'

const form = reactive({ phone: '', password: '' })
const loading = ref(false)
const userStore = useUserStore()
const router = useRouter()
const route = useRoute()

async function submit() {
  loading.value = true
  try {
    await userStore.login(form.phone, form.password)
    ElMessage.success('登录成功')
    router.replace(route.query.redirect || '/')
  } finally { loading.value = false }
}
</script>

<style scoped>
.auth-page { min-height: 100vh; display:flex; align-items:center; justify-content:center; background: linear-gradient(135deg,#1a1a2e,#16213e); padding: 20px; }
.auth-card { width: 400px; border-radius: 16px; }
.brand { text-align:center; font-size: 28px; font-weight: 700; color: #e6a23c; }
h2 { text-align:center; margin: 8px 0 24px; }
.links { display:flex; justify-content:space-between; margin-top: 14px; font-size: 13px; color:#e6a23c; }
</style>
