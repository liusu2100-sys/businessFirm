# API 手册

Base URL：`/api`  
鉴权：除标注「公开」外，需 `Authorization: Bearer <token>`  
响应：`{ code, message, data }`

## 公开

| 方法 | 路径 | 说明 |
|---|---|---|
| POST | `/auth/login` | 登录（手机号+密码） |
| POST | `/auth/register` | 注册 |
| GET | `/game-account/list` | 商品列表（筛选/分页） |
| GET | `/game-account/{id}` | 商品详情 |
| GET | `/announcements` | 公告列表 |
| GET | `/announcement/{id}` | 公告详情 |
| GET | `/banners` | 轮播 |
| GET | `/public/config` | 站点/定价/枚举配置 |
| GET | `/public/recharge-config` | 充值相关配置 |
| GET | `/public/stats` | 统计（在售、成交等） |
| GET | `/public/help` | 帮助内容 |

> 注：实现上部分 public 路径未统一加 `public/` 前缀（如 announcements/banners），以 `backend/routes/api.php` 为准。

## 用户

| 方法 | 路径 | 说明 |
|---|---|---|
| POST | `/auth/logout` | 退出 |
| GET | `/auth/me` | 当前用户 |
| GET/PUT | `/user/profile` | 资料 |
| PUT | `/user/password` | 修改登录密码 |
| GET | `/user/balance-log` | 余额流水 |
| POST | `/user/withdrawal` | 申请提现 |
| GET | `/user/withdrawal-list` | 提现记录 |

## 商品（登录）

| 方法 | 路径 | 说明 |
|---|---|---|
| GET | `/game-account/my/list` | 我的商品 |
| POST | `/game-account` | 发布（待审） |
| PUT | `/game-account/{id}` | 更新 |
| PUT | `/game-account/{id}/off-shelf` | 下架 |
| DELETE | `/game-account/batch` | 批量删除 |

### 商品状态

| 值 | 含义 |
|---|---|
| 0 | 待审 |
| 1 | 上架 |
| 2 | 下架 |

### 主要字段（节选）

`accountNo` `loginType` `havCoin` `insuranceBox` `staminaLevel` `weightLevel` `fhLevel` `kdValue` `awmAmmo` `helmetL6` `armorL6` `slot9Card` `meleeSkins` `weaponSkins` `operatorSkins` `trainSixGrid` `tradeStartTime` `tradeEndTime` `ban90Days` `commonLoginArea` `rankLevel` `price` `deposit` `rentalDuration` `remark` `priceRatio` `rentDays` `faceIsSelf` `dailyConsume` `pic` `liquidAssets` `screenshots`

## 订单

| 方法 | 路径 | 说明 |
|---|---|---|
| GET | `/order/list` | 列表；`type=buy|sell`，可按 `status` 筛选 |
| POST | `/order` | 创建；body: `{ accountId }` |
| PUT | `/order/{id}/pay` | 支付 |
| PUT | `/order/{id}/complete` | 确认完成 |
| PUT | `/order/{id}/early-settle` | 提前结算 |
| PUT | `/order/{id}/cancel` | 取消 |

### 订单状态

| 值 | 含义 |
|---|---|
| 0 | 待支付 |
| 1 | 交易中 |
| 3 | 已取消 |
| 4 | 已退款 |
| 5 | 已完成 |

## 收款账户 / 投诉

| 方法 | 路径 | 说明 |
|---|---|---|
| GET/POST | `/payment-account` | 列表 / 新增 |
| PUT/DELETE | `/payment-account/{id}` | 更新 / 删除 |
| PUT | `/payment-account/{id}/set-default` | 设默认 |
| GET/POST | `/complaint` | 投诉列表 / 提交 |
| DELETE | `/complaint/{id}` | 删除 |

## IM / 客服 / 上传

| 方法 | 路径 | 说明 |
|---|---|---|
| GET | `/im/conversations` | 会话列表 |
| GET | `/im/messages` | 消息 |
| POST | `/im/message` | 发消息 |
| POST | `/im/single` | 发起私聊 |
| POST | `/im/product-consultations` 或 `/product-consultations` | 商品咨询 |
| POST | `/cs/session/start` | 开启客服会话 |
| GET | `/cs/message/list` | 客服消息 |
| POST | `/cs/message/send` | 发送客服消息 |
| POST | `/upload/image` | 图片上传 |

## 管理端 `/api/admin/*`（需管理员）

| 方法 | 路径 | 说明 |
|---|---|---|
| GET | `/admin/dashboard` | 仪表盘 |
| GET | `/admin/users` | 用户 |
| POST | `/admin/users/credit` | 调账（充值确认） |
| GET | `/admin/accounts` | 商品审核列表 |
| PUT | `/admin/accounts/{id}/audit` | 审核 |
| GET | `/admin/orders` | 订单 |
| GET/PUT | `/admin/withdrawals` `/admin/withdrawals/{id}` | 提现处理 |
| GET/PUT | `/admin/complaints` `/admin/complaints/{id}` | 投诉回复 |
| CRUD | `/admin/announcements` | 公告 |
| CRUD | `/admin/banners` | 轮播 |
| GET/POST | `/admin/configs` | 站点配置 |
| GET | `/admin/cs/sessions` | 客服会话（及后续回复接口以实现为准） |

完整路由以 `backend/routes/api.php` 为准。
