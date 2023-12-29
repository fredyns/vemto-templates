<?php
<# TEMPLATE VARIABLES #>
<% let valuesForCompact = [] %>
<% let createValuesForCompact = [] %>
<% let editValuesForCompact = [] %>
<% let viewsPrefix = this.crud.getViewsPrefix() %>
<% let routesPrefix = this.crud.getRoutesPrefix() %>
<% let modelNameCamelCase = this.model.name.case('camelCase') %>
<% let modelsNamespace = this.projectHelper.getModelsNamespace() %>
<% let hasSpecificRequests = !! this.controllerSettings.requests %>
<% let modelPluralCamelCase = this.model.plural.case('camelCase') %>
<% let requestsNamespace = this.projectHelper.getRequestsNamespace() %>
<% let hasPermissions = this.generatorSettings.modules.permissions && this.model.isAuthModel() %>
<####>

namespace <$ this.projectHelper.getControllersNamespace() $>;

use <$ modelsNamespace $>\<$ this.model.name $>;
use Illuminate\Http\Request;
use Illuminate\View\View;
<% if(hasSpecificRequests) { %>
use <$ requestsNamespace $>\<$ this.model.name $>StoreRequest;
use <$ requestsNamespace $>\<$ this.model.name $>UpdateRequest;
<% } %>
<% if(this.crud.hasPasswordInputs()) { %>
use Illuminate\Support\Facades\Hash;
<% } %>
use Illuminate\Http\RedirectResponse;
<% if(this.crud.hasFileOrImageInputs()) { %>
use Illuminate\Support\Facades\Storage;
<% } %>
<# --- #>
<# Importing models from BelongsTo relationships #>
<% if(this.crud.hasBelongsToInputs()) { %>
    <% for(let input of this.crud.getBelongsToInputs()) { %>
        <% valuesForCompact.push(input.getRelationshipCollectionName()) %>
        <% let relModel = input.relationship.model %>
            <% if(!this.model.is(relModel)) { %>
            use <$ modelsNamespace $>\<$ relModel.name $>;
            <% } %>
    <% } %>
<% } %>
<# --- #>
<# Adding permissions classes #>
<% if(hasPermissions) { %>
<% valuesForCompact.push('roles') %>
use Spatie\Permission\Models\Role;
<% } %>
<# --- #>
<# Checking if it needs custom validation rules #>
<% if(this.model.hasUniqueFields() && !hasSpecificRequests) { %>
use Illuminate\Validation\Rule;
<% } %>
<# --- #>
<# Import models from BelongsToMany checkbox groups #>
<% if(this.crud.hasBelongsToManyCheckboxGroups()) { %>
    <% for(let component of this.crud.getBelongsToManyCheckboxGroups()) { %>
        <% if(component.onCreate || component.onEdit) { %>
            use <$ modelsNamespace $>\<$ component.relationship.model.name $>;
        <% } %>
    <% } %>
<% } %>

