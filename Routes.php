<?php

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
<% for(let crud of this.project.getMainCruds()) { %>
use <$ this.project.getControllersNamespace() $>\<$ crud.model.getControllerName() $>;
<% } %>
<###>
<% if(this.generatorSettings.modules.permissions) { %>
use <$ this.project.getControllersNamespace() $>\RoleController;
use <$ this.project.getControllersNamespace() $>\PermissionController;
<% } %>
<###>
<% if(this.generatorSettings.modules.uiTemplate && this.project.usesBreezeTemplate()) { %>
use App\Http\Controllers\ProfileController;
<% } %>

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/dashboard');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

<# Generate the specific routes when it uses LaravelUI templates #>
<% if(this.generatorSettings.modules.uiTemplate && this.project.usesLaravelUiTemplate()) { %>
Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');
<% } %>

<# Generate the specific routes when it uses Jetstream templates #>
<% if(this.generatorSettings.modules.uiTemplate && this.project.usesJetstreamTemplate()) { %>
Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');
<% } %>

<# Generate the specific routes when it uses Breeze templates #>
<% if(this.generatorSettings.modules.uiTemplate && this.project.usesBreezeTemplate()) { %>
Route::middleware(['auth'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

require __DIR__ . '/auth.php';

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
<% } %>

Route::prefix('/')
    <% if(this.project.usesJetstreamTemplate()) { %>
    ->middleware(['auth:sanctum', 'verified'])
    <% } else { %>
    ->middleware('auth')
    <% } %>
    ->group(function () {
    
<% if(this.generatorSettings.modules.permissions) { %>
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
<% } %>

<# Generate CRUD routes #>
<% for(let crud of this.project.getMainCruds()) { %>
    <# VARIABLES #>
    <% let controllerName = crud.model.getControllerName(); %>
    <% let crudModelNameCamelCase = crud.model.name.case('camelCase') %>
    <% let crudModelPluralParamCase = crud.model.plural.case('paramCase') %>
    <###>
    <% if(this.project.settings.routesMode == 'resource' && !crud.model.pluralAndSingularAreSame) { %>
    Route::resource('<$ crud.url $>', <$ controllerName $>::class);
    <% } else { %>
    Route::get('<$ crud.url $>', [<$ controllerName $>::class, 'index'])->name('<$ crudModelPluralParamCase $>.index');
    Route::post('<$ crud.url $>', [<$ controllerName $>::class, 'store'])->name('<$ crudModelPluralParamCase $>.store');
    Route::get('<$ crud.url $>/create', [<$ controllerName $>::class, 'create'])->name('<$ crudModelPluralParamCase $>.create');
    Route::get('<$ crud.url $>/{<$ crudModelNameCamelCase $>}', [<$ controllerName $>::class, 'show'])->name('<$ crudModelPluralParamCase $>.show');
    Route::get('<$ crud.url $>/{<$ crudModelNameCamelCase $>}/edit', [<$ controllerName $>::class, 'edit'])->name('<$ crudModelPluralParamCase $>.edit');
    Route::put('<$ crud.url $>/{<$ crudModelNameCamelCase $>}', [<$ controllerName $>::class, 'update'])->name('<$ crudModelPluralParamCase $>.update');
    Route::delete('<$ crud.url $>/{<$ crudModelNameCamelCase $>}', [<$ controllerName $>::class, 'destroy'])->name('<$ crudModelPluralParamCase $>.destroy');
    
    <% } %>
<% } %> 
});