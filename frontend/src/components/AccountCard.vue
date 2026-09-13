<template>
  <div class="account-card" @click="$router.push(`/product/${item.id}`)">
    <img :src="item.pic || 'https://picsum.photos/seed/placeholder/400/300'" :alt="item.accountNo" />
    <div class="body">
      <div class="price">¥{{ item.price }} <small>/ {{ item.rentDays || 1 }}天</small></div>
      <div style="margin-top:6px;font-size:13px;color:#606266">
        {{ item.rankLevel || '未定级' }} · {{ item.loginType }} · 哈夫币 {{ formatCoin(item.havCoin) }}
      </div>
      <div class="tag-row">
        <el-tag size="small" v-if="item.trainSixGrid">训练六格</el-tag>
        <el-tag size="small" type="success" v-if="item.faceIsSelf">本人脸</el-tag>
        <el-tag size="small" type="info">{{ item.commonLoginArea || '未知地区' }}</el-tag>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({ item: { type: Object, required: true } })
function formatCoin(n) {
  if (!n) return '0'
  if (n >= 10000) return (n / 10000).toFixed(1) + '万'
  return String(n)
}
</script>
