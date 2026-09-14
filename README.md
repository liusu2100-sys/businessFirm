# 鼠鼠商行 (businessFirm)

三角洲行动账号**租赁**平台功能复刻（演示）—— Vue 3 + Laravel API。

> 对标参考站：[sssh68.shop](https://sssh68.shop/)（代售名义，实际为租赁中介 + 钱包担保）。本仓库为学习/演示用途，请勿用于违规或未合规的真实交易。

## 文档

| 文档 | 说明 |
|---|---|
| [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md) | 架构、目录、资金流、配置驱动 |
| [docs/API.md](docs/API.md) | 接口一览与状态枚举 |
| [docs/FEATURES.md](docs/FEATURES.md) | 功能清单与完成度 |
| [docs/DEVELOPMENT.md](docs/DEVELOPMENT.md) | 环境、启动、MySQL、部署注意 |

## 技术栈

- **前端**: Vue 3 + Vue Router + Pinia + Element Plus + Vite + Axios（`frontend/`）
- **后端**: Laravel (PHP 8.x) + Sanctum（`backend/`）
- **数据库**: 默认 SQLite（迁移兼容 MySQL）

## 快速启动

### 1. 后端

```bash
cd backend
cp .env.example .env
composer install
php artisan key:generate
touch database/database.sqlite
php artisan migrate:fresh --seed
php artisan storage:link
php artisan serve --host=0.0.0.0 --port=8000
```

可选：取消超时未支付订单

```bash
php artisan orders:cancel-expired
```

### 2. 前端

```bash
cd frontend
npm install
npm run dev
```

浏览器打开 http://localhost:5173（Vite 已代理 `/api`、`/storage`、`/uploads` → `:8000`）

## 演示账号

| 角色 | 手机号 | 密码 |
|------|--------|------|
| 管理员 | 13800000000 | password123 |
| 卖家 | 13800000001 | password123 |
| 买家（余额 2000） | 13800000002 | password123 |
| 卖家2 | 13800000003 | password123 |

管理后台：登录管理员后访问 `/admin`。

## 主要功能

- C 端：首页筛选、租号大厅、详情下单支付、发布账号、个人中心
- 客服：右下角浮动客服 + 管理端回复
- 钱包：余额支付、订单完成分账/退押金、提现审核、管理员调账
- 运营后台：仪表盘、用户调账、账号审核、订单、提现、客服、配置

完成度与待办见 [docs/FEATURES.md](docs/FEATURES.md)。

## API 约定

- 前缀 `/api`
- 响应 `{ code: 200, message, data }`
- 鉴权：`Authorization: Bearer <token>`，前端存 `localStorage.token`

详情见 [docs/API.md](docs/API.md)。

## MySQL（可选）

见 [docs/DEVELOPMENT.md](docs/DEVELOPMENT.md)。

## 说明

- 演示项目，图片为占位图，勿用于生产。
- 请勿提交真实密钥；仓库应只含 `.env.example`。
