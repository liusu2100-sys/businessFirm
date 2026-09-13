import axios from 'axios'
import { ElMessage } from 'element-plus'
import router from '@/router'

const request = axios.create({
  baseURL: '/api',
  timeout: 30000,
})

request.interceptors.request.use((config) => {
  const token = localStorage.getItem('token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

request.interceptors.response.use(
  (res) => {
    const data = res.data
    if (data && typeof data.code !== 'undefined' && data.code !== 200) {
      ElMessage.error(data.message || '请求失败')
      if (data.code === 401) {
        localStorage.removeItem('token')
        router.push('/login')
      }
      return Promise.reject(data)
    }
    return data
  },
  (err) => {
    const msg = err.response?.data?.message || err.message || '网络错误'
    if (err.response?.status === 401) {
      localStorage.removeItem('token')
      router.push('/login')
    }
    ElMessage.error(msg)
    return Promise.reject(err)
  }
)

export default request
