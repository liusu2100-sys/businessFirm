<template>
  <div class="page" v-loading="loading">
    <template v-if="item">
      <el-row :gutter="20">
        <el-col :md="14" :xs="24">
          <el-card shadow="never">
            <img class="cover" :src="item.pic || 'https://picsum.photos/800/450'" />
            <div class="shots" v-if="item.screenshots?.length">
              <img v-for="(s,i) in item.screenshots" :key="i" :src="s" />
            </div>
          </el-card>
        </el-col>
        <el-col :md="10" :xs="24">
          <el-card shadow="never">
            <h2>{{ item.accountNo || ('账号#' + item.id) }}</h2>
            <div class="price">¥{{ item.price }} <small>/ {{ item.rentDays || 1 }}天 · 押金 ¥{{ item.deposit }}</small></div>
            <el-descriptions :column="1" border size="small" style="margin-top:16px">
              <el-descriptions-item label="登录方式">{{ item.loginType }}</el-descriptions-item>
              <el-descriptions-item label="段位">{{ item.rankLevel }}</el-descriptions-item>
              <el-descriptions-item label="哈夫币">{{ item.havCoin }}</el-descriptions-item>
              <el-descriptions-item label="保险柜">{{ item.insuranceBox }}</el-descriptions-item>
              <el-descriptions-item label="体力/负重/防护">{{ item.staminaLevel }} / {{ item.weightLevel }} / {{ item.fhLevel }}</el-descriptions-item>
              <el-descriptions-item label="KD">{{ item.kdValue }}</el-descriptions-item>
              <el-descriptions-item label="常登录地">{{ item.commonLoginArea }}</el-descriptions-item>
              <el-descriptions-item label="交易时段">{{ item.tradeStartTime }} - {{ item.tradeEndTime }}</el-descriptions-item>
              <el-descriptions-item label="卖家">{{ item.sellerNickname }}</el-descriptions-item>
            </el-descriptions>
            <div class="tag-row" style="margin-top:12px">
              <el-tag v-if="item.trainSixGrid">训练六格</el-tag>
              <el-tag type="success" v-if="item.faceIsSelf">本人脸</el-tag>
              <el-tag type="danger" v-if="item.ban90Days">近90天封禁</el-tag>
            </div>
            <p style="margin-top:12px;color:#606266">{{ item.remark }}</p>
            <div style="margin-top:20px;display:flex;gap:10px">
              <el-button type="warning" size="large" @click="rent" :loading="ordering">立即租赁</el-button>
              <el-button size="large" @click="consult">咨询卖家</el-button>
            </div>
          </el-card>
          <el-card shadow="never" style="margin-top:12px" header="皮肤/资产">
            <p><b>近战：</b>{{ (item.meleeSkins||[]).join('、') || '无' }}</p>
            <p><b>武器：</b>{{ (item.weaponSkins||[]).join('、') || '无' }}</p>
            <p><b>干员：</b>{{ (item.operatorSkins||[]).join('、') || '无' }}</p>
            <p><b>流动资产：</b>¥{{ item.liquidAssets }}</p>
          </el-card>
        </el-col>
      </el-row>
    </template>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { accountApi, orderApi, imApi } from '@/api'
import { useUserStore } from '@/stores/user'
import { ElMessage, ElMessageBox } from 'element-plus'

const route = useRoute()
const router = useRouter()
const userStore = useUserStore()
const item = ref(null)
const loading = ref(false)
const ordering = ref(false)

async function load() {
  loading.value = true
  try {
    const res = await accountApi.detail(route.params.id)
    item.value = res.data
  } finally { loading.value = false }
}

async function rent() {
  if (!userStore.isLogin) return router.push('/login')
  await ElMessageBox.confirm(`确认租赁？需支付租金 ¥${item.value.price} + 押金 ¥${item.value.deposit}`, '确认下单')
  ordering.value = true
  try {
    const res = await orderApi.create({ gameAccountId: item.value.id, rentDays: item.value.rentDays })
    await orderApi.pay(res.data.id)
    ElMessage.success('支付成功，请到「我的购买」查看')
    await userStore.refreshProfile()
    router.push('/user/center/buys')
  } catch (e) {
    // error toast already shown
  } finally { ordering.value = false }
}

async function consult() {
  if (!userStore.isLogin) return router.push('/login')
  const res = await imApi.productConsult(item.value.id)
  router.push({ path: '/user/center/messages', query: { cid: res.data.id } })
}

onMounted(load)
</script>

<style scoped>
.cover { width: 100%; border-radius: 8px; max-height: 420px; object-fit: cover; }
.shots { display: flex; gap: 8px; margin-top: 10px; overflow: auto; }
.shots img { height: 80px; border-radius: 6px; }
.price { color: #f56c6c; font-size: 28px; font-weight: 700; margin-top: 8px; }
</style>
