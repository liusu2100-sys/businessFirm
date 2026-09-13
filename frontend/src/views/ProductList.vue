<template>
  <div class="page">
    <el-card shadow="never">
      <el-form :inline="true" @submit.prevent="search">
        <el-form-item><el-input v-model="keyword" placeholder="搜索账号/备注/段位" clearable style="width:220px" /></el-form-item>
        <el-form-item>
          <el-select v-model="sort" style="width:130px" @change="search">
            <el-option label="最新发布" value="newest" />
            <el-option label="价格从低到高" value="price_asc" />
            <el-option label="价格从高到低" value="price_desc" />
            <el-option label="哈夫币最多" value="coin_desc" />
          </el-select>
        </el-form-item>
        <el-form-item><el-button type="warning" @click="search">搜索</el-button></el-form-item>
      </el-form>
    </el-card>
    <div class="card-grid" v-loading="loading" style="margin-top:16px">
      <AccountCard v-for="item in list" :key="item.id" :item="item" />
    </div>
    <div style="margin-top:20px;text-align:center">
      <el-pagination background layout="prev, pager, next, total" :total="total" :page-size="pageSize" v-model:current-page="page" @current-change="load" />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { accountApi } from '@/api'
import AccountCard from '@/components/AccountCard.vue'

const list = ref([])
const loading = ref(false)
const keyword = ref('')
const sort = ref('newest')
const page = ref(1)
const pageSize = 12
const total = ref(0)

async function load() {
  loading.value = true
  try {
    const res = await accountApi.list({ keyword: keyword.value, sort: sort.value, page: page.value, pageSize })
    list.value = res.data.list || []
    total.value = res.data.total || 0
  } finally { loading.value = false }
}
function search() { page.value = 1; load() }
onMounted(load)
</script>
