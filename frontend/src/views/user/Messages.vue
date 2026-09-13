<template>
  <el-card header="消息中心">
    <el-row :gutter="12">
      <el-col :span="8">
        <div class="conv" v-for="c in conversations" :key="c.id" :class="{active: cid===c.id}" @click="select(c)">
          <div class="t">{{ c.title }}</div>
          <div class="p">{{ c.lastMessage?.content || '暂无消息' }}</div>
        </div>
        <div v-if="!conversations.length" class="empty-box">暂无会话</div>
      </el-col>
      <el-col :span="16">
        <div class="chat" ref="boxRef">
          <div v-for="m in messages" :key="m.id" class="m" :class="{me: m.userId===userStore.user?.id}">
            <div class="b">{{ m.content }}</div>
            <div class="time">{{ m.nickname }} · {{ m.createdAt }}</div>
          </div>
        </div>
        <div class="send" v-if="cid">
          <el-input v-model="text" @keyup.enter="send" placeholder="输入消息" />
          <el-button type="warning" @click="send">发送</el-button>
        </div>
      </el-col>
    </el-row>
  </el-card>
</template>

<script setup>
import { ref, onMounted, nextTick, watch } from 'vue'
import { useRoute } from 'vue-router'
import { imApi } from '@/api'
import { useUserStore } from '@/stores/user'
const userStore = useUserStore()
const route = useRoute()
const conversations = ref([])
const messages = ref([])
const cid = ref(null)
const text = ref('')
const boxRef = ref()

async function loadConvs() {
  const res = await imApi.conversations()
  conversations.value = res.data || []
}
async function select(c) {
  cid.value = c.id
  const res = await imApi.messages({ conversationId: c.id })
  messages.value = res.data.list || []
  await nextTick()
  if (boxRef.value) boxRef.value.scrollTop = boxRef.value.scrollHeight
}
async function send() {
  if (!text.value.trim() || !cid.value) return
  await imApi.send({ conversationId: cid.value, content: text.value.trim() })
  text.value = ''
  const c = conversations.value.find(x => x.id === cid.value)
  if (c) await select(c)
  await loadConvs()
}
onMounted(async () => {
  await loadConvs()
  if (route.query.cid) {
    const id = Number(route.query.cid)
    let c = conversations.value.find(x => x.id === id)
    if (!c) { await loadConvs(); c = conversations.value.find(x => x.id === id) || { id } }
    await select(c)
  }
})
</script>

<style scoped>
.conv { padding: 10px; border-bottom: 1px solid #f0f0f0; cursor: pointer; }
.conv.active { background: #fff7e6; }
.conv .t { font-weight: 600; }
.conv .p { font-size: 12px; color: #999; margin-top: 4px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.chat { height: 360px; overflow: auto; background: #f7f8fa; padding: 12px; border-radius: 8px; }
.m { margin-bottom: 10px; }
.m.me { text-align: right; }
.m .b { display: inline-block; background: #fff; padding: 8px 12px; border-radius: 8px; max-width: 80%; text-align: left; }
.m.me .b { background: #ffd666; }
.time { font-size: 11px; color: #aaa; margin-top: 2px; }
.send { display: flex; gap: 8px; margin-top: 10px; }
</style>
