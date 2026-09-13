<template>
  <el-card header="客服会话">
    <el-row :gutter="12">
      <el-col :span="8">
        <div v-for="s in sessions" :key="s.id" class="item" :class="{active: sid===s.id}" @click="open(s)">
          <div>{{ s.nickname }} ({{ s.phone }})</div>
          <div class="t">{{ s.lastMessageAt }}</div>
        </div>
      </el-col>
      <el-col :span="16">
        <div class="chat">
          <div v-for="m in messages" :key="m.id" class="m" :class="{staff: m.isStaff}">
            <div class="b">{{ m.content }}</div>
          </div>
        </div>
        <div style="display:flex;gap:8px;margin-top:8px" v-if="sid">
          <el-input v-model="text" @keyup.enter="send" />
          <el-button type="warning" @click="send">回复</el-button>
        </div>
      </el-col>
    </el-row>
  </el-card>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { adminApi, csApi } from '@/api'
import { ElMessage } from 'element-plus'
const sessions = ref([])
const messages = ref([])
const sid = ref(null)
const text = ref('')
async function load() { const res = await adminApi.csSessions(); sessions.value = res.data || [] }
async function open(s) {
  sid.value = s.id
  const res = await csApi.list(s.id)
  messages.value = res.data || []
}
async function send() {
  if (!text.value.trim()) return
  await csApi.send({ sessionId: sid.value, content: text.value.trim() })
  text.value = ''
  ElMessage.success('已回复')
  const s = sessions.value.find(x => x.id === sid.value)
  if (s) open(s)
}
onMounted(load)
</script>

<style scoped>
.item { padding: 10px; border-bottom: 1px solid #eee; cursor: pointer; }
.item.active { background: #fff7e6; }
.t { font-size: 12px; color: #999; }
.chat { height: 360px; overflow: auto; background: #f7f8fa; padding: 12px; border-radius: 8px; }
.m { margin-bottom: 8px; }
.m.staff { text-align: right; }
.b { display: inline-block; background: #fff; padding: 8px 12px; border-radius: 8px; }
.m.staff .b { background: #ffd666; }
</style>
