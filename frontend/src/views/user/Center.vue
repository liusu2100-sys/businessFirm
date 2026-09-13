<template>
  <div class="page">
    <el-row :gutter="16">
      <el-col :md="5" :xs="24">
        <el-menu :default-active="$route.path" router class="side">
          <el-menu-item index="/user/center/profile">个人资料</el-menu-item>
          <el-menu-item index="/user/center/password">修改密码</el-menu-item>
          <el-menu-item index="/user/center/accounts">我的账号</el-menu-item>
          <el-menu-item index="/user/center/buys">我的购买</el-menu-item>
          <el-menu-item index="/user/center/sells">我的出售</el-menu-item>
          <el-menu-item index="/user/center/payment">收款账户</el-menu-item>
          <el-menu-item index="/user/center/balance">余额明细</el-menu-item>
          <el-menu-item index="/user/center/withdrawal">提现</el-menu-item>
          <el-menu-item index="/user/center/messages">消息</el-menu-item>
          <el-menu-item index="/user/center/complaints">投诉</el-menu-item>
        </el-menu>
      </el-col>
      <el-col :md="19" :xs="24"><router-view /></el-col>
    </el-row>
  </div>
</template>

<style scoped>
.side { border-radius: 10px; overflow: hidden; }
</style>
