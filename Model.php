<?php
<# TEMPLATE VARIABLES #>
<% let isAuthModel = this.model.isAuthModel() %>
<% let modelNamePascalCase = this.model.name.case('pascalCase') %>
<% let modelsNamespace = this.projectHelper.getModelsNamespace() %>
<% let jetstreamEnabled = this.generatorSettings.modules.uiTemplate && this.project.settings.uiTemplate === 'jetstream' %>
<####>

namespace <$ modelsNamespace $>;

<% if(this.project.laravelVersion > 7) { %>
use Illuminate\Database\Eloquent\Factories\HasFactory;
<% } %>
<# --- #>
<# Add use statements if it is an auth model #>
<% if(isAuthModel) { %>
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
<% if(this.generatorSettings.modules.permissions) { %>
use Spatie\Permission\Traits\HasRoles;
<% } %>
<# --- #>
<% if(this.generatorSettings.base.apiSanctumAuth) { %>
use Laravel\Sanctum\HasApiTokens;
<% } %>
<# --- #>
<% if(jetstreamEnabled) { %>
use Laravel\Sanctum\HasApiTokens;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Fortify\TwoFactorAuthenticatable;
<% } %>
<# --- #>
<% } else { %>
use Illuminate\Database\Eloquent\Model;
<% } %>
<% if(this.model.softDeletes) { %>
use Illuminate\Database\Eloquent\SoftDeletes;
<% } %>
use <$ modelsNamespace $>\Scopes\Searchable;
<# --- #>
<% if(this.model.hasUuidPrimaryKey()) { %>
    <% let correctUuidNamespace = this.project.laravelVersion < 9 ? 'App\\Traits\\HasUUID' : 'Illuminate\\Database\\Eloquent\\Concerns\\HasUuids' %>
    <###>
use <$ correctUuidNamespace $>;
<% } %>
<# --- #>

