import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { authApi, userApi, publicApi } from '@/api'

export const useUserStore = defineStore('user', () => {
  const token = ref(localStorage.getItem('token') || '')
  const user = ref(null)
  const config = ref({})

  const isLogin = computed(() => !!token.value)
  const isAdmin = computed(() => user.value?.role === 'admin')

  async function login(phone, password) {
    const res = await authApi.login({ phone, password })
    token.value = res.data.token
    user.value = res.data.user
    localStorage.setItem('token', token.value)
    return res
  }

  async function register(payload) {
    const res = await authApi.register(payload)
    token.value = res.data.token
    user.value = res.data.user
    localStorage.setItem('token', token.value)
    return res
  }

  async function fetchMe() {
    if (!token.value) return
    try {
      const res = await authApi.me()
      user.value = res.data
    } catch {
      logout()
    }
  }

  async function fetchConfig() {
    const res = await publicApi.config()
    config.value = res.data || {}
  }

  function logout() {
    token.value = ''
    user.value = null
    localStorage.removeItem('token')
  }

  async function refreshProfile() {
    const res = await userApi.profile()
    user.value = { ...user.value, ...res.data }
  }

  return { token, user, config, isLogin, isAdmin, login, register, fetchMe, fetchConfig, logout, refreshProfile }
})
