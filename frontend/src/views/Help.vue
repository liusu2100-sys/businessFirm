<template>
  <div class="page">
    <el-card header="帮助中心">
      <el-collapse v-model="active">
        <el-collapse-item v-for="h in list" :key="h.id" :title="h.title" :name="h.id">
          <div v-html="h.content"></div>
        </el-collapse-item>
      </el-collapse>
      <div v-if="!list.length" class="empty-box">暂无帮助文章</div>
    </el-card>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { publicApi } from '@/api'
const list = ref([])
const active = ref([])
onMounted(async () => {
  const res = await publicApi.help()
  list.value = res.data || []
  if (list.value[0]) active.value = [list.value[0].id]
})
</script>
