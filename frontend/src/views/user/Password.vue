<template>
  <el-card header="修改密码">
    <el-form label-width="100px" style="max-width:420px" @submit.prevent="save">
      <el-form-item label="原密码"><el-input v-model="oldPassword" type="password" show-password /></el-form-item>
      <el-form-item label="新密码"><el-input v-model="newPassword" type="password" show-password /></el-form-item>
      <el-form-item><el-button type="warning" native-type="submit">修改</el-button></el-form-item>
    </el-form>
  </el-card>
</template>

<script setup>
import { ref } from 'vue'
import { userApi } from '@/api'
import { ElMessage } from 'element-plus'
const oldPassword = ref('')
const newPassword = ref('')
async function save() {
  await userApi.updatePassword({ oldPassword: oldPassword.value, newPassword: newPassword.value })
  ElMessage.success('密码已修改')
  oldPassword.value = ''
  newPassword.value = ''
}
</script>
