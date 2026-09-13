<template>
  <el-card>
    <template #header>
      <div style="display:flex;justify-content:space-between">
        <span>投诉建议</span>
        <el-button type="warning" size="small" @click="visible=true">新建</el-button>
      </div>
    </template>
    <el-table :data="list">
      <el-table-column prop="title" label="标题" />
      <el-table-column prop="content" label="内容" show-overflow-tooltip />
      <el-table-column label="状态" width="100">
        <template #default="{ row }">{{ ({0:'待处理',1:'处理中',2:'已完成'}[row.status]) }}</template>
      </el-table-column>
      <el-table-column prop="reply" label="回复" />
      <el-table-column label="操作" width="90">
        <template #default="{ row }"><el-button link type="danger" @click="remove(row)">删除</el-button></template>
      </el-table-column>
    </el-table>
    <el-dialog v-model="visible" title="提交投诉" width="480px">
      <el-form label-width="70px">
        <el-form-item label="标题"><el-input v-model="title" /></el-form-item>
        <el-form-item label="内容"><el-input v-model="content" type="textarea" rows="4" /></el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="visible=false">取消</el-button>
        <el-button type="warning" @click="save">提交</el-button>
      </template>
    </el-dialog>
  </el-card>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { complaintApi } from '@/api'
import { ElMessage } from 'element-plus'
const list = ref([])
const visible = ref(false)
const title = ref('')
const content = ref('')
async function load() { const res = await complaintApi.list(); list.value = res.data.list || [] }
async function save() {
  await complaintApi.create({ title: title.value, content: content.value })
  ElMessage.success('已提交'); visible.value = false; title.value=''; content.value=''; load()
}
async function remove(row) { await complaintApi.remove(row.id); load() }
onMounted(load)
</script>