<% if(isAuthModel) { %>
class <$ modelNamePascalCase $> extends Authenticatable implements MustVerifyEmail {
<% } else { %>
class <$ modelNamePascalCase $> extends Model {
<% } %>
    <% if(isAuthModel) { %>
    use Notifiable;
    <% if(this.generatorSettings.modules.permissions) { %>
    use HasRoles;
    <% } %>
    <% if(this.generatorSettings.base.apiSanctumAuth) { %>
    use HasApiTokens;
    <% } %>
    <% if(jetstreamEnabled) { %>
    use HasApiTokens;
    use HasProfilePhoto;
    use TwoFactorAuthenticatable;
    <% } %>
    <% } %>
    <% if(this.model.softDeletes) { %>
    use SoftDeletes;
    <% } %>
    <% if(this.model.hasUuidPrimaryKey()) { %>
        <% let correctUuidTrait = this.project.laravelVersion < 9 ? 'HasUUID' : 'HasUuids' %>
        <###>
    use <$ correctUuidTrait $>;
    <% } %>
    <% if(this.project.laravelVersion > 7) { %>
    use HasFactory;
    <% } %>
    use Searchable;
    <% if(this.project.settings.massAssignment == 'guarded') { %>

    protected $guarded = [
        <# Model fields #>
        <% for (let field of this.model.guardedFields()) { %>
            <% if(!field.isPrimaryKey()) { %>
            '<$ field.name $>',
            <% } %>
        <% } %>
    ];
    <% } else { %>
    
    protected $fillable = [
        <# Model fields #>
        <% for (let field of this.model.fillableFields()) { %>
            <% if(!field.isPrimaryKey()) { %>
            '<$ field.name $>',
            <% } %>
        <% } %>
    ];
    <% } %>

    protected $searchableFields = ['*'];

    <% if(!this.model.tableNameFollowsStandard()) { %>
    protected $table = '<$ this.model.table $>';
    <% } %>

    <% if(!this.model.timestamps) { %>
    public $timestamps = false;
    <% } %>

    <% if(this.model.hasHiddenFields()) { %>
    protected $hidden = [
        <# Model fields #>
        <% for (let field of this.model.hiddenFields()) { %>
        '<$ field.name $>',
        <% } %>
    ];
    <% } else { this.removeLastLineBreak() } %>

    <% if(this.model.hasCastFields()) { %>
    protected $casts = [
        <# Model fields #>
        <% for (let field of this.model.validFields()) { %>
            <% if (field.type == "boolean") { %>
            '<$ field.name $>' => 'boolean',
            <% } %>
            <% if (field.type == "json") { %>
            '<$ field.name $>' => 'array',
            <% } %>
            <% if (field.type == "date") { %>
            '<$ field.name $>' => 'date',
            <% } %>
            <% if (field.isDateTime()) { %>
            '<$ field.name $>' => 'datetime',
            <% } %>
        <% } %>
    ];
    <% } else { this.removeLastLineBreak() } %>

    <% if(this.model.basicRelationships.length) { %>
        <# Relationships #>
        <% for (let relationship of this.model.basicRelationships) { %>
            <# Belongs To #>
            <% if(relationship.type == "belongsTo") { %>
            public function <$ relationship.name $>() 
            {
                return $this->belongsTo(<$ relationship.model.name $>::class
                <# Check if foreign key or parent key are different #>
                <up if(relationship.hasDifferentForeignOrParentKey()) { up>
                , '<$ relationship.foreignKey.name $>'
                <up } up>
                <# --- #>
                <up if(relationship.hasDifferentParentKey()) { up>
                , '<$ relationship.parentKey.name $>'
                <up } up>);
            }

            <% } %>
            <# Has Many #>
            <% if(relationship.type == "hasMany") { %>
            public function <$ relationship.name $>() 
            {
                return $this->hasMany(<$ relationship.model.name $>::class
                <# Check if foreign key or parent key are different #>
                <up if(relationship.hasDifferentForeignOrParentKey()) { up>
                , '<$ relationship.foreignKey.name $>'
                <up } up>
                <# --- #>
                <up if(relationship.hasDifferentParentKey()) { up>
                , '<$ relationship.parentKey.name $>'
                <up } up>);
            }

            <% } %>
            <# Has One #>
            <% if(relationship.type == "hasOne") { %>
            public function <$ relationship.name $>() 
            {
                return $this->hasOne(<$ relationship.model.name $>::class
                <# Check if foreign key or parent key are different #>
                <up if(relationship.hasDifferentForeignOrParentKey()) { up>
                , '<$ relationship.foreignKey.name $>'
                <up } up>
                <# --- #>
                <up if(relationship.hasDifferentParentKey()) { up>
                , '<$ relationship.parentKey.name $>'
                <up } up>);
            }

            <% } %>

        <% } %>
    <% } else { this.removeLastLineBreak() } %>

    <% if(this.model.manyToManyRelationships.length) { %>
        <# Relationships #>
        <% for (let relationship of this.model.manyToManyRelationships) { %>
            <# Belongs To Many #>
            <% if(relationship.type == "belongsToMany") { %>
            public function <$ relationship.name $>() 
            {
                return $this->belongsToMany(<$ relationship.model.name $>::class
                <# Needs to add the pivot table if localKey or foreignKey are not default #>
                <up if(relationship.needsToAddPivotToModelTemplate()) { up>
                , '<$ relationship.pivot.table $>'
                <up } up>
                <# --- #>
                <up if(relationship.hasDifferentLocalOrRelatedModelKeys()) { up>
                , '<$ relationship.localModelKey.name $>'
                <up } up>
                <# --- #>
                <up if(relationship.hasDifferentModelKey()) { up>
                , '<$ relationship.modelKey.name $>'
                <up } up>);
            }

            <% } %>
        <% } %>
    <% } else { this.removeLastLineBreak() } %>

    <% if(this.model.morphRelationships.length) { %>
        <# Morph Relationships #>
        <% for (let relationship of this.model.morphRelationships) { %>
        public function <$ relationship.name $>() 
        {
            return $this-><$ relationship.type $>(<$ relationship.model.name $>::class, '<$ relationship.morphTo $>');
        }

        <% } %>
    <% } else { this.removeLastLineBreak() } %>

    <% if(this.model.hasManyThroughRelationships.length) { %>
        <# Has Many Through Relationships #>
        <% for (let relationship of this.model.hasManyThroughRelationships) { %>
        public function <$ relationship.name $>() 
        {
            return $this-><$ relationship.type $>(<$ relationship.related.name $>::class, <$ relationship.through.name $>::class
            <up if(relationship.hasDifferentRelatedModelKeyName()) { up>
            , '<$ relationship.relatedModelKeyOriginalName $>'
            <up } up>
            <up if(relationship.hasDifferentThroughModelKeyName()) { up>
            , '<$ relationship.throughModelKeyOriginalName $>'
            <up } up>);
        }

        <% } %>
    <% } else { this.removeLastLineBreak() } %>

    <% let basicInverseMorphs = this.model.getBasicMorphInverseRelationships() %>
    <% if(basicInverseMorphs.length) { %>
        <# Inverse Morph Relationships #>
        <% for (let relationship of basicInverseMorphs) { %>
        public function <$ relationship.morphTo $>() 
        {
            return $this->morphTo();
        }

        <% } %>
    <% } else { this.removeLastLineBreak() } %>

    <% let manytoManyInverseMorphs = this.model.getManyToManyMorphRelatedRelationships() %>
    <% if(manytoManyInverseMorphs.length) { %>
        <# Inverse Morph Relationships #>
        <% for (let relationship of manytoManyInverseMorphs) { %>
        <% let relLocalModel = relationship.localModel %>
        public function <$ relLocalModel.plural.case('camelCase') $>() 
        {
            return $this->morphedByMany(<$ relLocalModel.name $>::class, '<$ relationship.morphTo $>');
        }

        <% } %>
    <% } else { this.removeLastLineBreak() } %>

    <% if(isAuthModel) { %>
        <% if(this.generatorSettings.modules.permissions) { %>
        public function isSuperAdmin(): bool
        {
            return $this->hasRole('super-admin');
        }
        <% } else { %>
        public function isSuperAdmin(): bool
        {
            return in_array($this->email, config('auth.super_admins'));
        }
        <% } %>
    <% } else { this.removeLastLineBreak() } %>
}