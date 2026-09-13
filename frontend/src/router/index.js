import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  {
    path: '/',
    component: () => import('@/layouts/MainLayout.vue'),
    children: [
      { path: '', name: 'Home', component: () => import('@/views/Home.vue') },
      { path: 'products', name: 'ProductList', component: () => import('@/views/ProductList.vue') },
      { path: 'product/:id', name: 'ProductDetail', component: () => import('@/views/ProductDetail.vue') },
      { path: 'publish', name: 'Publish', component: () => import('@/views/Publish.vue'), meta: { auth: true } },
      {
        path: 'user/center',
        component: () => import('@/views/user/Center.vue'),
        meta: { auth: true },
        children: [
          { path: '', redirect: '/user/center/profile' },
          { path: 'profile', name: 'UserProfile', component: () => import('@/views/user/Profile.vue') },
          { path: 'password', name: 'PasswordManage', component: () => import('@/views/user/Password.vue') },
          { path: 'accounts', name: 'UserAccounts', component: () => import('@/views/user/Accounts.vue') },
          { path: 'buys', name: 'MyBuys', component: () => import('@/views/user/MyBuys.vue') },
          { path: 'sells', name: 'MySells', component: () => import('@/views/user/MySells.vue') },
          { path: 'payment', name: 'PaymentAccounts', component: () => import('@/views/user/Payment.vue') },
          { path: 'balance', name: 'BalanceLog', component: () => import('@/views/user/Balance.vue') },
          { path: 'withdrawal', name: 'Withdrawal', component: () => import('@/views/user/Withdrawal.vue') },
          { path: 'complaints', name: 'Complaints', component: () => import('@/views/user/Complaints.vue') },
          { path: 'messages', name: 'Messages', component: () => import('@/views/user/Messages.vue') },
        ],
      },
      { path: 'help', name: 'Help', component: () => import('@/views/Help.vue') },
      { path: 'announcement', name: 'Announcement', component: () => import('@/views/Announcement.vue') },
      { path: 'announcement/:id', name: 'AnnouncementDetail', component: () => import('@/views/AnnouncementDetail.vue') },
      { path: 'my', name: 'MyPage', component: () => import('@/views/MyPage.vue'), meta: { auth: true } },
      {
        path: 'admin',
        component: () => import('@/views/admin/Layout.vue'),
        meta: { auth: true, admin: true },
        children: [
          { path: '', name: 'AdminDashboard', component: () => import('@/views/admin/Dashboard.vue') },
          { path: 'users', name: 'AdminUsers', component: () => import('@/views/admin/Users.vue') },
          { path: 'accounts', name: 'AdminAccounts', component: () => import('@/views/admin/Accounts.vue') },
          { path: 'orders', name: 'AdminOrders', component: () => import('@/views/admin/Orders.vue') },
          { path: 'withdrawals', name: 'AdminWithdrawals', component: () => import('@/views/admin/Withdrawals.vue') },
          { path: 'cs', name: 'AdminCs', component: () => import('@/views/admin/Cs.vue') },
          { path: 'configs', name: 'AdminConfigs', component: () => import('@/views/admin/Configs.vue') },
        ],
      },
    ],
  },
  { path: '/login', name: 'Login', component: () => import('@/views/Login.vue') },
  { path: '/register', name: 'Register', component: () => import('@/views/Register.vue') },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior: () => ({ top: 0 }),
})

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token')
  if (to.meta.auth && !token) {
    next({ path: '/login', query: { redirect: to.fullPath } })
  } else {
    next()
  }
})

export default router
