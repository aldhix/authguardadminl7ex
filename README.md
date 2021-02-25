# Auth Guard Admin dengan Level, Laravel 7 (Example)
Project ini hanya script contoh membuat auth guard (admin) pada laravel 7.

## Instalasi
Buat dahulu database pada mysql, kemudian pada Prompt ketikan perintah :
```sh
composer create-project --prefer-dist laravel/laravel:^7.* auth-guard
cd auth-guard
composer require laravel/ui:^2.4
php artisan ui bootstrap --auth
npm install && npm run dev
```

## Buat Model & Controller
Buat Model Admin dan Controller Admin, dengan artisan :
```sh
php artisan make:model Admin -ms
php artisan make:controller AdminAuth/LoginAdminController
php artisan make:controller HomeAdminController
```
## Setting & Configurasi file
edit [file .env](https://github.com/aldhix/authguardadminl7ex/blob/main/.env) untuk mengkonfigurasi database.
edit [file config\database.php](https://github.com/aldhix/authguardadminl7ex/blob/main/config/database.php) untuk mengkonfigurasi database.
edit [file config\auth.php](https://github.com/aldhix/authguardadminl7ex/blob/main/config/auth.php) untuk menkonfigurasi guard dan provider :
```sh
'guards' => [
        ......
        'admin' => [
            'driver' => 'session',
            'provider' => 'admins',
        ],
        ......
    ],
    
'providers' => [
        ......
        'admins' => [
            'driver' => 'eloquent',
            'model' => App\Admin::class,
        ],
    ],
```

Buat [file config\admin.php](https://github.com/aldhix/authguardadminl7ex/blob/main/config/admin.php) untuk menentukan path halaman admin :
```sh
return [
	'path'=>'admin',
];
```

## Migrations & Seeder

edit [file database\migrations\...create_admins_table.php](https://github.com/aldhix/authguardadminl7ex/blob/main/database/migrations/2021_02_25_005025_create_admins_table.php) untuk membuat tabel admins.
edit [file database\seeds\AdminSeeder.php](https://github.com/aldhix/authguardadminl7ex/blob/main/database/seeds/AdminSeeder.php) menambahkan data admin degan level super dan admin dengan level admin (biasa)
edit [file database\seeds\DatabaseSeeder.php](https://github.com/aldhix/authguardadminl7ex/blob/main/database/seeds/DatabaseSeeder.php) untuk memanggil seeder AdminSeeder.

## Model 
edit [file app\Admin.php](https://github.com/aldhix/authguardadminl7ex/blob/main/app/Admin.php) menkonfigurasi class extends ke Authenticatable.

## Controller 
edit [file app\Http\Controllers\HomeAdminController.php](https://github.com/aldhix/authguardadminl7ex/blob/main/app/Http/Controllers/HomeAdminController.php) untuk membuat method index(), add(), dan profile(). Pada method add() hanya dapat di akses oleh level super karena telah disisipkan perintah :
```sh
Gate::authorize('level','super');
```
Apabila bukan level super maka akan menampilkan respon 403 (Unauthorized).
Selanjutnya pada method profile() dapat di akses oleh level super dan admin karena telah tidak atuh authoizenya seperti pada code di bawah ini :
```sh
Gate::authorize('level',['super','admin']);
```
edit [file app\Http\Controllers\AdminAuth\LoginAdminController.php](https://github.com/aldhix/authguardadminl7ex/tree/main/app/Http/Controllers/AdminAuth) untuk membuat method loginForm(), login(), dan logout().

## Middleware & Service Provider
edit [file app\Providers\AuthServiceProvider.php](https://github.com/aldhix/authguardadminl7ex/blob/main/app/Providers/AuthServiceProvider.php) untuk membuat otorasi (authorization) level.
```sh
Gate::define('level', function($user, ...$level){
    return in_array($user->level, $level);
});
```
edit [file app\Http\Middleware\Authenticate.php](https://github.com/aldhix/authguardadminl7ex/blob/main/app/Http/Middleware/Authenticate.php) mengarahkan yang tidak memiliki authentic pada path admin akan diarahkan ke halaman login admin.
```sh
if($request->is( config('admin.path').'*') ){
    return route('admin.login');
}
.....
```
edit [file app\Http\Middleware\RedirectIfAuthenticated.php](https://github.com/aldhix/authguardadminl7ex/blob/main/app/Http/Middleware/RedirectIfAuthenticated.php) mengarahkan halaman apabila memiliki authentic guard admin ke halaman home admin.
```sh
if( $guard == 'admin' ){
    return redirect()->route('admin.home');
}
```

## View
edit [file resource\views\home.blade.php](https://github.com/aldhix/authguardadminl7ex/blob/main/resources/views/home.blade.php) menambahkan perintah otorasi tampilan dengan method @can() yang sebelumnya sudah diregistrasi pada AuthServiceProvider diatas. Contoh :
```sh
@can('level','super')
<div class="alert alert-success" role="alert">
    Show for level Super
</div>
@endcan
```
pada perintah diatas akan alert akan muncul jika otorasi level adminnya super. Apabila ingin menambahkan level lebih dari satu otosainya gunakan array, sebagai contoh :
```sh
@can('level',['super','admin'])
<div class="alert alert-warning" role="alert">
    Show for level Super & Admin
</div>
@endcan
```
Dari perintah diatas dapat diketahui bahwa alert akan tampil jika dengan admin level super ataupun admin.

edit [file resource\views\auth\login.blade.php](https://github.com/aldhix/authguardadminl7ex/blob/main/resources/views/auth/login.blade.php) pada file ini hanya meminjam halaman login bawaan auth laravel, dengan mengarahkan route actionnya apabila path admin akan di arahkan ke admin login routenya.
```sh
if(request()->is( config('admin.path').'*')){
    $route = route('admin.login');
    $title = 'Login for Admin';
} else {
    $route = route('login');
    $title = 'Login';
}
```
edit [file resource\views\layouts\app.blade.php](https://github.com/aldhix/authguardadminl7ex/blob/main/resources/views/layouts/app.blade.php) tidak jauh berbeda dengan login hanya meminjam layout bawaan laravel, dan pada bagian logout akan di arahkan apabila halaman pada path admin.
```sh
if(request()->is( config('admin.path').'*')){
    $route = route('admin.logout');
} else {
    $route = route('logout');
}
```

## Route
edit [file routes\web.php](https://github.com/aldhix/authguardadminl7ex/blob/main/routes/web.php) buat route group dengan prefix sesuai config path admin. dan group middleware auth:admin agar hanya dapat akses jika berhasil login ke admin.
```sh
Route::group(['prefix' => config('admin.path') ], function() {
    Route::get('login','AdminAuth\LoginAdminController@loginForm')->name('admin.login');
    Route::post('login','AdminAuth\LoginAdminController@login');
    Route::group(['middleware' => 'auth:admin'], function() {
        Route::get('/','HomeAdminController@index')->name('admin.home');
        Route::post('logout','AdminAuth\LoginAdminController@logout')->name('admin.logout');
        Route::get('add','HomeAdminController@add');
        Route::get('profile','HomeAdminController@profile');
    });
});
```

## Run & Test
Jalan perintah migrate ke database, dan buat server
```sh
php artisan migrate:fresh
php artisan db:seed
php artisan serve
```
untuk akses halaman admin [http:/localhost:8000/admin](http:/localhost:8000/admin),
email : admin@localhost.com
password : 12345678
