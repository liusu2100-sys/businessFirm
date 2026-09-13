<template>
  <div class="auth-page">
    <el-card class="auth-card">
      <div class="brand">🐭 鼠鼠商行</div>
      <h2>注册</h2>
      <el-form :model="form" @submit.prevent="submit">
        <el-form-item><el-input v-model="form.phone" placeholder="手机号" /></el-form-item>
        <el-form-item><el-input v-model="form.nickname" placeholder="昵称（可选）" /></el-form-item>
        <el-form-item><el-input v-model="form.password" type="password" placeholder="密码（至少6位）" show-password /></el-form-item>
        <el-button type="warning" style="width:100%" :loading="loading" native-type="submit">注册</el-button>
      </el-form>
      <div class="links"><router-link to="/login">已有账号？去登录</router-link></div>
    </el-card>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useUserStore } from '@/stores/user'
import { ElMessage } from 'element-plus'

const form = reactive({ phone: '', password: '', nickname: '' })
const loading = ref(false)
const userStore = useUserStore()
const router = useRouter()

async function submit() {
  loading.value = true
  try {
    await userStore.register(form)
    ElMessage.success('注册成功')
    router.replace('/')
  } finally { loading.value = false }
}
</script>

<style scoped>
.auth-page { min-height: 100vh; display:flex; align-items:center; justify-content:center; background: linear-gradient(135deg,#1a1a2e,#16213e); padding: 20px; }
.auth-card { width: 400px; border-radius: 16px; }
.brand { text-align:center; font-size: 28px; font-weight: 700; color: #e6a23c; }
h2 { text-align:center; margin: 8px 0 24px; }
.links { margin-top: 14px; text-align:center; color:#e6a23c; font-size:13px; }
</style>
