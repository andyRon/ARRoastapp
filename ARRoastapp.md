ARRoastapp
---

https://laravelacademy.org/books/api-driven-development-laravel-vue

Laravel 10 + Vue 3

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

单页面应用（SPA）🔖在整个应用中只需要两个视图即可！一个可以展示SPA视图以及一个登录视图：

- 新增 `resources/views/app.blade.php` 视图文件
- 新增 `resources/views/login.blade.php` 视图文件

🔖 

在两个地方存放了 CSRF Token 值，一个是名为 `csrf-token` 的 meta 标签，一个是全局 JavaScript 变量 `window.Laravel`，我们会将其添加到 Axios 请求头，以便在每个请求中传递来阻止恶意请求。此外，我们还需要在所有 API 路由和 Web 路由的 `CreateFreshApiToken` 中使用 `auth:api` 中间件（下一篇教程中详细讲述），以便可以安全消费应用自己提供的 API。

`<div id="app"><router-view></router-view></div> `元素将在我们开发应用侧边栏时包含由 VueRouter 定义的路由视图。

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



本应用只允许认证用户才能访问，所以在入口路由上使用了 `auth` 中间件。

### 2 安装配置Laravel Socialite并实现基于Github的用户认证



```sh
composer require laravel/socialite
```



#### 配置 Github 认证

https://github.com/settings/developers

![](images/image-20240518094601624.png)

```
http://arroast.test/auth/github/callback
```



将新增应用的 Client ID、Client Secret 及授权回调 URL 信息添加到应用根目录下 `.env` 配置文件中。

```
GIT_CLIENT_ID=你的 Github 应用 Client ID
GIT_CLIENT_SECRET=你的 Github 应用 Client Secret
GIT_REDIRECT=你的 Github 应用授权回调URL
```

在 `config/services.php` 配置文件中新增配置：

```json
    'github' => [
        'client_id' => env('GIT_CLIENT_ID'),
        'client_secret' => env('GIT_CLIENT_SECRET'),
        'redirect' => env('GIT_REDIRECT')
    ]
```



> 上面就完成了基于 Github 登录所需的所有配置信息配置工作，其它第三方 OAuth 登录操作步骤也是与此类似。

#### 实现基于Github进行登录认证

1. 创建控制器 `/app/Http/Controllers/Web/AuthenticationController.php`

2. 在 `routes/web.php` 中注册登录路由：

```php
// 登录页面
// guest中间件的用途是登录用户访问该路由会跳转到指定认证后页面，而非登录用户访问才会显示登录页面。
Route::get("/login", [AppController::class, 'getLogin'])->name('login')->middleware('guest'); 
```

3. 修改RedirectIfAuthenticated中的重定向,`redirect('/');`

4. 定义AppController的getLogin方法

现在访问 `http://roast.test`，就会跳转到登录页面了。现在页面上只显示了一个简陋的登录链接。

5. 注册登录认证路由

```php
// 注册登录认证路由
// {social}代表所使用的OAuth提供方，比如github，Socialite会根据这个参数值去config/services.php中获取对应的OAuth配置信息。
Route::get('/auth/{social}', [AuthenticationController::class, 'getSocialRedirect'])->middleware('guest');
Route::get('/auth/{social}/callback', [AuthenticationController::class, 'getSocialCallback'])->middleware('guest');
```

6. 在AuthenticationController.php中编写具体的GitHub登录认证代码



7. 修改并运行数据库（用户表）迁移文件，添加字段：

```php
$table->string('provider')->comment('OAuth服务提供方，如github');
$table->string('provider_id')->comment('从第三方OAuth那里获取的用户唯一ID');
$table->text('avatar')->comment('从第三方OAuth获取的用户头像');
```

删除迁移文件 `CreatePasswordResetsTable` ，在本应用中用不到它。🔖

```
php artisan migrate
```



### 3 安装配置Laravel Passport

通过 [Laravel Passport](https://github.com/laravel/passport)，你可以在几分钟内搭建起一个功能完备的 OAuth 服务器，用户可以像 Github、微信、QQ、Google 那样基于你提供的 OAuth 服务登录到不同的 Web 服务。不过，我们的目标是**不同设备通过同一个入口获取同一份数据**，而这正是 API 驱动应用的强大之处。对所有数据库增删改查方法而言，数据结构和调用方法都是一样的，你可以从多个平台消费这些 API，例如移动端、Web 浏览器。



Laravel Socialite，以便用户通过社交媒体账户提供的 OAuth 服务进行登录认证。

而Laravel Passport 搭建一个自己的 OAuth 服务器，以便颁发凭证给用户，让他们可以访问自己的应用数据，比如授权登录。



```sh
composer require laravel/passport
```

#### 1️⃣安装Passport并进行数据库迁移

```sh
php artisan passport:install
```

![](images/image-20240509162844870.png)

生成安全访问令牌（token）所需的加密键，此外，该命令还会创建「personal access」和「password grant」客户端用于生成访问令牌，保存在表`oauth_clients`中。

#### 2️⃣在用户模型类中使用 HasApiTokens



#### 3️⃣在AuthServiceProvider中注册 Passport 路由

🔖

#### 4️⃣设置Passport在输入API请求中使用

做好以上初始化及配置工作后，还需要将 API 认证驱动设置为 Laravel Passport，这既能在用户通过 Session 登录访问 API 时派上用场，也能检查移动端请求头中的访问令牌。

 `config/auth.php`

```json
    'api' => [
        'driver' => 'passport',
        'provider' => 'users',
    ]
```



#### 5️⃣从 Web 浏览器访问认证 API



#### 6️⃣清理 routes/api.php 文件





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





```sh
php artisan make:model Cafe -m
```



```php
    public function up(): void
    {
        Schema::create('cafes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('address');
            $table->string('city');
            $table->string('state');
            $table->string('zip');
            $table->decimal('latitude', 11, 8);
            $table->decimal('longitude', 11, 8);
            $table->timestamps();
        });
    }
```





```sh
php artisan migrate
```

🔖



### 8 通过Axios库构建API请求
