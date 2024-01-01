<?php

use App\Models\User;
<# CRUD controllers #>
<% for(let crud of this.project.cruds) { %>
    use <$ this.project.getApiControllersNamespace() $>\<$ crud.model.getControllerName() $>;
    <# Has Many Relationships controllers #>
    <% for(let relationship of crud.model.getHasManyRelationships()) { %>
        <% if(relationship.hasApi) { %>
            use <$ this.project.getApiControllersNamespace() $>\<$ relationship.getControllerName() $>;
        <% } %>
        <% } %>
    <###>
    <# Belong to Many Relationships controllers #>
    <% for(let relationship of crud.model.getBelongsToManyRelationships()) { %>
        <###>
        <% if(relationship.hasApi) { %>
            use <$ this.project.getApiControllersNamespace() $>\<$ relationship.getControllerName() $>;
        <% } %>
        <% } %>
<% } %>
<# Authentication Controller #>
<% if(this.generatorSettings.base.apiSanctumAuth) { %>
    use <$ this.project.getApiControllersNamespace() $>\AuthController;
<% } %>
<# Permissions module controllers #>
<% if(this.generatorSettings.modules.permissions) { %>
    use <$ this.project.getApiControllersNamespace() $>\RoleController;
use <$ this.project.getApiControllersNamespace() $>\PermissionController;
<% } %>
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

<% if(this.generatorSettings.base.apiSanctumAuth) { %>

    Route::post('/login', [AuthController::class, 'login'])->name('api.login');

    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    })->name('api.user');
    <% } %>

Route::name('api.')
<% if(this.generatorSettings.base.apiSanctumAuth) { %>
    ->middleware('auth:sanctum')
    <% } %>
    ->group(function () {

    <# Generate permissions routes #>
    <% if(this.generatorSettings.modules.permissions) { %>
        Route::apiResource('roles', RoleController::class);
        Route::apiResource('permissions', PermissionController::class);
        <% } %>

    // get current user
    Route::get('user',  fn (Request $request) => $request->user());

    <# Generate CRUD routes #>
    <% for(let crud of this.project.cruds) { %>
        <% if(crud.hasApi) { %>
            <% let controllerName = crud.model.getControllerName(); %>
        <% if(this.project.settings.routesMode == 'resource') { %>
                Route::apiResource('<$ crud.url $>', <$ controllerName $>::class);
        <% } else { %>
                <###>
                <# Cruds routes api #>
                <% for(let route of crud.getApiRoutes()) { %>
                    Route::<$ route.method $>('<$ route.endpoint $>', [<$ controllerName $>::class, '<$ route.getSuffix() $>'])->name('<$ crud.model.plural.case('paramCase') $>.<$ route.getSuffix() $>');
            <% } %>
        <% } %>

        <% for(let relationship of crud.model.getHasManyRelationships()) { %>
                <# HasMany relationships api routes #>
                <% if(relationship.hasApi) { %>
                    // <$ crud.model.name $> <$ relationship.name.case('capitalCase') $>
                    <% for(let route of relationship.getApiRoutes()) { %>
                        Route::<$ route.method $>('<$ route.endpoint $>', [<$ relationship.getControllerName() $>::class, '<$ route.getSuffix() $>'])->name('<$ crud.model.plural.case('paramCase') $>.<$ relationship.model.plural.case('paramCase') $>.<$ route.getSuffix() $>');
                <% } %>

            <% } %>
                <% } %>

        <% for(let relationship of crud.model.getBelongsToManyRelationships()) { %>
                <# BelongsToMany relationships api routes #>
                <% if(relationship.hasApi) { %>
                    // <$ crud.model.name $> <$ relationship.name.case('capitalCase') $>
                    <% for(let route of relationship.getApiRoutes()) { %>
                        Route::<$ route.method $>('<$ route.endpoint $>', [<$ relationship.getControllerName() $>::class, '<$ route.getSuffix() $>'])->name('<$ crud.model.plural.case('paramCase') $>.<$ relationship.model.plural.case('paramCase') $>.<$ route.getSuffix() $>');
                <% } %>

            <% } %>
                <% } %>

    <% } %>
        <% } %>
});

/**
 * mobile login API
 */
Route::post('/api/login', function (Request $request) {
    $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
        'device_name' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    return $user->createToken($request->device_name)->plainTextToken;
});