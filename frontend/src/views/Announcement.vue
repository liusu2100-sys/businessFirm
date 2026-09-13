<template>
  <div class="page">
    <el-card header="平台公告">
      <el-table :data="list" @row-click="(r)=>$router.push(`/announcement/${r.id}`)" style="cursor:pointer">
        <el-table-column prop="title" label="标题" />
        <el-table-column prop="createdAt" label="时间" width="180" />
        <el-table-column label="置顶" width="80">
          <template #default="{ row }"><el-tag v-if="row.isTop" size="small" type="danger">置顶</el-tag></template>
        </el-table-column>
      </el-table>
    </el-card>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { publicApi } from '@/api'
const list = ref([])
onMounted(async () => {
  const res = await publicApi.announcements({ type: 'notice' })
  list.value = res.data.list || []
})
</script>
