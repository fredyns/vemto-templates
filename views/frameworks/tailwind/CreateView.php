<# TEMPLATE VARIABLES #>
<% let modelPluralSnakeCase = this.model.plural.case('snakeCase') %>
<% let modelPluralParamCase = this.model.plural.case('paramCase') %>
<% let crudNameSnakeCase = this.crud.name.case('snakeCase') %>
<####>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @lang('crud.<$ crudNameSnakeCase $>.create_title')
        </h2>
    </x-slot>

    <div class="py-12">
        <x-form method="POST" action="{{ route('<$ modelPluralParamCase $>.store') }}" <$ this.crud.hasFileOrImageInputs() ? 'has-files' : '' $> class="mt-4">

        @include('<$ this.project.viewsPath() $>.<$ modelPluralSnakeCase $>.form-inputs')

        <div class="max-w-7xl mx-auto py-3 sm:px-6 lg:px-8">
            <x-partials.card>
                <div class="my-3">
                    <a href="{{ route('<$ modelPluralParamCase $>.index') }}" class="button">
                        <i class="mr-1 icon ion-md-return-left text-primary"></i>
                        @lang('crud.common.back')
                    </a>

                    <button type="submit" class="button button-primary float-right">
                        <i class="mr-1 icon ion-md-save"></i>
                        @lang('crud.common.create')
                    </button>
                </div>
            </x-partials.card>
        </div>

        </x-form>
    </div>
</x-app-layout>