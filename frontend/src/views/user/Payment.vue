<template>
  <el-card>
    <template #header>
      <div style="display:flex;justify-content:space-between">
        <span>收款账户</span>
        <el-button type="warning" size="small" @click="openDialog()">添加</el-button>
      </div>
    </template>
    <el-table :data="list">
      <el-table-column label="类型" width="100">
        <template #default="{ row }">{{ ({alipay:'支付宝',wechat:'微信',bank:'银行卡'}[row.type]) }}</template>
      </el-table-column>
      <el-table-column prop="accountName" label="户名" />
      <el-table-column prop="accountNo" label="账号" />
      <el-table-column prop="bankName" label="银行" />
      <el-table-column label="默认" width="80">
        <template #default="{ row }"><el-tag v-if="row.isDefault" size="small" type="success">默认</el-tag></template>
      </el-table-column>
      <el-table-column label="操作" width="180">
        <template #default="{ row }">
          <el-button link @click="setDefault(row)" v-if="!row.isDefault">设默认</el-button>
          <el-button link type="danger" @click="remove(row)">删除</el-button>
        </template>
      </el-table-column>
    </el-table>
    <el-dialog v-model="visible" title="添加收款账户" width="420px">
      <el-form :model="form" label-width="80px">
        <el-form-item label="类型">
          <el-select v-model="form.type" style="width:100%">
            <el-option label="支付宝" value="alipay" />
            <el-option label="微信" value="wechat" />
            <el-option label="银行卡" value="bank" />
          </el-select>
        </el-form-item>
        <el-form-item label="户名"><el-input v-model="form.accountName" /></el-form-item>
        <el-form-item label="账号"><el-input v-model="form.accountNo" /></el-form-item>
        <el-form-item label="银行" v-if="form.type==='bank'"><el-input v-model="form.bankName" /></el-form-item>
        <el-form-item label="默认"><el-switch v-model="form.isDefault" /></el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="visible=false">取消</el-button>
        <el-button type="warning" @click="save">保存</el-button>
      </template>
    </el-dialog>
  </el-card>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { paymentApi } from '@/api'
import { ElMessage } from 'element-plus'
const list = ref([])
const visible = ref(false)
const form = reactive({ type: 'alipay', accountName: '', accountNo: '', bankName: '', isDefault: false })
async function load() { const res = await paymentApi.list(); list.value = res.data || [] }
function openDialog() { Object.assign(form, { type: 'alipay', accountName: '', accountNo: '', bankName: '', isDefault: false }); visible.value = true }
async function save() { await paymentApi.create(form); ElMessage.success('已添加'); visible.value = false; load() }
async function setDefault(row) { await paymentApi.setDefault(row.id); load() }
async function remove(row) { await paymentApi.remove(row.id); ElMessage.success('已删除'); load() }
onMounted(load)
</script>
