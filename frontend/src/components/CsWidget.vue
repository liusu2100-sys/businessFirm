<template>
  <div class="cs-wrap">
    <div v-if="open" class="cs-panel">
      <div class="cs-hd">
        <span>🐭 在线客服</span>
        <el-icon style="cursor:pointer" @click="open=false"><Close /></el-icon>
      </div>
      <div class="cs-bd" ref="boxRef">
        <div v-if="!userStore.isLogin" class="tip">请先 <a @click="$router.push('/login')">登录</a> 后咨询客服</div>
        <template v-else>
          <div v-for="m in messages" :key="m.id" class="msg" :class="{ staff: m.isStaff, me: !m.isStaff }">
            <div class="bubble">{{ m.content }}</div>
            <div class="time">{{ m.createdAt }}</div>
          </div>
        </template>
      </div>
      <div class="cs-ft" v-if="userStore.isLogin">
        <el-input v-model="text" placeholder="输入消息..." @keyup.enter="send" />
        <el-button type="warning" @click="send">发送</el-button>
      </div>
    </div>
    <button class="cs-btn" @click="toggle">客服</button>
  </div>
</template>

<script setup>
import { ref, nextTick } from 'vue'
import { useUserStore } from '@/stores/user'
import { csApi } from '@/api'
import { ElMessage } from 'element-plus'

const userStore = useUserStore()
const open = ref(false)
const text = ref('')
const messages = ref([])
const sessionId = ref(null)
const boxRef = ref()

async function toggle() {
  open.value = !open.value
  if (open.value && userStore.isLogin) {
    const res = await csApi.start()
    sessionId.value = res.data.id
    await load()
  }
}

async function load() {
  if (!sessionId.value) return
  const res = await csApi.list(sessionId.value)
  messages.value = res.data || []
  await nextTick()
  if (boxRef.value) boxRef.value.scrollTop = boxRef.value.scrollHeight
}

async function send() {
  if (!text.value.trim()) return
  await csApi.send({ sessionId: sessionId.value, content: text.value.trim() })
  text.value = ''
  await load()
  ElMessage.success('已发送')
}
</script>

<style scoped>
.cs-wrap { position: fixed; right: 20px; bottom: 20px; z-index: 200; }
.cs-btn {
  width: 56px; height: 56px; border-radius: 50%; border: none;
  background: linear-gradient(135deg, #e6a23c, #f56c6c); color: #fff;
  font-weight: 700; cursor: pointer; box-shadow: 0 6px 20px rgba(230,162,60,.45);
}
.cs-panel {
  width: 320px; height: 420px; background: #fff; border-radius: 12px;
  box-shadow: 0 12px 40px rgba(0,0,0,.18); margin-bottom: 12px;
  display: flex; flex-direction: column; overflow: hidden;
}
.cs-hd { background: #1a1a2e; color: #ffd666; padding: 12px 14px; display: flex; justify-content: space-between; align-items: center; }
.cs-bd { flex: 1; overflow: auto; padding: 12px; background: #f7f8fa; }
.cs-ft { display: flex; gap: 8px; padding: 10px; border-top: 1px solid #eee; }
.msg { margin-bottom: 10px; }
.msg.me .bubble { background: #ffd666; margin-left: 40px; }
.msg.staff .bubble { background: #fff; margin-right: 40px; }
.bubble { padding: 8px 12px; border-radius: 10px; display: inline-block; max-width: 90%; }
.time { font-size: 11px; color: #aaa; margin-top: 2px; }
.tip { text-align: center; padding: 40px 10px; color: #888; }
.tip a { color: #e6a23c; cursor: pointer; }
</style>
