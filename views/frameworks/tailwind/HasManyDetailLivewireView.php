<div>
    <div>
        @can('create', <$ this.projectHelper.getModelsNamespace() $>\<$ this.relatedCrud.model.name $>::class)
        <button class="button" wire:click="new<$ this.relatedCrud.model.name.case('pascalCase') $>">
            <i class="mr-1 icon ion-md-add text-primary"></i>
            @lang('crud.common.new')
        </button>
        @endcan

        @can('delete-any', <$ this.projectHelper.getModelsNamespace() $>\<$ this.relatedCrud.model.name $>::class)
        <button
            class="button button-danger"
            {{ empty($selected) ? 'disabled' : '' }}
            onclick="confirm('{{ __('crud.common.are_you_sure') }}') || event.stopImmediatePropagation()"
            wire:click="destroySelected"
        >
            <i class="mr-1 icon ion-md-trash text-primary"></i>
            @lang('crud.common.delete_selected')
        </button>
        @endcan
    </div>

    <x-modal wire:model="showingModal">
        <div class="px-6 py-4">
            <div class="text-lg font-bold">
                {{ $modalTitle }}
            </div>

            <div class="mt-5">
                <div>
                    <% for(let input of this.relatedCrud.inputs) { %>
                        <% if(!input.isComputed && (input.onCreate || input.onUpdate)) { %>
                        <###>
                        <% let inputOnlyForCreate = input.onCreate && !input.onUpdate %>
                        <% let inputOnlyForUpdate = input.onUpdate && !input.onCreate %>
                        <###>
                        <% if(inputOnlyForCreate) { %>
                            @if(!$<$ this.relatedCrud.model.name.case('camelCase') $>->exists)
                        <% } %>
                        <###>
                        <% if(inputOnlyForUpdate) { %>
                            @if($<$ this.relatedCrud.model.name.case('camelCase') $>->exists)
                        <% } %>
                            <# We'll replace this [INPUT:id] code with input template #>
                            [INPUT:<$ input.id $>]
                            <###>
                            <% if(inputOnlyForCreate || inputOnlyForUpdate) { %>
                                @endif
                            <% } %>
                        <% } %>
                    <% } %>
                </div>
            </div>

            <% if(this.relatedCrud.hasManyDetails.length > 0) { %>
            @if($editing)
                <% for(let detail of this.relatedCrud.hasManyDetails) { %>
                @can('view-any', <$ this.projectHelper.getModelsNamespace() $>\<$ detail.relatedCrud.model.name $>::class)
                <x-partials.card class="mt-5 shadow-none bg-gray-50">
                    <h4 class="text-sm text-gray-600 font-bold mb-3">
                        <$ detail.name $>
                    </h4>

                    <livewire:<$ detail.getLivewireViewName() $> :<$ detail.crud.model.name.case('camelCase') $>="$<$ detail.crud.model.name.case('camelCase') $>" />
                </x-partials.card>
                @endcan
            <% } %>
            
            @endif
            <% } %>
        </div>

        <div class="px-6 py-4 bg-gray-50 flex justify-between">
            <button
                type="button"
                class="button"
                wire:click="$toggle('showingModal')"
            >
                <i class="mr-1 icon ion-md-close"></i>
                @lang('crud.common.cancel')
            </button>

            <button
                type="button"
                class="button button-primary"
                wire:click="save"
            >
                <i class="mr-1 icon ion-md-save"></i>
                @lang('crud.common.save')
            </button>
        </div>
    </x-modal>

    <% let cols = 1 %>
    <div class="block w-full overflow-auto scrolling-touch mt-4">
        <table class="w-full max-w-full mb-4 bg-transparent">
            <thead class="text-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left w-1">
                        <input type="checkbox" wire:model="allSelected" wire:click="toggleFullSelection" title="{{ trans('crud.common.select_all') }}">
                    </th>
                    <% for(let input of this.relatedCrud.inputs) { %>
                    <% if(input.onIndex && input.isAllowedOnIndexPages()) { %>
                    <% cols++ %>
                    <% if(input.isNumeric()) { %>
                    <th class="px-4 py-3 text-right">@lang('crud.<$ this.relatedCrud.name.case('snakeCase') $>.inputs.<$ input.name $>')</th>
                    <% } else { %>
                    <th class="px-4 py-3 text-left">@lang('crud.<$ this.relatedCrud.name.case('snakeCase') $>.inputs.<$ input.name $>')</th>
                    <% } %>
                    <% } %>
                    <% } %>
                    <th></th>
                </tr>
            </thead>
            <tbody class="text-gray-600">
                @foreach ($<$ this.relatedCrud.model.plural.case('camelCase') $> as $<$ this.relatedCrud.model.name.case('camelCase') $>)
                <tr class="hover:bg-gray-100">
                    <td class="px-4 py-3 text-left">
                        <input type="checkbox" value="{{ $<$ this.relatedCrud.model.name.case('camelCase') $>->id }}" wire:model="selected">
                    </td>
                    <% for(let input of this.relatedCrud.inputs) { %>
                    <# COMPUTED INPUT #>
                    <% if(input.isComputed && input.onIndex) { %>
                    <% if(input.isNumeric()) { %>
                    <td class="px-4 py-3 text-right">{{ <$ input.computedFormula $> }}</td>
                    <% } else { %>
                    <td class="px-4 py-3 text-left">{{ <$ input.computedFormula $> }}</td>
                    <% } %>
                    <% } %>
                    <# INPUT LINKED TO FIELD #>
                    <% if(input.isLinkedToField() && input.onIndex) { %>
                    <% if(input.isImage()) { %>
                    <td class="px-4 py-3 text-left"><x-partials.thumbnail src="{{ $<$ this.relatedCrud.model.name.case('camelCase') $>-><$ input.name $> ? \Storage::url($<$ this.relatedCrud.model.name.case('camelCase') $>-><$ input.name $>) : '' }}"/></td>
                    <% } else if(input.isFile()) { %>
                    <td class="px-4 py-3 text-left">@if($<$ this.relatedCrud.model.name.case('camelCase') $>-><$ input.name $>) <a href="{{ \Storage::url($<$ this.relatedCrud.model.name.case('camelCase') $>-><$ input.name $>) }}" target="blank"><i class="mr-1 icon ion-md-download"></i>&nbsp;Download</a> @else - @endif</td>
                    <% } else { %>
                    <% if(input.isForRelationship()) { %>
                    <td class="px-4 py-3 text-left">{{ optional($<$ this.relatedCrud.model.name.case('camelCase') $>-><$ input.relationship.name $>)-><$ input.relationship.model.getLabelFieldName() $> ?? '-' }}</td>
                    <% } else if(input.isNumeric()) { %>
                    <td class="px-4 py-3 text-right">{{ $<$ this.relatedCrud.model.name.case('camelCase') $>-><$ input.name $> ?? '-' }}</td>
                    <% } else if(input.isJson()) { %>
                    <td class="px-4 py-3 text-right"><pre>{{ json_encode($<$ this.relatedCrud.model.name.case('camelCase') $>-><$ input.name $>) ?? '-' }}</pre></td>
                    <% } else { %>
                    <td class="px-4 py-3 text-left">{{ $<$ this.relatedCrud.model.name.case('camelCase') $>-><$ input.name $> ?? '-' }}</td>
                    <% } %>
                    <% } %>
                    <% } %>
                    <% } %>
                    <td
                        class="px-4 py-3 text-right"
                        style="width: 134px;"
                    >
                        <div
                            role="group"
                            aria-label="Row Actions"
                            class="relative inline-flex align-middle"
                        >
                            @can('update', $<$ this.relatedCrud.model.name.case('camelCase') $>)
                            <button
                                type="button"
                                class="button"
                                wire:click="edit<$ this.relatedCrud.model.name.case('pascalCase') $>('{{ $<$ this.relatedCrud.model.name.case('camelCase') $>->id }}')"
                            >
                                <i
                                    class="icon ion-md-create"
                                ></i>
                            </button>
                            @endcan
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="<$ cols $>">
                        <div class="mt-10 px-4">
                            {{ $<$ this.relatedCrud.model.plural.case('camelCase') $>->render() }}
                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>