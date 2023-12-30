<# TEMPLATE VARIABLES #>
<% let modelNameCamelCase = this.model.name.case('camelCase') %>
<####>
@php $editing = isset($<$ modelNameCamelCase $>) @endphp

<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <x-partials.card>
        <x-slot name="title">
            <a href="javascript: history.go(-1)" class="mr-4">
                <i class="mr-1 icon ion-md-arrow-back"></i>
            </a>
            <span>@lang('app.containers.sections.general')</span>
        </x-slot>

        <div class="flex flex-wrap">
            <% for(let input of this.crud.getRenderableInputs()) { %>
            <% if(!input.isComputed && (input.onCreate || input.onUpdate)) { %>
            <% let inputOnlyForCreate = input.onCreate && !input.onUpdate %>
            <% let inputOnlyForUpdate = input.onUpdate && !input.onCreate %>
            <###>
            <% if(inputOnlyForCreate) { %>
            @if(!$editing)
            <% } %>
            <###>
            <% if(inputOnlyForUpdate) { %>
            @if($editing)
            <% } %>
            <# We'll replace this [INPUT:id] code with input template #>
            [INPUT:<$ input.id $>]
            <###>
            <% if(inputOnlyForCreate || inputOnlyForUpdate) { %>
            @endif
            <% } %>
            <% } %>

            <% } %>

            <% for(let component of this.crud.getDependentSelects()) { %>
            @livewire('selects.<$ component.getName().case("param-case") $>', ['<$ modelNameCamelCase $>' => $editing ? $<$ modelNameCamelCase $>-><$ this.model.getPrimaryKeyName() $> : null])
            <% } %>

            <% for(let component of this.crud.getBelongsToManyCheckboxGroups()) { %>
            <% let model = component.relationship.model %>
            <% let componentModelNameCamelCase = model.name.case('camelCase') %>
            <% let modelPluralCamelCase = model.plural.case('camelCase') %>
            <% let componentCondition = component.condition || component.getDefaultCondition() %>
            <###>
            <% if(component.onCreate || component.onEdit) { %>
            <###>
            <% let componentOnlyForCreate = component.onCreate && !component.onEdit %>
            <% let componentOnlyForEdit = component.onEdit && !component.onCreate %>
            <###>
            <% if(componentOnlyForCreate) { %>
            @if(!$editing)
            <% } %>
            <###>
            <% if(componentOnlyForEdit) { %>
            @if($editing)
            <% } %>
            <div class="px-4 my-4">
                <h4 class="font-bold text-lg text-gray-700">Assign @lang('crud.<$ model.plural.case('snakeCase') $>.name')</h4>

                <div class="py-2">
                    @foreach ($<$ modelPluralCamelCase $> as $<$ componentModelNameCamelCase $>)
                    <div>
                        <x-inputs.checkbox
                            id="<$ componentModelNameCamelCase $>{{ $<$ componentModelNameCamelCase $>->id }}"
                            name="<$ modelPluralCamelCase $>[]"
                            label="{{ ucfirst($<$ componentModelNameCamelCase $>-><$ model.getLabelFieldName() $>) }}"
                            value="{{ $<$ componentModelNameCamelCase $>-><$ model.getPrimaryKeyName() $> }}"
                            :checked="isset($<$ modelNameCamelCase $>) ? <$ componentCondition $> : false"
                            :add-hidden-value="false"
                        ></x-inputs.checkbox>
                    </div>
                    @endforeach
                </div>
            </div>
            <###>
            <% if(componentOnlyForCreate || componentOnlyForEdit) { %>
            @endif
            <% } %>
            <% } %>
            <% } %>

            <# If the permissions module has been enabled and it is a CRUD for the auth model #>
            <% if(this.generatorSettings.modules.permissions && this.model.isAuthModel()) { %>
            <div class="px-4 my-4">
                <h4 class="font-bold text-lg text-gray-700">Assign @lang('crud.roles.name')</h4>

                <div class="py-2">
                    @foreach ($roles as $role)
                    <div>
                        <x-inputs.checkbox
                            id="role{{ $role->id }}"
                            name="roles[]"
                            label="{{ ucfirst($role->name) }}"
                            value="{{ $role->id }}"
                            :checked="isset($<$ modelNameCamelCase $>) ? $<$ modelNameCamelCase $>->hasRole($role) : false"
                            :add-hidden-value="false"
                        ></x-inputs.checkbox>
                    </div>
                    @endforeach
                </div>
            </div>
            <% } %>
        </div>
    </x-partials.card>
</div>