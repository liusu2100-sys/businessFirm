<template>
  <el-card header="个人资料">
    <el-form :model="form" label-width="90px" style="max-width:480px">
      <el-form-item label="手机号"><el-input :model-value="form.phone" disabled /></el-form-item>
      <el-form-item label="昵称"><el-input v-model="form.nickname" /></el-form-item>
      <el-form-item label="头像URL"><el-input v-model="form.avatar" /></el-form-item>
      <el-form-item label="真实姓名"><el-input v-model="form.realName" /></el-form-item>
      <el-form-item label="余额"><b style="color:#e6a23c">¥{{ form.balance }}</b></el-form-item>
      <el-form-item><el-button type="warning" @click="save">保存</el-button></el-form-item>
    </el-form>
    <el-divider />
    <h4>充值说明</h4>
    <div v-if="recharge">
      <p>{{ recharge.tips }}</p>
      <img v-if="recharge.qrcode" :src="recharge.qrcode" style="width:160px;height:160px" />
      <p>{{ recharge.contact }}</p>
    </div>
  </el-card>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue'
import { userApi, publicApi } from '@/api'
import { useUserStore } from '@/stores/user'
import { ElMessage } from 'element-plus'

const userStore = useUserStore()
const form = reactive({ phone: '', nickname: '', avatar: '', realName: '', balance: 0 })
const recharge = ref(null)

onMounted(async () => {
  const [p, r] = await Promise.all([userApi.profile(), publicApi.rechargeConfig()])
  Object.assign(form, p.data)
  recharge.value = r.data
})

async function save() {
  await userApi.updateProfile({ nickname: form.nickname, avatar: form.avatar, realName: form.realName })
  await userStore.refreshProfile()
  ElMessage.success('已保存')
}
</script>