class <$ this.crud.getControllerName() $> extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('view-any', <$ this.model.name $>::class);

        $search = $request->get('search', '');

        $<$ modelPluralCamelCase $> = <$ this.model.name $>::search($search)
            ->latest(<% if(!this.model.hasTimestampFields()) { %>'<$ this.model.getPkName() $>'<% } %>)
            ->paginate(10)
            ->withQueryString();

        return view('<$ viewsPrefix $>.index', compact('<$ modelPluralCamelCase $>', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $this->authorize('create', <$ this.model.name $>::class);

        <% if(this.crud.hasBelongsToInputs()) { %>
            <% for(let input of this.crud.getBelongsToInputsGroupedByCollection()) { %>
                <% let relModel = input.relationship.model %>
                <% let collectionName = input.getRelationshipCollectionName() %>
                $<$ collectionName $> = <$ relModel.name $>::pluck('<$ relModel.getLabelFieldName() $>', '<$ relModel.getPkName() $>');
            <% } %>

        <% } %>

        <% if(hasPermissions) { %>
        $roles = Role::get();
        <% } %>
        <# --- #>
        <% if(this.crud.hasBelongsToManyCheckboxGroups()) { %>
            <% for(let component of this.crud.getBelongsToManyCheckboxGroupsForCreateForm()) { %>
                <###>
                <% let relationshipModelPluralCamelCase = component.relationship.model.plural.case('camelCase') %>
                <% createValuesForCompact.push(relationshipModelPluralCamelCase) %>
                <###>
                $<$ relationshipModelPluralCamelCase $> = <$ component.relationship.model.name $>::get();
            <% } %>
        <% } %>

        <% let hasCreateValuesForCompact = valuesForCompact.length || createValuesForCompact.length %>

        return view('<$ viewsPrefix $>.create'
        <% if(hasCreateValuesForCompact) { %>
        , compact('<$ [].concat(valuesForCompact, createValuesForCompact).join("', '") $>')
        <% } %>);
    }

    /**
     * Store a newly created resource in storage.
     */
    <% let storeRequestName = hasSpecificRequests ? this.model.name + 'StoreRequest' : 'Request' %>
    public function store(<$ storeRequestName $> $request): RedirectResponse
    {
        $this->authorize('create', <$ this.model.name $>::class);

        <% if(hasSpecificRequests) { %>
        $validated = $request->validated();
        <% } else { %>
        $validated = $request->validate([
            <% for (let input of this.crud.inputs) { %>
                <% if(input.hasValidation()) { %>
                '<$ input.name $>' => <$ input.getValidationForTemplate() $>,
                <% } %>
            <% } %>
            <###>
            <% for(let component of this.crud.getBelongsToManyCheckboxGroupsForCreateForm()) { %>
                <% let relationshipModelPluralCamelCase = component.relationship.model.plural.case('camelCase') %>
                <###>
                '<$ relationshipModelPluralCamelCase $>' => ['array'],
            <% } %>
        ]);
        <% } %>
        <# --- #>
        <# Checking for CRUD password inputs #>
        <% if(this.crud.hasPasswordInputs()) { %>

            <% for(let input of this.crud.getPasswordInputs()) { %>
                <% if(!input.isRequiredOnCreate()) { %>
                if(!empty($validated['<$ input.name $>'])) {
                <% } %>
                $validated['<$ input.name $>'] = Hash::make($validated['<$ input.name $>']);
                <% if(!input.isRequiredOnCreate()) { %>
        }
                <% } %>

            <% } %>
        <% } %>
        <# --- #>
        <# Checking for CRUD file inputs #>
        <% if(this.crud.hasFileOrImageInputs()) { %>
            <% for(let input of this.crud.getFileAndImageInputs()) { %>
            if($request->hasFile('<$ input.name $>')) {
                $validated['<$ input.name $>'] = $request->file('<$ input.name $>')->store('public');
            }
        
            <% } %>
        <% } %>
        <# --- #>
        <# Checking for CRUD json inputs #>
        <% if(this.crud.hasJsonInputs()) { %>
            <% for(let input of this.crud.getJsonInputs()) { %>
            $validated['<$ input.name $>'] = json_decode($validated['<$ input.name $>'], true);
            
            <% } %>
        <% } %>

        $<$ modelNameCamelCase $> = <$ this.model.name $>::create($validated);

        <# Checking for CRUD BelongsToMany relationships #>
        <% if(this.crud.hasBelongsToManyCheckboxGroups()) { %>
            <% for(let component of this.crud.getBelongsToManyCheckboxGroupsForCreateForm()) { %>
                <###>
                <% let relationshipModelPluralCamelCase = component.relationship.model.plural.case('camelCase') %>
                <###>
                $<$ modelNameCamelCase $>-><$ relationshipModelPluralCamelCase $>()->attach($request-><$ relationshipModelPluralCamelCase $>);
            <% } %>
        <% } %>

        <% if(hasPermissions) { %>
        $<$ modelNameCamelCase $>->syncRoles($request->roles);
        <% } %>

        return redirect()->route('<$ routesPrefix $>.show', $<$ modelNameCamelCase $>)->withSuccess(__('crud.common.created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, <$ this.model.name $> $<$ modelNameCamelCase $>): View
    {
        $this->authorize('view', $<$ modelNameCamelCase $>);

        return view('<$ viewsPrefix $>.show', compact('<$ modelNameCamelCase $>'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, <$ this.model.name $> $<$ modelNameCamelCase $>): View
    {
        $this->authorize('update', $<$ modelNameCamelCase $>);

        <% if(this.crud.hasBelongsToInputs()) { %>
            <% for(let input of this.crud.getBelongsToInputsGroupedByCollection()) { %>
            <% let relModel = input.relationship.model %>
            <% let collectionName = input.getRelationshipCollectionName() %>
            $<$ collectionName $> = <$ relModel.name $>::pluck('<$ relModel.getLabelFieldName() $>', '<$ relModel.getPkName() $>');
            <% } %>

        <% } %>
        <# --- #>
        <% if(hasPermissions) { %>
        $roles = Role::get();
        <% } %>
        <# --- #>
        <# BelongsToMany checkbox groups #>
        <% if(this.crud.hasBelongsToManyCheckboxGroups()) { %>
            <% for(let component of this.crud.getBelongsToManyCheckboxGroupsForEditForm()) { %>
                <###>
                <% let relationshipModelPluralCamelCase = component.relationship.model.plural.case('camelCase') %>
                <% editValuesForCompact.push(relationshipModelPluralCamelCase) %>
                <###>
                $<$ relationshipModelPluralCamelCase $> = <$ component.relationship.model.name $>::get();
            <% } %>
        <% } %>

        <% let hasEditValuesForCompact = valuesForCompact.length || editValuesForCompact.length %>

        return view('<$ viewsPrefix $>.edit', compact('<$ modelNameCamelCase $>'<% if(hasEditValuesForCompact) { %>
        , '<$ [].concat(valuesForCompact, editValuesForCompact).join("', '") $>'
        <% } %>));
    }

    /**
     * Update the specified resource in storage.
     */
    <% let updateRequestName = hasSpecificRequests ? this.model.name + 'UpdateRequest' : 'Request' %>
    public function update(<$ updateRequestName $> $request, <$ this.model.name $> $<$ modelNameCamelCase $>): RedirectResponse
    {
        $this->authorize('update', $<$ modelNameCamelCase $>);

        <% if(hasSpecificRequests) { %>
        $validated = $request->validated();
        <% } else { %>
        $validated = $request->validate([
            <% for (let input of this.crud.inputs) { %>
                <% if(input.hasUpdateValidation()) { %>
                '<$ input.name $>' => <$ input.getUpdateValidationWithUniqueRules() $>,
                <% } %>
            <% } %>
            <###>
            <% for(let component of this.crud.getBelongsToManyCheckboxGroupsForEditForm()) { %>
                <% let relationshipModelPluralCamelCase = component.relationship.model.plural.case('camelCase') %>
                <###>
                '<$ relationshipModelPluralCamelCase $>' => ['array'],
            <% } %>
        ]);
        <% } %>
        <# --- #>
        <# Checking for CRUD password inputs #>
        <% if(this.crud.hasPasswordInputs()) { %>
        
            <% for(let input of this.crud.getPasswordInputs()) { %>
            if(empty($validated['<$ input.name $>'])) {
                unset($validated['<$ input.name $>']);
            } else {
                $validated['<$ input.name $>'] = Hash::make($validated['<$ input.name $>']);
            }

            <% } %>
        <% } %>
        <# --- #>
        <# Checking for CRUD file inputs #>
        <% if(this.crud.hasFileOrImageInputs()) { %>
            <% for(let input of this.crud.getFileAndImageInputs()) { %>
            if($request->hasFile('<$ input.name $>')) {
                if($<$ modelNameCamelCase $>-><$ input.name $>) {
                    Storage::delete($<$ modelNameCamelCase $>-><$ input.name $>);
                }

                $validated['<$ input.name $>'] = $request->file('<$ input.name $>')->store('public');
            }

            <% } %>
        <% } %>
        <# --- #>
        <# Checking for CRUD json inputs #>
        <% if(this.crud.hasJsonInputs()) { %>
            <% for(let input of this.crud.getJsonInputs()) { %>
            $validated['<$ input.name $>'] = json_decode($validated['<$ input.name $>'], true);
            
            <% } %>
        <% } %>
        <# --- #>
        <# Checking for CRUD BelongsToMany relationships #>
        <% if(this.crud.hasBelongsToManyCheckboxGroups()) { %>
            <% for(let component of this.crud.getBelongsToManyCheckboxGroupsForEditForm()) { %>
                <###>
                <% let relationshipModelPluralCamelCase = component.relationship.model.plural.case('camelCase') %>
                <###>
                $<$ modelNameCamelCase $>-><$ relationshipModelPluralCamelCase $>()->sync($request-><$ relationshipModelPluralCamelCase $>);
            <% } %>
        <% } %>

        $<$ modelNameCamelCase $>->update($validated);

        <% if(hasPermissions) { %>
        $<$ modelNameCamelCase $>->syncRoles($request->roles);
        <% } %>

        return redirect()->route('<$ routesPrefix $>.show', $<$ modelNameCamelCase $>)->withSuccess(__('crud.common.saved'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, <$ this.model.name $> $<$ modelNameCamelCase $>): RedirectResponse
    {
        $this->authorize('delete', $<$ modelNameCamelCase $>);

        <% if(this.crud.hasFileOrImageInputs()) { %>
            <% for(let input of this.crud.getFileAndImageInputs()) { %>
            if($<$ modelNameCamelCase $>-><$ input.name $>) {
                Storage::delete($<$ modelNameCamelCase $>-><$ input.name $>);
            }

            <% } %>
        <% } %>
        $<$ modelNameCamelCase $>->delete();

        return redirect()->route('<$ routesPrefix $>.index')->withSuccess(__('crud.common.removed'));
    }
}
