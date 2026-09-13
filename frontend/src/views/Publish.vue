<template>
  <div class="page">
    <el-card header="发布账号">
      <el-form :model="form" label-width="110px">
        <el-row :gutter="16">
          <el-col :md="12"><el-form-item label="账号编号"><el-input v-model="form.accountNo" /></el-form-item></el-col>
          <el-col :md="12"><el-form-item label="登录方式" required>
            <el-select v-model="form.loginType" style="width:100%">
              <el-option v-for="t in loginTypes" :key="t" :label="t" :value="t" />
            </el-select>
          </el-form-item></el-col>
          <el-col :md="12"><el-form-item label="账号密码"><el-input v-model="form.accountPwd" placeholder="交易后对买家可见" /></el-form-item></el-col>
          <el-col :md="12"><el-form-item label="段位">
            <el-select v-model="form.rankLevel" style="width:100%" clearable>
              <el-option v-for="t in ranks" :key="t" :label="t" :value="t" />
            </el-select>
          </el-form-item></el-col>
          <el-col :md="8"><el-form-item label="租金" required><el-input-number v-model="form.price" :min="0" :precision="2" style="width:100%" /></el-form-item></el-col>
          <el-col :md="8"><el-form-item label="押金"><el-input-number v-model="form.deposit" :min="0" :precision="2" style="width:100%" /></el-form-item></el-col>
          <el-col :md="8"><el-form-item label="租期(天)"><el-input-number v-model="form.rentDays" :min="1" style="width:100%" /></el-form-item></el-col>
          <el-col :md="8"><el-form-item label="哈夫币"><el-input-number v-model="form.havCoin" :min="0" style="width:100%" /></el-form-item></el-col>
          <el-col :md="8"><el-form-item label="保险柜"><el-input-number v-model="form.insuranceBox" :min="0" style="width:100%" /></el-form-item></el-col>
          <el-col :md="8"><el-form-item label="KD"><el-input-number v-model="form.kdValue" :min="0" :step="0.1" style="width:100%" /></el-form-item></el-col>
          <el-col :md="8"><el-form-item label="体力等级"><el-input-number v-model="form.staminaLevel" :min="0" style="width:100%" /></el-form-item></el-col>
          <el-col :md="8"><el-form-item label="负重等级"><el-input-number v-model="form.weightLevel" :min="0" style="width:100%" /></el-form-item></el-col>
          <el-col :md="8"><el-form-item label="防护等级"><el-input-number v-model="form.fhLevel" :min="0" style="width:100%" /></el-form-item></el-col>
          <el-col :md="12"><el-form-item label="常登录地"><el-input v-model="form.commonLoginArea" /></el-form-item></el-col>
          <el-col :md="12"><el-form-item label="封面图URL"><el-input v-model="form.pic" placeholder="https://..." /></el-form-item></el-col>
          <el-col :md="12"><el-form-item label="近战皮肤">
            <el-select v-model="form.meleeSkins" multiple style="width:100%">
              <el-option v-for="t in melee" :key="t" :label="t" :value="t" />
            </el-select>
          </el-form-item></el-col>
          <el-col :md="12"><el-form-item label="武器皮肤">
            <el-select v-model="form.weaponSkins" multiple style="width:100%">
              <el-option v-for="t in weapons" :key="t" :label="t" :value="t" />
            </el-select>
          </el-form-item></el-col>
          <el-col :span="24">
            <el-form-item label="选项">
              <el-checkbox v-model="form.trainSixGrid">训练六格</el-checkbox>
              <el-checkbox v-model="form.faceIsSelf">本人脸</el-checkbox>
              <el-checkbox v-model="form.ban90Days">近90天封禁</el-checkbox>
            </el-form-item>
          </el-col>
          <el-col :span="24"><el-form-item label="备注"><el-input v-model="form.remark" type="textarea" rows="3" /></el-form-item></el-col>
        </el-row>
        <el-form-item>
          <el-button type="warning" :loading="loading" @click="submit">提交审核</el-button>
        </el-form-item>
      </el-form>
    </el-card>
  </div>
</template>

<script setup>
import { reactive, ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { accountApi } from '@/api'
import { useUserStore } from '@/stores/user'
import { ElMessage } from 'element-plus'

const userStore = useUserStore()
const router = useRouter()
const loading = ref(false)
const loginTypes = computed(() => userStore.config.login_types || ['QQ','微信','手机'])
const ranks = computed(() => userStore.config.rank_levels || [])
const melee = computed(() => userStore.config.melee_skin_options || [])
const weapons = computed(() => userStore.config.weapon_skin_options || [])

const form = reactive({
  accountNo: '', loginType: 'QQ', accountPwd: '', rankLevel: '',
  price: 50, deposit: 100, rentDays: 1, havCoin: 0, insuranceBox: 0, kdValue: 1,
  staminaLevel: 0, weightLevel: 0, fhLevel: 0, commonLoginArea: '',
  pic: 'https://picsum.photos/seed/newacc/400/300',
  meleeSkins: [], weaponSkins: [], trainSixGrid: false, faceIsSelf: true, ban90Days: false,
  remark: '',
})

async function submit() {
  if (!form.loginType || form.price < 0) return ElMessage.warning('请完善必填项')
  loading.value = true
  try {
    await accountApi.create({ ...form })
    ElMessage.success('已提交，等待审核')
    router.push('/user/center/accounts')
  } finally { loading.value = false }
}
</script>
