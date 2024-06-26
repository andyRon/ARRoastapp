ARRoastapp
---

参考：https://laravelacademy.org/books/api-driven-development-laravel-vue

Laravel 10 + Vue 3构建的前后端分离，查找本地附近咖啡烘培店和专卖店



第三方库说明：

- [laravel-vite-plugin](https://github.com/laravel/vite-plugin)插件是专为 Laravel 项目设计的，它允许 Vite 在开发过程中实时刷新静态资源文件，如 CSS 和 JavaScript。这可以显著提高开发效率，因为它允许你在不重启服务器的情况下看到代码更改的效果。
- [vitejs/plugin-vue](https://github.com/vitejs/vite-plugin-vue)是Vite的官方Vue插件，它为 Vue 项目提供了一些额外的功能，如代码分割、样式预处理等。
- [Laravel Passport](https://github.com/laravel/passport)
- Laravel Socialite
- [Foudation6](https://get.foundation/sites) 
- Vue3
- [Vue Router](https://router.vuejs.org/zh/)
- [Vuex](https://vuex.vuejs.org/zh/)

## 一、Laravel初始化

### 1 初始化Laravel单页面应用

主要功能是查找本地附近的咖啡烘培店和专卖店



```sh
composer create-project --prefer-dist laravel/laravel ARRoastapp "10.*"
```

#### 清理默认安装配置

本项目基于API驱动，不需要：

- 移除 `app/Http/Controllers/Auth` 目录，将通过 Socialite 重构用户认证功能
- 移除 `resources/views/welcome.blade.php` 文件，这个是默认的欢迎页面，不需要它

#### 新增目录

对于提供API的应用而言，可以基于API和Web将控制器进行分隔：

- 创建 `app/Http/Controllers/API` 目录来存放 API 控制器
- 创建 `app/Http/Controllers/Web` 目录来存放 Web 控制器

如果以后还会开发其它应用，比如博客、电商、社交等，也可以这么做。

#### 新增视图

单页面应用（SPA）在整个应用中只需要两个视图即可！新增：

- `resources/views/app.blade.php` 展示SPA视图
- `resources/views/login.blade.php` 登录视图

```php
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite('resources/css/app.css')

    <link rel="icon" type="image/x-icon" href="/favicon.ico">

    <title>Roast</title>

    <script type='text/javascript'>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
</head>
<body>

<div id="app">
    app....
    <router-view></router-view>
</div>

	@vite('resources/js/app.js')

</body>
</html>
```

在两个地方存放了CSRF Token值，一个是名为 `csrf-token` 的 meta 标签，一个是全局 JavaScript 变量 `window.Laravel`，会将其添加到 Axios 请求头，**以便在每个请求中传递来阻止恶意请求**。

此外，还需要在所有API路由和Web路由的`CreateFreshApiToken`中使用`auth:api`中间件（下一篇教程中详细讲述），以便可以安全消费应用自己提供的API。

`<div id="app"><router-view></router-view></div> `元素将在开发应用侧边栏时包含由`VueRouter`定义的路由视图。

所有的外部CSS和JavaScript文件都将通过Vite编译合并通过下面方式引入：

```php
@vite('resources/css/app.css')

@vite('resources/js/app.js')
```



```php
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roast</title>
</head>
<body>
<a href="/auth/github">
    Log In With Github
</a>
</body>
</html>
```

本应用通过 [Laravel Socialite](https://laravelacademy.org/post/9043.html) 实现的第三方应用登录。

#### 新增Web控制器和路由

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



#### 配置Github认证

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

6. 在`AuthenticationController.php`中编写具体的GitHub登录认证代码



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



### 3 安装配置Laravel Passport 🔖

通过[Laravel Passport](https://github.com/laravel/passport)，可以在几分钟内搭建起一个功能完备的OAuth服务器，用户可以像Github、微信、QQ、Google那样基于你提供的OAuth服务登录到不同的Web服务。不过，我们的目标是**不同设备通过同一个入口获取同一份数据**，而这正是 API 驱动应用的强大之处。对所有数据库增删改查方法而言，数据结构和调用方法都是一样的，你可以从多个平台消费这些 API，例如移动端、Web 浏览器。

Laravel Socialite是以便用户通过社交媒体账户提供的 OAuth 服务进行登录认证。

而Laravel Passport是搭建一个自己的OAuth服务器，以便颁发凭证给用户，让他们可以访问自己的应用数据，比如授权登录。

```sh
composer require laravel/passport
```

#### 1️⃣安装Passport并进行数据库迁移

```sh
php artisan passport:install
```

![](images/image-20240509162844870.png)

生成安全访问令牌（token）所需的加密键，此外，该命令还会创建「personal access」和「password grant」客户端用于生成访问令牌，保存在表`oauth_clients`中。

#### 2️⃣在用户模型类中使用HasApiTokens 



#### 3️⃣在AuthServiceProvider中注册Passport路由

在 Laravel Passport 的新版本中，没有`routes`方法来注册认证路由。Passport 的路由现在是通过 Laravel 的路由系统自动注册的，不需要手动在服务提供者中注册它们。

可以通过编辑`routes/api.php`文件来修改默认的路由。🔖

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

在访问认证 API 之前，先要在 `Http\Kernel.php` 的 `$middlewareGroups` 属性中新增中间件 `CreateFreshApiToken`：

```php
protected $middlewareGroups = [
    'web' => [

        \Laravel\Passport\Http\Middleware\CreateFreshApiToken::class,
    ],

];
```

该中间件为认证用户创建一个新的访问令牌，当认证用户发起请求时，会在请求中附加一个 JWT 令牌并允许用户访问你的 API，关于这部分的演示我们把它放到后面 Vue 部分进行。

#### 6️⃣清理 routes/api.php 文件





## 二、JavaScript初始化

### 4 配置JavaScript和SASS

[mix到vite的迁移指南](https://github.com/laravel/vite-plugin/blob/main/UPGRADE.md#migrating-from-laravel-mix-to-vite) 



#### 配置app.js



#### 配置SASS

安装 [Foudation6](https://get.foundation/sites) （世界上最先进的响应式前端框架。为适用于任何类型设备的网站快速创建原型和生产代码。）

```sh
npm install foundation-sites --save-dev
```

在app.scss中引入

```scss
@import "foundation-sites/scss/foundation";
```



#### 设置SASS目录

[Sass 指南](https://sass-guidelin.es/#the-7-1-pattern)

```scss
sass/
|
|– abstracts/
|   |– _variables.scss    # Sass Variables
|   |– _functions.scss    # Sass Functions
|   |– _mixins.scss       # Sass Mixins
|   |– _placeholders.scss # Sass Placeholders
|
|– base/
|   |– _reset.scss        # Reset/normalize
|   |– _typography.scss   # Typography rules
|   …                     # Etc.
|
|– components/
|   |– _buttons.scss      # Buttons
|   |– _carousel.scss     # Carousel
|   |– _cover.scss        # Cover
|   |– _dropdown.scss     # Dropdown
|   …                     # Etc.
|
|– layout/
|   |– _navigation.scss   # Navigation
|   |– _grid.scss         # Grid system
|   |– _header.scss       # Header
|   |– _footer.scss       # Footer
|   |– _sidebar.scss      # Sidebar
|   |– _forms.scss        # Forms
|   …                     # Etc.
|
|– pages/
|   |– _home.scss         # Home specific styles
|   |– _contact.scss      # Contact specific styles
|   …                     # Etc.
|
|– themes/
|   |– _theme.scss        # Default theme
|   |– _admin.scss        # Admin theme
|   …                     # Etc.
|
|– vendors/
|   |– _bootstrap.scss    # Bootstrap
|   |– _jquery-ui.scss    # jQuery UI
|   …                     # Etc.
|
`– main.scss              # Main Sass file
```





### 5 引入Vue3、Vue Router和Vuex

Vue3用于处理所有响应式设计和 Web 组件的构建。

[Vue Router](https://router.vuejs.org/zh/)和 Vue3一起工作用于提供路由组件，其原理是使用了**[HTML 5 History API](https://developer.mozilla.org/zh-CN/docs/Web/API/History_API)**在应用程序中创建可链接页面，不过是以单页面应用方式执行。

[Vuex](https://vuex.vuejs.org/zh/) 用于处理单页面应用数据的**状态管理系统**，它会在单一数据源（Single Source of Truth）中存放应用所使用的所有数据，这在处理大型应用程序的时候非常有用。如果你之前使用过组件和模块但没有使用过单一数据源，就需要来回传递很多属性来保持数据的同步，随着应用越来越复杂，这样的操作会越来越麻烦，使应用变得难以维护。而使用 Vuex 的话，你可以导入特定模块到组件中，这样这些组件就可以访问模块中的数据，你可以调用执行变更的动作来更新数据，以保证所有更改都被跟踪，所有数据都保持同步。

Vue为Firefox和Chrom 提供了开发工具：[vuejs/vue-devtools](https://github.com/vuejs/vue-devtools)，可以查看应用当前状态和组件数据、路由信息以及事件跟踪。

#### 1️⃣引入Vue

```js
import { createApp } from 'vue';

createApp({}).mount('#app')
```



#### 2️⃣安装Vue Router和vuex

```sh
npm install vue-router --save-dev

npm install vuex --save-dev
```



#### 3️⃣配置JavaScript目录

好的目录结构可以让项目更容易维护，也具备更好的可读性。

- 创建 `resources/assets/js/api` 目录，用于存放前端 API 路由调用
- 创建 `resources/assets/js/components` 目录，用于存放 Vue 组件
- 创建 `resources/assets/js/mixins` 目录，Vue 有一个叫做 mixins 的特性，用于定义可以在不同组件中共用的方法，从而提高代码可用性，该目录用于存放这些 mixins
- 创建 `resources/assets/js/pages` 目录，在 Vue Router 中，页面本质上也是组件，不过我喜欢将它们放到单独的目录中作为「特殊的」组件，这样更容易被找到，页面也可以包含子页面，这一点我们在后面会讲到
- 创建 `resources/assets/js/modules` 目录，用于数据存储，Vuex 将数据分割到多个组件并存放到这个目录。如果你之前使用过 Vuex，官方文档提到过要将操作、修改和获取分割到不同的目录，不过在 Vue 2 中，这些都将合并到一个模块

#### 4️⃣创建JavaScript文件

- config.js
- event-bus.js，事件总线，用于通过不同组件之间的消息传递进行通信

- routes.js，包含所有 Roast 单页面应用的前端路由
- store.js，Vuex 模块的起点，Vuex 由一个父模块和多个子模块构成，该文件包含父模块，随后我们会导入所有子模块到这个文件。





### 6 通过Vue Router配置前端路由

单页面应用的实现有赖于 [HTML 5 History API](https://developer.mozilla.org/en-US/docs/Web/API/History)， Vue Router帮我们处理了几乎所有底层操作，比如推入和弹出状态。



#### 配置路由文件

`resources/js/routes.js`

#### 添加路由

- `/` - 首页
- `/cafes` - 咖啡店列表
- `/cafes/new` - 新增咖啡店
- `/cafes/:id` - 显示单个咖啡店

```json
[
  {
    path: '/',
    name: 'home',
    component: () => import('./pages/Home.vue')
  },
  {
    path: '/cafes',
    name: 'cafes',
    component: () => import('./pages/Cafes.vue')
  },
  {
    path: '/cafes/new',
    name: 'newcafe',
    component: () => import('./pages/NewCafe.vue')
  },
  {
    path: '/cafes/:id',
    name: 'cafe',
    component: () => import('./pages/Cafe.vue')
  },
]
```

每个路由都有一个`name`，以便在应用中直接通过名字就可以访问该路由。

此外，每个路由还有一个`component`对象，用于定义渲染每个页面的Vue组件。

`:id`表示动态路由参数。

#### 添加页面组件

- `resources/js/pages/Cafe.vue`
- `resources/js/pages/Cafes.vue`
- `resources/js/pages/Home.vue`
- `resources/js/pages/NewCafe.vue`

#### 将路由导入app.js



#### 构建应用

`npm run watch`

🔖



### 7 实现Laravel后端API接口

通过Vue Router为应用添加了前端路由，现在需要为对应的页面提供数据以便渲染。需要注意的是，在**API驱动的单页面应用**中，所有数据都是通过 Ajax 异步加载的，因此，我们需要现在 Laravel 后端提供 API 接口，然后根据接口返回数据通过 Vue 进行渲染。

#### 设计路由

- `/cafes`  获取系统的咖啡店列表
- `/cafes/new`    POST 添加咖啡店
- `/cafes/:id ` 加载某个咖啡店的信息

#### 添加路由到 routes/api.php



#### 构建控制器



#### 构建模型类 Cafe.php

```sh
php artisan make:model Cafe -m
```



#### 编辑Cafe模型对应迁移文件

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



#### 完成具体三个路由方法



> ```
> php artisan make:factory CafeFactory --model=Cafe
> 
> php artisan make:seeder CafesTableSeeder
> ```
>
> ```sh
> php artisan db:seed
> ```



### 8 通过Axios库构建API请求

#### 配置 config.js 文件



#### cafe.js 

`resources/js/api/cafe.js`



#### 获取所有咖啡店



#### 获取单个咖啡店信息



#### 新增一个咖啡店





## 三、Vuex&Vue Router使用入门：表单提交  

前文同构Axios库构建了一些调用Laravel后端 API 路由的方法。这篇将从API接口获取的数据保存下来以便在单页面应用中使用，而这正是Vuex模块可以大展拳脚的地方。

### 9 构建Vuex模块

在 [Vuex文档](https://vuex.vuejs.org/zh/)中将 Vuex 定位成专为 Vue.js 应用程序开发的**状态管理**模式，它采用集中式存储管理应用的所有组件的状态，并以相应的规则保证状态以一种可预测的方式发生变化。

人话就是可以**在多个组件和页面中使用的单点数据**。原因是随着构建的应用越来越大，单页面也会变得越来越复杂，在多个地方使用数据会非常麻烦。例如，假设你有一份登录到应用的用户数据，有了Vuex模块之后，你无需通过用户名将用户作为参数传递到不同的组件，只需将其保存到Vuex模块中，然后在任何地方都可以访问这个模块来获取数据。

#### 1️⃣配置store.js



#### 2️⃣安装es6-promise支持IE数据存储

```sh
npm install es6-promise --save-dev
```

 IE 11 不支持 promise



#### 3️⃣新增数据存储器到Vue实例



#### 4️⃣新增Vuex模块cafes.js

 `resources/assets/js/modulescafes.js`：管理所有的咖啡相关数据



#### 5️⃣配置Vuex模块的state属性

Vuex模块有四个属性：[state](https://vuex.vuejs.org/zh/guide/state.html)、[actions](https://vuex.vuejs.org/zh/guide/actions.html)、[mutations](https://vuex.vuejs.org/zh/guide/mutations.html)、[getters](https://vuex.vuejs.org/zh/guide/getters.html)。

```js
import CafeAPI from '../api/cafe.js'

export const cafes = {
    state: {
      	cafes: [],
        cafe: {}
    },
    actions: {
    },
    mutations: {
    },
    getters: {
    }
}
```

在 `cafes` 模块中有两个需要跟踪的数据：咖啡店数组，以及存储单个咖啡店的对象。分别对应返回所有咖啡店和单个咖啡店的 API。

根据经验，我们经常遇到的一个问题是显示加载状态。在单页面应用中，==加载状态==至关重要。HTML/CSS 和其他页面功能通常会在向等待数据加载的用户提供不良UX的数据之前加载。对于我们在状态中跟踪的每个数据，我会**为跟踪加载状态的数据状态添加相应的变量**。这样我就可以读取这个变量来确定是否显示加载状态。 随着 Vue 被激活，数据被加载后，这个变量会更新，使用该变量的组件也会更新，并相应地显示到页面。相应的状态变量定义如下：

```js
export const cafes = {
    state: {
        cafes: [],
        cafesLoadStatus: 0,
        cafe: {},
        cafeLoadStatus: 0
    },
}
```

通常定义状态码如下：

- `status = 0` -> 数据尚未加载
- `status = 1` -> 数据开始加载
- `status = 2` -> 数据加载成功
- `status = 3` -> 数据加载失败

这样就可以基于数据加载状态在需要的时候相应的提示信息

#### 6️⃣配置Vuex模块的actions属性

`actions` 在模块中用于被调用来修改状态。

本例中会调用一个 action 用于发起 API 请求并提交 `mutations`。

在 `actions` 对象中可以添加方法来加载所有咖啡店和单个咖啡店信息：

```js
    actions: {
        loadCafes( { commit } ){
        },
        loadCafe( { commit }, data ){
        }
    }
```

 `actions` 部分有两个需要注意的地方：

- 每个方法都包含一个名为 `commit` 的析构参数，该参数通过 Vuex 传入，允许我们提交 `mutations`。你还可以传入其他的析构参数，要了解更多关于参数析构的细节，可以参考 [lukehoban/es6features](https://github.com/lukehoban/es6features#destructuring) 这个 Github 项目。
- `loadCafe` 动作包含了一个名为 `data` 的第二个参数。该参数是一个对象，包含我们想要加载的咖啡店的 ID。

```js
actions: {
    loadCafes( { commit } ){
        commit( 'setCafesLoadStatus', 1 );

        CafeAPI.getCafes()
            .then( function( response ){
                commit( 'setCafes', response.data );
                commit( 'setCafesLoadStatus', 2 );
            })
            .catch( function(){
                commit( 'setCafes', [] );
                commit( 'setCafesLoadStatus', 3 );
            });
    },

    loadCafe( { commit }, data ){
        commit( 'setCafeLoadStatus', 1 );

        CafeAPI.getCafe( data.id )
            .then( function( response ){
                commit( 'setCafe', response.data );
                commit( 'setCafeLoadStatus', 2 );
            })
            .catch( function(){
                commit( 'setCafe', {} );
                commit( 'setCafeLoadStatus', 3 );
            });

    }
},
```

`commit`函数用于提交一个 mutation。`state` 中的每个数据片段都应该有一个与之对应的 mutation。在上面两个方法中，都提交了所使用的状态的加载状态，接下来，调用 API 来加载想要加载的指定信息状态，这些 API 调用定义在 `resources/js/api/cafe.js` 文件中，之后链式调用 `then` 和 `catch` 方法，前者在 API 请求成功后调用，后者在 API 请求失败后调用，`response` 变量会传递到这两个方法，以便获取响应数据和请求头。



#### 7️⃣配置Vuex模块的mutations属性

`mutations` 定义了数据的更新方式，每个模块都有 `state`，每个 `state` 都需要对应的 mutation 来更新，完整工作流如下：

- 用户调用一个 action
- 该 action 加载/计算数据
- 该 action 提交一个 mutation
- state 被更新
- getter 将更新后的 state 返回给组件
- 组件被更新

以上工作流可以通过多种方式来实现，不过相较于 jQuery 或 vanilla JS 的实现，使用 Vuex 更加简单。

```js
    mutations: {
        setCafesLoadStatus( state, status ){
            state.cafesLoadStatus = status;
        },
        setCafes( state, cafes ){
            state.cafes = cafes;
        },
        setCafeLoadStatus( state, status ){
            state.cafeLoadStatus = status;
        },
        setCafe( state, cafe ){
            state.cafe = cafe;
        },
    },
```

所有 `mutations` 所做的工作都是设置 `state`，所以第一个参数是 `state`，这里的 `state` 是局部模块 state 而不是全局 state，所以我们在第6️⃣步中配置的 `state` 可以被访问，第二个参数是 `state` 更新后的数据。



#### 8️⃣配置Vuex模块的getters属性

前面有了想要跟踪的 `state` 数据，从 API 接口获取数据的 `actions`，以及用于设置 `state` 的 `mutations`，现在需要定义 `getters` 从模块中获取数据。

为每一个 `state` 数据的获取定义一个方法：

```js
    getters: {
        getCafesLoadStatus( state ){
            return state.cafesLoadStatus;
        },

        getCafes( state ){
            return state.cafes;
        },

        getCafeLoadStatus( state ){
            return state.cafeLoadStatus;
        },

        getCafe( state ){
            return state.cafe;
        },
    }
```

#### 9️⃣将Vuex模块添加到数据存储器



🔖



### 10 在Vue组件中使用Vuex模块❤️ 

#### 1️⃣设置Home.vue组件

在一个API驱动的单页面应用中，会首先加载HTML、CSS 和 JavaScript，这意味着**页面会在布局和样式加载之后才加载数据**。

所要做的就是绑定Home页面组件的一个声明周期钩子并加载数据.【Vue的生命周期钩子】

应用首页，需要展示咖啡店列表。

在组件创建之后绑定的声明周期钩子`created()`会被调用，将在这个钩子函数中分配加载咖啡店的动作。

#### 2️⃣分发加载咖啡店动作

由于需要加载所有咖啡店，所以需要在数据存储器上分发一个动作，有多种方式可以实现这一目的，不过这里使用在全局 Vuex 数据存储器变量 `$store` 上调用一个方法来分发指定 action（所有 `actions` 定义在 `resources/assets/js/modules/cafe.js` 文件中），加载所有咖啡店对应的 action 是 `loadCafes`，所以在生命周期钩子 `created()` 中添加如下代码：

```js
created() {
    this.$store.dispatch( 'loadCafes' );
}
```

其实现的功能是使用 Vue 存储器分发 `loadCafes` 动作来调用API、加载咖啡店、并将数据保存到 `cafes` 模块中的 `cafes` 数组。

#### 3️⃣将咖啡店添加到组件

在Vuex模块中设置的所有`getters`都会以计算属性的方式导入到Vue组件中，在应用首页，我们在希望获取已加载的咖啡店数据以及数据加载状态，以便给用户展示是否在加载数据。

要实现上述目的，需要两个计算属性，并且需要将其添加到 `Home.vue` 文件：

```js
/**
 * 定义组件的计算属性
 */
computed: {
    // 获取 cafes 加载状态
    cafesLoadStatus(){
        return this.$store.getters.getCafesLoadStatus;
    },

    // 获取 cafes
    cafes(){
        return this.$store.getters.getCafes;
    }
}
```

目前有了两个可以用来显示数据的计算属性，每个计算属性方法都会从 Vuex 模块中定义的 `getters` 里返回数据。

#### 4️⃣显示数据

```vue
<template>
    <div id="home">
        <span v-show="cafesLoadStatus == 1">加载中...</span>
        <span v-show="cafesLoadStatus == 2">数据加载成功！</span>
        <span v-show="cafesLoadStatus == 3">数据加载失败！</span>
        <ul>
            <li v-for="cafe in cafes">{{ cafe.name }}</li>
        </ul>
    </div>
</template>
```



### 11 将SASS编译到Vue组件

#### 1️⃣创建变量文件

Sass变量文件：`_variables.scss` ：

```scss
$white: #FFFFFF;
$black: #111111;

$primary-color: #7F6D50;
$secondary-color: #FFBE54;
$highlight-color: #FFDBA0;
$dark-color: #7F5F2A;
$dull-color: #CCAF80;
```

#### 2️⃣添加变量文件到app.scss



#### 3️⃣在vite.config.js中添加 



#### 4️⃣添加Navigation.vue组件 



### 12 为Vue Router添加页面布局

由于构建的是单页面应用，可以通过Vue Router来实现：创建一个**根级页面**，其中包含在每个页面都会用到的Vue组件，如Header和Footer。使用布局的另一个好处是你可以**一次加载所有你需要的Vuex数据**，它们会随着布局里的组件出现在所有子页面上。

#### 1️⃣重新组织嵌套路由



#### 2️⃣添加Layout.vue



由于已经构建好了相关 Vuex 模块，所以只需要将其绑定布局页面的 `created()` 钩子函数并加载响应数据即可。在本案例中，需要在导航组件中显示用户信息以及咖啡店列表信息，所以最终的 `Layout.vue` 代码如下：

```vue
<template>
    <div id="app-layout">
        <navigation></navigation>
        <router-view></router-view>
    </div>
</template>

<script >
import Navigation from "../components/global/Navigation.vue";
export default {
    components: {
        Navigation
    },
    created() {
        this.$store.dispatch('loadCafes')
        this.$store.dispatch('loadUser')
    }
}
</script>

<style scoped>

</style>
```

`<router-view></router-view>` 组件将用于渲染导入父组件的子组件。



#### 3️⃣调整认证控制器重定向

用户登录成功后将重定向路由修改为 `/home`



### 13 通过Vue组件、Vue Router、Vuex和Laravel实现表单提交

- 添加一个提交新咖啡店的表单到 `NewCafe.vue` 文件
- 发送请求动作到 `Cafes` 模块以便提交新咖啡店
- 通过 JavaScript API 提交新咖啡店到 Laravel API
- 将处理结果返回给前端，尤其是 Vuex 模块
- 重新加载咖啡店并更新 Vuex 模块

#### 添加表单到 NewCafe.vue 页面



#### 提交新增的咖啡店数据



#### 在 Vuex 模块中处理新增操作



🔖 调试

### 14 通过JavaScript和Laravel验证表单请求

#### 1️⃣在 NewCafe.vue 组件中添加前端校验



#### 2️⃣添加验证失败通知



#### 3️⃣构建JavaScript验证函数





#### 4️⃣为新增咖啡店构建 Laravel 请求验证类

```sh
php artisan make:request StoreCafeRequest
```



#### 5️⃣自定义验证失败消息



`StoreCafeRequest` 为例，中的 `messages()` 方法：

```php
    /**
     * 自定义验证失败消息
     * {key}.{validation} => {message}
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'name.required'     => '咖啡店名字不能为空',
            'address.required'  => '咖啡店地址不能为空',
            'city.required'     => '咖啡店所在城市不能为空',
            'state.required'    => '咖啡店所在省份不能为空',
            'zip.required'      => '咖啡店邮编不能为空',
            'zip.regex'         => '无效的邮政编码'
        ];
    }
```



#### 6️⃣添加请求验证类到控制器路由



## 四、在地图上标记咖啡店

### 15 通过高德地图Web服务API对咖啡店地址进行地理编码

打开后续各种LBS功能大门的钥匙是首先将咖啡店的**物理地址转化为地图上的经纬度格式**，这个过程叫做==地理编码（Geocode）==。

高德地图、百度地图、Google地图这些地图服务都提供了开放的 API。

[高德地图Web服务API文档](https://lbs.amap.com/api/webservice/summary/)

#### 1️⃣获取高德地图Web服务API Key

遵循高德地图提供的[获取Key](https://lbs.amap.com/api/webservice/guide/create-project/get-key)这篇文档在控制台创建一个新应用，然后点击「添加 Key」 按钮，服务平台选择「Web服务」来添加一个 Key。



#### 2️⃣添加API Key到配置文件



#### 3️⃣构建高德地图工具类



#### 4️⃣安装Guzzle HTTP扩展包

在调用高德地图 API 之前需要安装相应的网络扩展包发送 HTTP 请求。

> Guzzle 是一个**PHP HTTP客户端**，它使得发送 HTTP 请求变得简单，并且易于与 Web 服务整合。Guzzle 提供了构建查询字符串、POST 请求、大批量上传和下载的简单接口，支持上传和下载文件、使用 HTTP cookies、上传 JSON 数据等功能。此外，Guzzle 还允许使用同一个接口同时发送同步和异步请求。它使用 PSR-7 接口进行请求、响应和流操作，这使得 Guzzle 可以与其他兼容 PSR-7 的库一起使用。Guzzle 还抽离了底层的 HTTP 传输，允许编写与环境和传输无关的代码，这意味着它不依赖于 cURL、PHP 流、socket 或非阻塞事件循环。此外，Guzzle 还包含一个中间件系统，允许开发者增强和编排客户行为。

这里使用 [Guzzle HTTP](https://guzzle-cn.readthedocs.io/zh_CN/latest/index.html) ：

```sh
composer require guzzlehttp/guzzle
```



#### 5️⃣添加地理编码方法到工具类

[地理编码文档](https://lbs.amap.com/api/webservice/guide/api/georegeo)

#### 6️⃣在新增咖啡店时保存经纬度



🔖 调试

### 16 通过Vue+高德地图JS API在地图上标记咖啡店 🔖

将基于地理编码在地图上标记咖啡店。

#### 1️⃣获取高德地图JS API Key

由于是在前端实现地图标记功能，所以需要申请高德地图的 JS API Key，和之前一样新建一个key，不过选在【Web端(JS API)】。



#### 2️⃣将API Key添加到config.js中



#### 3️⃣添加高德地图脚本到app.blade.php

高德地图完整的 JS API 文档：https://lbs.amap.com/api/javascript-api/summary

```html
<!-- 引入高德地图-->
<script src="https://webapi.amap.com/maps?v=1.4.15&key=..."></script>
```

#### 4️⃣新增CafeMap组件

将以 Vue 组件方式展示地图，以便可以插入到不同页面，从而方便复用和维护。



#### 5️⃣添加高德地图到组件

在 `CafeMap.vue` 组件中初始化高德地图的绘制



#### 6️⃣添加CafeMap组件到Cafes页面



#### 7️⃣将咖啡店标记在地图上



### 17 在高德地图上自定义咖啡店点标记图标并显示信息窗体 



#### 自定义点标记图标

https://lbs.amap.com/api/javascript-api/reference/overlay#marker

`storage/app/public/img`

在 `public` 目录下创建一个软链接 `storage` 指向 `storage/app/public`: 

```sh
php artisan storage:link
```



#### 信息窗体创建及点击展示事件

[信息窗体和右键菜单-覆盖物](https://lbs.amap.com/api/javascript-api/guide/overlays/infowindow)



## 五、添加喜欢、标签功能

### 18 实现Laravel模型类之间的多对多关联及冲泡方法前端查询API

一个咖啡店可能会提供多种冲泡方法，单个冲泡方法本身也不隶属于任何咖啡店，因此咖啡店和冲泡方法是多对多的关联关系。

#### 1️⃣创建冲泡方法数据表

```sh
php artisan make:migration create_brew_methods_table
```



```sh
php artisan make:seeder BrewMethodsSeeder
```



```sh
php artisan db:seed --class=BrewMethodsSeeder
```





#### 2️⃣创建关联关系中间表

```sh
php artisan make:migration create_cafes_brew_methods_table
```



#### 3️⃣创建冲泡方法模型类

```sh
php artisan make:model BrewMethod
```

```php
class BrewMethod extends Model
{
    // 定义与 Cafe 模型间的多对多关联
    public function cafes()
    {
        return $this->belongsToMany(Cafe::class, 'cafes_brew_methods', 'brew_method_id', 'cafe_id');
    }
}
```



#### 4️⃣定义咖啡店与冲泡方法间的关联关系

```php
class Cafe extends Model
{
    // 定义与 BrewMethod 模型间的多对多关联
    public function brewMethods()
    {
        return $this->belongsToMany(BrewMethod::class, 'cafes_brew_methods', 'cafe_id', 'brew_method_id');
    }
}
```

#### 5️⃣多对多关联查询

```php
    public function getCafes()
    {
        $cafes = Cafe::with('brewMethods')->get();
        return response()->json($cafes);
    }
    public function getCafe($id)
    {
        $cafe = Cafe::where('id', '=', $id)->with('brewMethods')->first();
        return response()->json($cafe);
    }
```

使用 `with` 方法，将模型类中的关联关系方法名作为参数传入，这样对应的关联数据会以属性的方式出现在查询结果中，属性名就是 `with` 方法传入的字符串参数。

http://arroast.test/cafe/3

#### 6️⃣实现冲泡方法查询API

```sh
php artisan make:controller API/BrewMethodsController
```



前端查询代码



🔖🔖前端问题处理后再继续



### 19 通过Vue.js实现动态表单一次提交多个咖啡店位置信息 🔖

由于一个咖啡店可能有多个分店，可能需要多个位置字段（具体数目未知），因此需要一个动态表单。

#### 19.1 构思新的NewCafe.vue组件功能

新增咖啡店功能的表单只支持单个地理位置，而有些咖啡店可能散布在多个地方（可以理解为多个分店），这些分店都有一个共同的父节点（可以理解为总店），它们共享同一个咖啡店名称、网址、简介等信息。每个咖啡店，不管是总店还是分店，都会支持多个冲泡方法，最后我们会将总店和分店信息分别存储到 `cafes` 表的不同记录中，并且以某种方式进行关联。

总店和分店区别主要体现在：

- 具体地址
- 位置名称（唯一标识位置）
- 冲泡方法

在具体的提交表单中，需要提供一个添加位置的按钮来添加标识咖啡店位置的字段以及移除位置的按钮来移除与之关联的位置字段，这样，发送给服务器的数据结构也将需要做相应的调整。调整地方：

- 保存咖啡店的分发动作
- 提交咖啡店到服务器端 JavaScript API
- 更新服务器端验证逻辑来接收新的表单数据
- 修改服务器端保存数据到数据库的业务逻辑

#### 19.2 修改NewCafe.vue



#### 19.3 修改填充表单



#### 19.4 在表单中引入冲泡方法数据



#### 19.5 验证动态表单

由于可以给咖啡店添加任意数量的位置信息，因此验证动态表单最困难的部分就是不知道要验证多少数据，好在，已经准备好了 `locations` 字段及其验证规则数组来处理这些位置字段验证，为此需要重写 `validateNewCafe` 方法中的位置字段验证代码



#### 19.6 更新addCafe分发动作



#### 19.7 更新cafe.js API



#### 19.8 更新服务端验证规则



#### 19.9 保存新的表单请求数据



#### 19.10 定义咖啡店模型的父子关联

对数据表 `cafes` 的结构进行修改

```sh
php artisan make:migration added_cafe_parent_child_relationship
```



#### 19.11 添加UX特性到表单

UX（User Experience，用户体验）



#### 19.12 通过动态表单提交咖啡店





### 20 通过Laravel+Vue实现喜欢/取消喜欢咖啡店功能

#### 20.1 创建用户喜欢表

```sh
php artisan make:migration added_users_cafes_likes --create=users_cafes_likes
```



#### 20.2 构建模型间关联关系



#### 20.3 添加喜欢/取消喜欢路由及其路由



#### 20.4 添加路由到cafe.js



#### 20.5 更新Vuex Cafe模块



#### 20.6 更新 Cafe.vue 页面



#### 20.7 添加喜欢/取消喜欢咖啡店组件



#### 20.8 确保载入咖啡店时加载是否喜欢状态





### 21 咖啡店标签后端接口

#### 1️⃣创建标签表

```sh
php artisan make:migration create_tags_table --create=tags
```

#### 2️⃣创建中间表

标签、咖啡店、用户三者关联关系的中间表 `cafes_users_tags`：

```sh
php artisan make:migration create_cafes_users_tags_table --create=cafes_users_tags
```



#### 3️⃣创建标签模型类

```sh
php artisan make:model Tag
```



#### 4️⃣在咖啡店模型类中定义与标签的关联关系

还需要在咖啡店模型类 `app/Models/Cafe.php` 中定于咖啡店与标签之间的多对多关联方法 `tags`：

```php
public function tags()
{
    return $this->belongsToMany(Tag::class, 'cafes_users_tags', 'cafe_id', 'tag_id');
}
```

这样，就可以在查询咖啡店时获取咖啡店的标签了。



#### 5️⃣定义标签路由及其方法



#### 6️⃣标签搜索

```sh
php artisan make:controller API/TagsController
```



#### 7️⃣更新新增咖啡店处理方法



### 22 咖啡店标签前端输入及显示

### 标签输入组件



#### 在新增咖啡店页面引入标签输入组件



#### 在咖啡店详情页显示标签 🔖



## 六、实现数据筛选功能

### 23 通过Vue Mixins在前端首页对咖啡店进行过滤筛选

随着咖啡店数量的增多，需要按照指定条件对咖啡店进行过滤筛选，才能找到心仪的咖啡店。由于我们现在在应用首页已经将所有咖啡店数据一次性返回了，所以现在我们在前端基于 Vue 对咖啡店进行过滤，当然，随着数据的进一步增大，筛选过滤功能必须集合后端 API 实现，但是对于目前数据量来说，前端处理就可以了。

基于以下几个维度对数据进行过滤：

- 文本搜索
- 标签选择
- 是否是烘焙店
- 冲泡方法



#### 文本过滤处理函数

筛选的文本字段包括咖啡店名称、位置名称、地址、城市及省份，如果以上任意字段包含筛选文本，则该咖啡店会显示到筛选结果中，否则不显示（如果文本筛选字段为空，则显示咖啡店）

`resources/js/mixins/filters/CafeTextFilter.js`

#### 标签过滤处理函数



#### 是否是烘焙店过滤处理函数



#### 冲泡方法过滤处理函数



#### 创建过滤器Vue组件



#### 文本搜索框



#### 标签输入框



#### 添加是否是烘焙店筛选条件



#### 冲泡方法复选组件



#### 发送过滤条件变更事件



#### 显示/隐藏咖啡店过滤组件

🔖类中不同fair

#### 添加咖啡店过滤组件到首页 🔖





#### 在首页组件实现筛选结果显示 



🔖



### 24 使用Vue Mixins在高德地图上对咖啡店点标记进行筛选过滤 🔖

#### 创建高德地图过滤组件

 类似`CafeFilter`组件，为地图上的咖啡店标记创建过滤组件



#### 添加文本过滤器到组件



#### 添是否是烘焙店过滤器



#### 添加冲泡方法过滤器到组件



#### 完成地图过滤功能



#### 在CafeMap中监听过滤条件变更





#### 实现processFilters方法







### 25 优化高德地图多个点标记信息窗体显示&引入Google Analytics进行单页面应用访问统计 🔖

#### 优化多点标记高德地图信息窗体显示





## 七、前端用户认证

### 26 根据是否需要登录重新组织后端路由

#### 重新组织API路由

对应用而言，一般首页、列表页、详情页都应该是可以公开访问的，只有新增、编辑、喜欢、取消喜欢等与用户相关的功能需要登录后才能访问。



#### 移除老的登录界面 🔖



#### 移除`/`路由上的认证中间件 🔖



### 27 通过Vue组件实现单页面应用无跳转登录 🔖

#### 前端默认重定向到首页



#### 为用户登录创建模态框组件



#### 引入 LoginModal.vue



#### 调整需要登录后显示的视图





### 28 通过Vuex + Vue Router导航守卫在前端实现认证路由保护







## 八、编辑用户信息

### 29 实现用户个人信息编辑

为ARRoast应用添加个人信息编辑页用于完善用户个人信息，以便附近有新咖啡店，或者某个咖啡店新增了用户最喜欢的冲泡方法时通知用户，此外，收集个人信息还可以为用户及朋友推荐附近的咖啡店，从而逐渐形成一个咖啡社区。基于以上种种功能，需要收集以下用户信息：

- 最喜欢的咖啡类型
- 口味记录
- 是否公开用户信息
- 位置信息

#### 完善用户信息表

```sh
php artisan make:migration alter_users_add_profile_fields --table=users
```



#### 新增处理用户更新路由



#### 更新用户信息请求验证

```sh
php artisan make:request EditUserRequest
```



#### 实现更新个人信息方法



#### 前端表单提交功能实现

之前[新增咖啡店](https://laravelacademy.org/post/9618.html)的实现思路一致：在 Vuex 模块中定义 Action、Mutation 和 Getter，在 Vue Router 中新增一个路由，然后在个人信息编辑页面组件中使用 Vuex 获取数据并提交 Action，最终通过在 Action 中调用的 Axios 发送请求到后端 API 接口实现表单提交。



#### 添加链接指向个人信息编辑页



### 30 通过Laravel + Vue实现文件上传

#### 创建存储文件表

```sh
php artisan make:migration create_cafes_photos_table
```



#### 在模型类中定义关联关系

```sh
php artisan make:model CafePhoto
```



#### 创建图片存放目录

`storage/app/public/photos`

#### 调整前端添加咖啡店 API 调用方法



#### 更新Vuex Action传递图片参数



#### 更新新增咖啡店表单允许上传图片



#### 修改后端 API 处理图片上传🔖🔖





## 九、应用代码重构🔖

功能模块重构&CSS整体优化

### 31 首页篇 

规划下对应用哪几块功能进行重构：

- 将咖啡店列表页合并到首页
- 移除信息窗体功能，点击咖啡店标记直接跳转到对应的咖啡店详情页
- 移除标签和标签过滤器（暂时）
- 移除文件上传功能，将其替换为上传咖啡店 Logo
- 将是否是烘焙店替换为咖啡店类型字段
- 将之前的咖啡店总店概念整合到所属公司，分店打平，将对应的公有字段也移到公司表中
- 有了上面的基础，在新增咖啡店页面，现在一次只能添加一个咖啡店
- 编辑咖啡店功能实现
- 应用 CSS 整体优化

遵循之前的开发流程==「数据表 -> 模型类-> 路由 -> 控制器-> 前端调用API -> Vuex -> Vue Router -> Vue组件 -> CSS」==，从应用首页着手，对应用进行重构。

#### 31.1 数据表调整

```sh
php artisan make:migration create_companies_table --create=companies

php artisan make:migration create_cities_table --create=cities
```



```sh
php artisan make:migration alter_cafes_drop_company_columns --table=cafes
```



用户与公司之间的关系

```sh
php artisan make:migration create_company_owners_table --create=company_owners
```



```sh
php artisan make:migration alter_cafes_add_city_id --table=cafes
```



```sh
php artisan make:migration alter_companies_add_subscription --table=companies
php artisan make:migration add_brew_methods_icon --table=brew_methods
```



#### 31.2 模型类调整

```sh
php artisan make:model Company
php artisan make:model City
```





#### 31.3 后端路由及控制器实现

```sh
php artisan make:controller API/CitiesController
```

#### 31.4 新增前端API调用文件

`resources/js/api/cities.js `



#### 31.5 新增/调整Vuex模块

`resources/js/modules/cities.js`

`resources/js/modules/display.js`

`resources/js/modules/filters.js`



调整 `resources/js/modules/cafes.js`



修改`resources/js/store.js`



#### 调整Vue Router 🔖



重构 Layout 组件
重构 Home 组件
优化全局 CSS





### 32 新增咖啡店篇 🔖

#### 数据表迁移

```sh
php artisan make:migration alter_cafes_add_matcha_tea --table=cafes
```



🔖

### 33 实现编辑/删除咖啡店功能



### 34 咖啡店详情页篇



### 35 通过Vue Transitions实现Vue组件的CSS动画效果&若干Bug修复





## 十、构建后台管理系统

### 36 基于RBAC的咖啡店增删改查权限管理功能

#### 1️⃣设置用户角色类型

```sh
php artisan make:migration alter_users_add_permission --table=users
```

 `permission` 字段定义四种角色类型：

- `3` – 超级管理员，具备所有权限
- `2` – 管理员，具备后台管理权限和咖啡店增删改权限
- `1` – 商家，具备对自有咖啡店和对应公司的更新权限
- `0` – 普通用户，具备更新个人信息、喜欢及咖啡店浏览权限。默认

为了简化应用流程，`permission` 既承担了用户角色功能，又承担了对应的权限功能，将基于这个字段实现简单的 RBAC 权限管理功能。



#### 2️⃣在模型类中定义常量属性



#### 3️⃣实现咖啡店策略类

将基于 [Laravel 自带授权功能中的策略类](https://laravelacademy.org/post/8916.html#toc-4)结合用户实例上的 `permission` 字段实现简单的 RBAC 权限管理，创建针对咖啡店权限的策略类：

```sh
php artisan make:policy CafePolicy
```



#### 4️⃣实现咖啡店增删改查授权功能

 `actions` 表，用于存储待审核/已处理动作记录

```sh
php artisan make:migration create_actions_table --create=actions
```



```sh
php artisan make:model Action
```



#### 5️⃣修改前端代码🔖



### 37 后台管理后端动作审核接口实现 

#### 1️⃣新增拦截非后台管理员的中间件

```sh
php artisan make:middleware Owner
```



#### 2️⃣初始化控制器方法

```sh
php artisan make:controller API/Admin/ActionsController
```



#### 3️⃣注册管理后台路由



#### 4️⃣实现获取待审核动作方法



#### 5️⃣创建 Action Policy

创建用于动作审核授权的 Action 策略类，以判断用户是否有操作权限

```sh
php artisan make:policy ActionPolicy
```



#### 6️⃣实现通过动作审核方法





#### 7️⃣实现拒绝动作审核方法







### 38 基于Vue Router路由元信息实现前端路由权限判断





### 39 管理后台前端动作审核列表页面功能实现



### 40 管理后台新增公司管理、用户管理、城市管理、冲泡方法管理等功能



## 补充

### 基于 Laravel Mix + Vue Router 路由懒加载实现单页面应用 JS 文件按组件分割



### bug和待添加功能

- [ ] 其它第三方登录（如微博、qq）

- [ ] 2中基于GitHub登录认证流程图

  

  
