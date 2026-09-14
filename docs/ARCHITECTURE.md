# 架构说明

## 项目定位

`businessFirm`（鼠鼠商行）是对 [sssh68.shop](https://sssh68.shop/) 的**功能级复刻演示**：三角洲行动账号**租赁**中介平台（押金 + 租金 + 平台钱包担保），而非纯买断商城。

视觉先对齐原站交互与中文文案，便于对照；品牌可后续换皮。

## 目录结构

```
businessFirm/
├── README.md                 # 快速开始
├── docs/                     # 项目文档（本目录）
│   ├── ARCHITECTURE.md
│   ├── API.md
│   ├── FEATURES.md
│   └── DEVELOPMENT.md
├── frontend/                 # Vue 3 SPA
└── backend/                  # Laravel API
```

## 技术栈

| 层 | 技术 |
|---|---|
| 前端 | Vue 3、Vue Router、Pinia、Element Plus、Vite、Axios |
| 后端 | Laravel（PHP 8.x）、Laravel Sanctum |
| 数据 | 默认 SQLite；迁移兼容 MySQL |
| 文件 | `storage` / `public/uploads`（本地演示） |

## 运行时拓扑

```
浏览器 :5173 (Vite)
   │  /api  /storage  /uploads  代理
   ▼
Laravel :8000
   │
   ▼
SQLite / MySQL
```

## 鉴权

- 登录后下发 Sanctum Personal Access Token
- 前端存 `localStorage.token`
- 请求头：`Authorization: Bearer <token>`
- 需登录路由由前端 `meta.auth` + 后端 `auth:sanctum` 双重约束
- 管理接口额外 `AdminMiddleware`（用户 `is_admin`）

## 统一响应

```json
{ "code": 200, "message": "success", "data": {} }
```

业务失败：`code != 200` + `message`；未登录多为 HTTP 401。

## 核心领域

- **User**：余额、资料、角色（普通用户 / 管理员）
- **GameAccount**：待审 / 上架 / 下架；三角洲属性字段 + 配置驱动枚举
- **Order**：待支付 → 交易中 → 完成 / 取消 / 退款；支持提前结算
- **Wallet**：`balance_logs` 记录每一笔变动；提现审核；管理端调账（模拟充值确认）
- **IM / CS**：买卖家会话、商品咨询、全站客服会话
- **Ops**：公告、轮播、`site_configs`（等价原站 `/public/config`）

## 资金流转（简述）

1. 管理端给用户调增余额（演示「扫码充值后客服确认」）
2. 买家下单，支付时冻结/扣减 **租金 + 押金**
3. 订单完成：卖家到账（扣平台佣金），押金按规则退回买家
4. 卖家可申请提现，管理端审核

超时未支付订单可用：

```bash
php artisan orders:cancel-expired
```

## 配置驱动

大量筛选项、皮肤/段位/登录方式、佣金率、提现费率等来自 `site_configs`（接口 `GET /api/public/config`），前端首页与发布页应读取配置，避免写死枚举。
