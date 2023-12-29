<# TEMPLATE VARIABLES #>
<% let modelPluralParamCase = this.model.plural.case('paramCase') %>
<% let modelNameCamelCase = this.model.name.case('camelCase') %>
<% let crudNameSnakeCase = this.crud.name.case('snakeCase') %>
<####>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @lang('crud.<$ crudNameSnakeCase $>.show_title')
        </h2>
    </x-slot>

    <style>
        .trix-button--icon-link, .trix-button--icon-quote, .trix-button--icon-code, .trix-button--icon-attach {
            display: none;
        }
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-partials.card>
                <x-slot name="title">
                    <a href="{{ route('<$ modelPluralParamCase $>.index') }}" class="mr-4">
                        <i class="mr-1 icon ion-md-arrow-back"></i>
                    </a>
                    <% if(this.project.usesLaravelUiTemplate()) { %>
                    @lang('crud.<$ crudNameSnakeCase $>.show_title')
                    <% } %>
                </x-slot>

                <div class="flex flex-wrap mt-4 px-4">
                    <# CRUD inputs elements #>
                    <% for(let input of this.crud.inputs) { %>
                        <% if(input.isLinkedToField() && input.onShow) { %>
                        <# Input types #>
                            <% if(input.isImage()) { %>
                            <div class="mb-4 <$ this.project.css().getReponsiveClassesForInput(input) $>">
                                <h5 class="font-medium text-gray-700">@lang('crud.<$ crudNameSnakeCase $>.inputs.<$ input.name $>')</h5>
                                <x-partials.thumbnail src="{{ $<$ modelNameCamelCase $>-><$ input.name $> ? \Storage::url($<$ modelNameCamelCase $>-><$ input.name $>) : '' }}" size="150"/>
                            </div>
                            <% } else if (input.isFile()) { %>
                            <div class="mb-4 <$ this.project.css().getReponsiveClassesForInput(input) $>">
                                <h5 class="font-medium text-gray-700">@lang('crud.<$ crudNameSnakeCase $>.inputs.<$ input.name $>')</h5>
                                @if($<$ modelNameCamelCase $>-><$ input.name $>) 
                                    <a href="{{ \Storage::url($<$ modelNameCamelCase $>-><$ input.name $>) }}" target="blank">
                                        <i class="mr-1 icon ion-md-download"></i>&nbsp;Download
                                    </a> 
                                @else
                                    - 
                                @endif
                            </div>
                            <% } else { %>
                                <% if(input.isForRelationship()) { %>
                                <div class="mb-4 <$ this.project.css().getReponsiveClassesForInput(input) $>">
                                    <h5 class="font-medium text-gray-700">@lang('crud.<$ crudNameSnakeCase $>.inputs.<$ input.name $>')</h5>
                                    <span>
                                        {{ optional($<$ modelNameCamelCase $>-><$ input.relationship.name $>)-><$ input.relationship.model.getLabelFieldName() $> ?? '-' }}
                                    </span>
                                </div>
                                <% } else if(input.isJson()) { %>
                                <div class="mb-4 <$ this.project.css().getReponsiveClassesForInput(input) $>">
                                    <h5 class="font-medium text-gray-700">@lang('crud.<$ crudNameSnakeCase $>.inputs.<$ input.name $>')</h5>
                                    <pre>{{ json_encode($<$ modelNameCamelCase $>-><$ input.name $>) ?? '-' }}</pre>
                                </div>
                                <% } else if(input.isUrl()) {  %>
                                <div class="mb-4 <$ this.project.css().getReponsiveClassesForInput(input) $>">
                                    <h5 class="font-medium text-gray-700">@lang('crud.<$ crudNameSnakeCase $>.inputs.<$ input.name $>')</h5>
                                    <a class="underline cursor-pointer" target="_blank" href="{{ $<$ modelNameCamelCase $>-><$ input.name $> }}">
                                        {{ $<$ modelNameCamelCase $>-><$ input.name $> ?? '-' }}
                                    </a>
                                </div>
                                <% } else { %>
                                <div class="mb-4 <$ this.project.css().getReponsiveClassesForInput(input) $>">
                                    <h5 class="font-medium text-gray-700">@lang('crud.<$ crudNameSnakeCase $>.inputs.<$ input.name $>')</h5>
                                    <span>
                                        {{ $<$ modelNameCamelCase $>-><$ input.name $> ?? '-' }}
                                    </span>
                                </div>
                                <% } %>
                            <% } %>
                        <% } %>
                    <% } %>
                </div>

                <# If the permissions module has been enabled and it is for the auth model #>
                <% if(this.generatorSettings.modules.permissions && this.model.isAuthModel()) { %>
                <div class="mt-4 px-4">
                    <div class="mb-4">
                        <h5 class="font-medium text-gray-700">@lang('crud.roles.name')</h5>
                        <div>
                            @forelse ($<$ modelNameCamelCase $>->roles as $role)
                                <div class="inline-block p-1 text-center text-sm rounded bg-blue-400 text-white">{{ $role->name }}</div>
                                <br>
                            @empty
                                -
                            @endforelse
                        </div>
                    </div>
                </div>
                <% } %>

                <div class="mt-10">
                    <a href="{{ route('<$ modelPluralParamCase $>.index') }}" class="button">
                        <i class="mr-1 icon ion-md-return-left"></i> @lang('crud.common.back')
                    </a>
                    
                    @can('update', $<$ modelNameCamelCase $>)
                    <a href="{{ route('<$ modelPluralParamCase $>.edit', $<$ modelNameCamelCase $>) }}" class="button">
                        <i class="mr-1 icon ion-md-create"></i> @lang('crud.common.edit')
                    </a>
                    @endcan

                    @can('delete', $<$ modelNameCamelCase $>)
                        <div class="float-right">
                            <form
                                action="{{ route('<$ modelPluralParamCase $>.destroy', $<$ modelNameCamelCase $>) }}"
                                method="POST"
                                onsubmit="return confirm('{{ __('crud.common.are_you_sure') }}')"
                            >
                                @csrf @method('DELETE')
                                <button type="submit" class="button">
                                    <i class="mr-1 icon ion-md-trash text-red-600"></i>
                                    <span class="text-red-600">@lang('crud.common.delete')</span>
                                </button>
                            </form>
                        </div>
                    @endcan
                </div>
            </x-partials.card>

            <% if(this.crud.hasManyDetails) { %>
                <# HasManyDetail components #>
                <% for(let detail of this.crud.getHasManyDetailsForShowPage()) { %>
                <% let detailModelNameCamelCase = detail.crud.model.name.case('camelCase') %>
                <div class="display: none;"></div>
                @can('view-any', <$ this.projectHelper.getModelsNamespace() $>\<$ detail.relatedCrud.model.name $>::class)
                <x-partials.card class="mt-5">
                    <x-slot name="title">
                        @lang('crud.<$ detail.relatedCrud.name.case('snakeCase') $>.name')
                    </x-slot>

                    <livewire:<$ detail.getLivewireViewName() $> :<$ detailModelNameCamelCase $>="$<$ detailModelNameCamelCase $>" />
                </x-partials.card>
                @endcan
                <% } %>
            <% } %>

            <% if(this.crud.manyToManyDetails) { %>
                <# ManyToManyDetail components #>
                <% for(let detail of this.crud.getManyToManyDetailsForShowPage()) { %>
                <% let detailModelNameCamelCase = detail.crud.model.name.case('camelCase') %>
                <div class="display: none;"></div>
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
        </div>
    </div>
</x-app-layout>