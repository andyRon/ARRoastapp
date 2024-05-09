ARRoastapp
---

https://laravelacademy.org/books/api-driven-development-laravel-vue

## 一 Laravel初始化

### 1 初始化Laravel单页面应用

主要功能是查找本地附近的咖啡烘培店和专卖店



```sh
composer create-project --prefer-dist laravel/laravel ARRoastapp "10.*"
```



#### 清理默认安装配置

- 移除 `app/Http/Controllers/Auth` 目录，将通过 Socialite 重构用户认证功能
- 移除 `resources/views/welcome.blade.php` 文件，这个是默认的欢迎页面，不需要它

#### 新增目录

对于提供 API 的应用而言，我们可以基于 API 和 Web 将控制器进行分隔：

- 创建 `app/Http/Controllers/API` 目录来存放 API 控制器
- 创建 `app/Http/Controllers/Web` 目录来存放 Web 控制器

如果以后还会开发其它应用，比如博客、电商、社交等，也可以这么做。

#### 新增视图

单页面应用（SPA）在整个应用中只需要两个视图即可！一个可以展示 SPA 视图以及一个登录视图：

- 新增 `resources/views/app.blade.php` 视图文件
- 新增 `resources/views/login.blade.php` 视图文件

🔖

#### 新增 Web 控制器和路由

```php
<?php

namespace app\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class AppController extends Controller
{
    public function getApp()
    {
        return view('app');
    }
}
```



```php
Route::get('/', [AppController::class, 'getApp'])->middleware('auth');
```



### 2 安装配置Laravel Socialite并实现基于Github的用户认证



```sh
composer require laravel/socialite
```





```
http://arroast.test/auth/github/callback
```



### 3 安装配置Laravel Passport

Laravel Socialite，以便用户通过社交媒体账户提供的 OAuth 服务进行登录认证。

而Laravel Passport 搭建一个自己的 OAuth 服务器，以便颁发凭证给用户，让他们可以访问自己的应用数据，比如授权登录。

```sh
composer require laravel/passport
```



```sh
php artisan passport:install
```

![](images/image-20240509162844870.png)

生成安全访问令牌（token）所需的加密键，此外，该命令还会创建「personal access」和「password grant」客户端用于生成访问令牌



#### 在 AuthServiceProvider 中注册 Passport 路由

🔖



## 二 JavaScript初始化

### 4 配置JavaScript和SASS

[mix到vite的迁移指南](https://github.com/laravel/vite-plugin/blob/main/UPGRADE.md#migrating-from-laravel-mix-to-vite) 





```sh

npm install foundation-sites --save-dev
```





### 5 引入Vue3、Vue Router和Vuex

```sh
npm install vue-router --save-dev

npm install vuex --save-dev
```



### 6 通过Vue Router配置前端路由

🔖



### 7 实现Laravel后端API接口

#### 设计路由

- `/cafes`  获取系统的咖啡店列表
- `/cafes/new`    POST 添加咖啡店
- `/cafes/:id ` 加载某个咖啡店的信息
