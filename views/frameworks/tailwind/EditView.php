<# TEMPLATE VARIABLES #>
<% let modelPluralSnakeCase = this.model.plural.case('snakeCase') %>
<% let modelPluralParamCase = this.model.plural.case('paramCase') %>
<% let modelNameCamelCase = this.model.name.case('camelCase') %>
<% let crudNameSnakeCase = this.crud.name.case('snakeCase') %>
<####>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @lang('crud.<$ crudNameSnakeCase $>.edit_title')
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-partials.card>
                <x-slot name="title">
                    <a href="{{ route('<$ modelPluralParamCase $>.index') }}" class="mr-4">
                        <i class="mr-1 icon ion-md-arrow-back"></i>
                    </a>
                    <% if(this.project.usesLaravelUiTemplate()) { %>
                    @lang('crud.<$ crudNameSnakeCase $>.edit_title')
                    <% } %>
                </x-slot>

                <x-form method="PUT" action="{{ route('<$ modelPluralParamCase $>.update', $<$ modelNameCamelCase $>) }}" <$ this.crud.hasFileOrImageInputs() ? 'has-files' : '' $> class="mt-4">
                    @include('<$ this.project.viewsPath() $>.<$ modelPluralSnakeCase $>.form-inputs')
                    
                    <div class="mt-10">
                        <a href="{{ route('<$ modelPluralParamCase $>.index') }}" class="button">
                            <i class="mr-1 icon ion-md-return-left text-primary"></i> 
                            @lang('crud.common.back')
                        </a>

                        <a href="{{ route('<$ modelPluralParamCase $>.show', $<$ modelNameCamelCase $>) }}" class="button">
                            <i class="mr-1 icon ion-md-backspace text-primary">
                            </i> 
                            @lang('crud.common.cancel')
                        </a>

                        <button type="submit" class="button button-primary float-right">
                            <i class="mr-1 icon ion-md-save"></i> 
                            @lang('crud.common.update')
                        </button>
                    </div>
                </x-form>
            </x-partials.card>

            <% if(this.crud.hasManyDetails) { %>
                <# HasManyDetail components #>
                <% for(let detail of this.crud.getHasManyDetailsForEditPage()) { %>
                <% let detailModelNameCamelCase = detail.crud.model.name.case('camelCase') %>

                @can('view-any', <$ this.projectHelper.getModelsNamespace() $>\<$ detail.relatedCrud.model.name $>::class)
                <x-partials.card class="mt-5">
                    <x-slot name="title">
                        <$ detail.name $>
                    </x-slot>

                    <livewire:<$ detail.getLivewireViewName() $> :<$ detailModelNameCamelCase $>="$<$ detailModelNameCamelCase $>" />
                </x-partials.card>
                @endcan

                <% } %>
            <% } %>

            <% if(this.crud.manyToManyDetails) { %>
                <# ManyToManyDetail components #>
                <% for(let detail of this.crud.getManyToManyDetailsForEditPage()) { %>

                <% let detailModelNameCamelCase = detail.crud.model.name.case('camelCase') %>
                @can('view-any', <$ this.projectHelper.getModelsNamespace() $>\<$ detail.relationship.model.name $>::class)
                <x-partials.card class="mt-5">
                    <x-slot name="title">
                        <$ detail.name $>
                    </x-slot>

                    <livewire:<$ detail.getLivewireViewName() $> :<$ detailModelNameCamelCase $>="$<$ detailModelNameCamelCase $>" />
                </x-partials.card>
                @endcan
                <% } %>

            <% } %>
        </div>
    </div>
</x-app-layout>