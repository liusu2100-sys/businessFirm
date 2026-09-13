<template>
  <div class="page" v-loading="loading">
    <el-card v-if="item">
      <h2>{{ item.title }}</h2>
      <div style="color:#909399;margin-bottom:16px">{{ item.createdAt }}</div>
      <div v-html="item.content"></div>
      <el-button style="margin-top:20px" @click="$router.back()">返回</el-button>
    </el-card>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { publicApi } from '@/api'
const route = useRoute()
const item = ref(null)
const loading = ref(false)
onMounted(async () => {
  loading.value = true
  try {
    const res = await publicApi.announcement(route.params.id)
    item.value = res.data
  } finally { loading.value = false }
})
</script>
