<template>
  <div class="layout">
    <header class="header">
      <div class="header-inner">
        <router-link to="/" class="logo">
          <span class="logo-ico">🐭</span>
          <span>{{ siteName }}</span>
        </router-link>
        <nav class="nav">
          <router-link to="/">首页</router-link>
          <router-link to="/products">租号大厅</router-link>
          <router-link to="/publish">发布账号</router-link>
          <router-link to="/announcement">公告</router-link>
          <router-link to="/help">帮助</router-link>
        </nav>
        <div class="actions">
          <template v-if="userStore.isLogin">
            <el-dropdown>
              <span class="user-entry">
                <el-avatar :size="28" :src="userStore.user?.avatar">{{ userStore.user?.nickname?.[0] }}</el-avatar>
                <span class="nick">{{ userStore.user?.nickname }}</span>
                <span class="bal">¥{{ userStore.user?.balance }}</span>
              </span>
              <template #dropdown>
                <el-dropdown-menu>
                  <el-dropdown-item @click="$router.push('/user/center/profile')">个人中心</el-dropdown-item>
                  <el-dropdown-item @click="$router.push('/my')">我的</el-dropdown-item>
                  <el-dropdown-item v-if="userStore.isAdmin" @click="$router.push('/admin')">运营后台</el-dropdown-item>
                  <el-dropdown-item divided @click="onLogout">退出登录</el-dropdown-item>
                </el-dropdown-menu>
              </template>
            </el-dropdown>
          </template>
          <template v-else>
            <el-button type="warning" @click="$router.push('/login')">登录</el-button>
            <el-button @click="$router.push('/register')">注册</el-button>
          </template>
        </div>
      </div>
    </header>
    <main class="main"><router-view /></main>
    <footer class="footer">
      <div>© {{ new Date().getFullYear() }} {{ siteName }} · 三角洲行动账号租赁平台（演示）</div>
    </footer>
    <CsWidget />
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useUserStore } from '@/stores/user'
import CsWidget from '@/components/CsWidget.vue'

const userStore = useUserStore()
const router = useRouter()
const siteName = computed(() => userStore.config.site_name || '鼠鼠商行')

onMounted(async () => {
  await userStore.fetchConfig()
  if (userStore.isLogin) await userStore.fetchMe()
})

function onLogout() {
  userStore.logout()
  router.push('/login')
}
</script>

<style scoped>
.layout { min-height: 100vh; display: flex; flex-direction: column; }
.header {
  background: linear-gradient(90deg, #1a1a2e, #16213e);
  color: #fff; position: sticky; top: 0; z-index: 100;
  box-shadow: 0 2px 12px rgba(0,0,0,.15);
}
.header-inner {
  max-width: 1200px; margin: 0 auto; padding: 0 16px;
  height: 60px; display: flex; align-items: center; gap: 24px;
}
.logo { display: flex; align-items: center; gap: 8px; font-size: 20px; font-weight: 700; color: #ffd666; }
.logo-ico { font-size: 26px; }
.nav { display: flex; gap: 18px; flex: 1; }
.nav a { color: rgba(255,255,255,.85); font-size: 14px; }
.nav a.router-link-active { color: #ffd666; font-weight: 600; }
.actions { display: flex; align-items: center; gap: 8px; }
.user-entry { display: flex; align-items: center; gap: 8px; cursor: pointer; color: #fff; }
.nick { font-size: 14px; }
.bal { background: rgba(255,214,102,.2); color: #ffd666; padding: 2px 8px; border-radius: 10px; font-size: 12px; }
.main { flex: 1; }
.footer { text-align: center; padding: 24px; color: #909399; font-size: 13px; background: #fff; margin-top: 24px; }
@media (max-width: 768px) {
  .nav { display: none; }
  .nick { display: none; }
}
</style>
