# 商品管理

## 安裝

### 系統需求
- Redis Server >= 6.0
- MySQL/MariaDB >= 8.0
- PHP >= 8.2

### 套件版本
- Vue.js v3.5.13
- Vite v5.4.11
- TailwindCSS v3.4.15
- Vue Router v4.5.0

### 環境變數
```
cp .env.example .env
```
```
cp .env.example .env.testing
```
### 前端
```
npm install && npm run build
```
### 後端
```
composer install
```

### 啟動廣播
```
php artisan reverb:start
```

### 測試
```
php artisan test
```
