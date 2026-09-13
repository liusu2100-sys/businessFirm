import request from '@/utils/request'

export const authApi = {
  login: (data) => request.post('/auth/login', data),
  register: (data) => request.post('/auth/register', data),
  logout: () => request.post('/auth/logout'),
  me: () => request.get('/auth/me'),
}

export const userApi = {
  profile: () => request.get('/user/profile'),
  updateProfile: (data) => request.put('/user/profile', data),
  updatePassword: (data) => request.put('/user/password', data),
  balanceLog: (params) => request.get('/user/balance-log', { params }),
  withdrawal: (data) => request.post('/user/withdrawal', data),
  withdrawalList: (params) => request.get('/user/withdrawal-list', { params }),
}

export const accountApi = {
  list: (params) => request.get('/game-account/list', { params }),
  detail: (id) => request.get(`/game-account/${id}`),
  myList: (params) => request.get('/game-account/my/list', { params }),
  create: (data) => request.post('/game-account', data),
  update: (id, data) => request.put(`/game-account/${id}`, data),
  offShelf: (id) => request.put(`/game-account/${id}/off-shelf`),
  batchDelete: (ids) => request.delete('/game-account/batch', { data: { ids } }),
}

export const orderApi = {
  list: (params) => request.get('/order/list', { params }),
  create: (data) => request.post('/order', data),
  pay: (id) => request.put(`/order/${id}/pay`),
  complete: (id) => request.put(`/order/${id}/complete`),
  earlySettle: (id) => request.put(`/order/${id}/early-settle`),
  cancel: (id) => request.put(`/order/${id}/cancel`),
}

export const paymentApi = {
  list: () => request.get('/payment-account'),
  create: (data) => request.post('/payment-account', data),
  update: (id, data) => request.put(`/payment-account/${id}`, data),
  remove: (id) => request.delete(`/payment-account/${id}`),
  setDefault: (id) => request.put(`/payment-account/${id}/set-default`),
}

export const complaintApi = {
  list: (params) => request.get('/complaint', { params }),
  create: (data) => request.post('/complaint', data),
  remove: (id) => request.delete(`/complaint/${id}`),
}

export const publicApi = {
  announcements: (params) => request.get('/announcements', { params }),
  announcement: (id) => request.get(`/announcement/${id}`),
  banners: () => request.get('/banners'),
  config: () => request.get('/public/config'),
  rechargeConfig: () => request.get('/public/recharge-config'),
  stats: () => request.get('/public/stats'),
  help: () => request.get('/public/help'),
}

export const imApi = {
  conversations: () => request.get('/im/conversations'),
  messages: (params) => request.get('/im/messages', { params }),
  send: (data) => request.post('/im/message', data),
  single: (userId) => request.post('/im/single', { userId }),
  productConsult: (gameAccountId) => request.post('/im/product-consultations', { gameAccountId }),
}

export const csApi = {
  start: () => request.post('/cs/session/start'),
  list: (sessionId) => request.get('/cs/message/list', { params: { sessionId } }),
  send: (data) => request.post('/cs/message/send', data),
}

export const uploadApi = {
  image: (file) => {
    const fd = new FormData()
    fd.append('file', file)
    return request.post('/upload/image', fd)
  },
}

export const adminApi = {
  dashboard: () => request.get('/admin/dashboard'),
  users: (params) => request.get('/admin/users', { params }),
  credit: (data) => request.post('/admin/users/credit', data),
  accounts: (params) => request.get('/admin/accounts', { params }),
  auditAccount: (id, status) => request.put(`/admin/accounts/${id}/audit`, { status }),
  orders: (params) => request.get('/admin/orders', { params }),
  withdrawals: (params) => request.get('/admin/withdrawals', { params }),
  processWithdrawal: (id, data) => request.put(`/admin/withdrawals/${id}`, data),
  complaints: (params) => request.get('/admin/complaints', { params }),
  replyComplaint: (id, data) => request.put(`/admin/complaints/${id}`, data),
  announcements: () => request.get('/admin/announcements'),
  saveAnnouncement: (data, id) => id ? request.put(`/admin/announcements/${id}`, data) : request.post('/admin/announcements', data),
  deleteAnnouncement: (id) => request.delete(`/admin/announcements/${id}`),
  banners: () => request.get('/admin/banners'),
  saveBanner: (data, id) => id ? request.put(`/admin/banners/${id}`, data) : request.post('/admin/banners', data),
  deleteBanner: (id) => request.delete(`/admin/banners/${id}`),
  configs: () => request.get('/admin/configs'),
  saveConfigs: (configs) => request.post('/admin/configs', { configs }),
  csSessions: () => request.get('/admin/cs/sessions'),
}
