<template>
  <div class="page">
    <el-carousel height="280px" v-if="banners.length" class="banner">
      <el-carousel-item v-for="b in banners" :key="b.id">
        <a :href="b.link || '#'" class="banner-item">
          <img :src="b.image" :alt="b.title" />
          <div class="banner-title">{{ b.title }}</div>
        </a>
      </el-carousel-item>
    </el-carousel>

    <el-alert v-if="notice" :title="notice" type="warning" show-icon :closable="false" style="margin:16px 0" />

    <div class="stats">
      <div class="stat"><b>{{ stats.accountCount || 0 }}</b><span>在租账号</span></div>
      <div class="stat"><b>{{ stats.orderCount || 0 }}</b><span>成交订单</span></div>
      <div class="stat"><b>{{ stats.userCount || 0 }}</b><span>注册用户</span></div>
      <div class="stat"><b>{{ stats.todayOrders || 0 }}</b><span>今日订单</span></div>
    </div>

    <el-card shadow="never" class="filter-card">
      <el-form :inline="true" :model="filters" @submit.prevent="load">
        <el-form-item label="登录方式">
          <el-select v-model="filters.loginType" clearable placeholder="全部" style="width:110px">
            <el-option v-for="t in loginTypes" :key="t" :label="t" :value="t" />
          </el-select>
        </el-form-item>
        <el-form-item label="段位">
          <el-select v-model="filters.rankLevel" clearable placeholder="全部" style="width:110px">
            <el-option v-for="t in ranks" :key="t" :label="t" :value="t" />
          </el-select>
        </el-form-item>
        <el-form-item label="价格">
          <el-input v-model="filters.minPrice" placeholder="最低" style="width:80px" />
          <span style="margin:0 6px">-</span>
          <el-input v-model="filters.maxPrice" placeholder="最高" style="width:80px" />
        </el-form-item>
        <el-form-item label="哈夫币≥">
          <el-input v-model="filters.minHavCoin" placeholder="数量" style="width:100px" />
        </el-form-item>
        <el-form-item>
          <el-checkbox v-model="filters.trainSixGrid">训练六格</el-checkbox>
          <el-checkbox v-model="filters.faceIsSelf">本人脸</el-checkbox>
        </el-form-item>
        <el-form-item>
          <el-button type="warning" @click="load">筛选</el-button>
          <el-button @click="reset">重置</el-button>
        </el-form-item>
      </el-form>
    </el-card>

    <div class="sec-hd">
      <h2>热门账号</h2>
      <router-link to="/products">查看更多 →</router-link>
    </div>
    <div class="card-grid" v-loading="loading">
      <AccountCard v-for="item in list" :key="item.id" :item="item" />
    </div>
    <div v-if="!loading && !list.length" class="empty-box">暂无账号，稍后再来看看吧</div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { accountApi, publicApi } from '@/api'
import { useUserStore } from '@/stores/user'
import AccountCard from '@/components/AccountCard.vue'

const userStore = useUserStore()
const banners = ref([])
const stats = ref({})
const list = ref([])
const loading = ref(false)
const filters = ref({ loginType: '', rankLevel: '', minPrice: '', maxPrice: '', minHavCoin: '', trainSixGrid: false, faceIsSelf: false })

const notice = computed(() => userStore.config.home_notice)
const loginTypes = computed(() => userStore.config.login_types || ['QQ', '微信', '手机'])
const ranks = computed(() => userStore.config.rank_levels || [])

async function load() {
  loading.value = true
  try {
    const params = { pageSize: 8, ...filters.value }
    Object.keys(params).forEach((k) => { if (params[k] === '' || params[k] === false) delete params[k] })
    const res = await accountApi.list(params)
    list.value = res.data.list || []
  } finally {
    loading.value = false
  }
}
function reset() {
  filters.value = { loginType: '', rankLevel: '', minPrice: '', maxPrice: '', minHavCoin: '', trainSixGrid: false, faceIsSelf: false }
  load()
}
onMounted(async () => {
  const [b, s] = await Promise.all([publicApi.banners(), publicApi.stats()])
  banners.value = b.data || []
  stats.value = s.data || {}
  load()
})
</script>

<style scoped>
.banner { border-radius: 12px; overflow: hidden; }
.banner-item { display: block; height: 280px; position: relative; }
.banner-item img { width: 100%; height: 100%; object-fit: cover; }
.banner-title { position: absolute; left: 0; right: 0; bottom: 0; padding: 12px 16px; background: linear-gradient(transparent, rgba(0,0,0,.6)); color: #fff; }
.stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 16px; }
.stat { background: #fff; border-radius: 10px; padding: 16px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,.04); }
.stat b { display: block; font-size: 22px; color: #e6a23c; }
.stat span { color: #909399; font-size: 13px; }
.filter-card { margin-bottom: 16px; }
.sec-hd { display: flex; justify-content: space-between; align-items: center; margin: 8px 0 16px; }
.sec-hd h2 { margin: 0; font-size: 18px; }
.sec-hd a { color: #e6a23c; font-size: 13px; }
@media (max-width: 768px) { .stats { grid-template-columns: repeat(2, 1fr); } }
</style>
