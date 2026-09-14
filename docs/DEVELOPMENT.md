# 开发与部署

## 环境要求

- PHP 8.2+（演示环境曾用 8.4）、Composer、扩展：mbstring、xml、curl、sqlite3/mysql、zip
- Node.js 18+ / npm
- 可选：MySQL 8、Redis（当前演示未强依赖 Redis）

## 本地开发

### 后端

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

### 前端

```bash
cd frontend
npm install
npm run dev
```

访问：http://localhost:5173

Vite 已将 `/api`、`/storage`、`/uploads` 代理到 `http://127.0.0.1:8000`。

### 定时任务（可选）

```bash
php artisan orders:cancel-expired
# 或写入 cron / scheduler
```

## 切换 MySQL

`backend/.env`：

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=business_firm
DB_USERNAME=root
DB_PASSWORD=
```

然后：

```bash
php artisan migrate:fresh --seed
```

## 生产构建

```bash
cd frontend && npm run build
# 将 dist 交由 Nginx 托管，/api 反代到 php-fpm
cd backend && composer install --no-dev -o
```

建议 Nginx：

- `/` → 前端静态
- `/api` → Laravel `public/index.php`
- `/storage`、`/uploads` → 后端公开目录

## 安全注意

- 本仓库为**演示项目**，勿直接用于真实资金与生产账号交易
- 不要提交 `.env`、真实收款码、真实密钥
- 上线前需评估：实名/未成年限制、支付合规、游戏平台 ToS、越权与审计日志

## 相关文档

- [架构](./ARCHITECTURE.md)
- [API](./API.md)
- [功能清单](./FEATURES.md)
