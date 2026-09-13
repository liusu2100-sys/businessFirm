<template>
  <el-card header="站点配置">
    <el-form label-width="140px" style="max-width:640px">
      <el-form-item label="站点名称"><el-input v-model="form.site_name" /></el-form-item>
      <el-form-item label="口号"><el-input v-model="form.site_slogan" /></el-form-item>
      <el-form-item label="首页公告"><el-input v-model="form.home_notice" type="textarea" /></el-form-item>
      <el-form-item label="佣金率"><el-input-number v-model="form.commission_rate" :step="0.01" :min="0" :max="1" /></el-form-item>
      <el-form-item label="提现费率"><el-input-number v-model="form.withdraw_fee_rate" :step="0.01" :min="0" :max="1" /></el-form-item>
      <el-form-item label="最低提现"><el-input-number v-model="form.min_withdraw" :min="0" /></el-form-item>
      <el-form-item label="支付超时(分)"><el-input-number v-model="form.order_pay_timeout_minutes" :min="1" /></el-form-item>
      <el-form-item label="客服联系"><el-input v-model="form.cs_contact" /></el-form-item>
      <el-form-item label="充值提示"><el-input v-model="form.recharge_tips" type="textarea" /></el-form-item>
      <el-form-item><el-button type="warning" @click="save">保存</el-button></el-form-item>
    </el-form>
  </el-card>
</template>

<script setup>
import { reactive, onMounted } from 'vue'
import { adminApi } from '@/api'
import { useUserStore } from '@/stores/user'
import { ElMessage } from 'element-plus'
const userStore = useUserStore()
const form = reactive({
  site_name: '', site_slogan: '', home_notice: '',
  commission_rate: 0.05, withdraw_fee_rate: 0.02, min_withdraw: 10,
  order_pay_timeout_minutes: 30, cs_contact: '', recharge_tips: '',
})
onMounted(async () => {
  const res = await adminApi.configs()
  Object.assign(form, res.data || {})
})
async function save() {
  await adminApi.saveConfigs({ ...form })
  await userStore.fetchConfig()
  ElMessage.success('已保存')
}
</script>
