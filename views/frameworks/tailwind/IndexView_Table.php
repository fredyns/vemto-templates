<% let cols = 1 %>
<div class="block w-full overflow-auto scrolling-touch">
    <table class="w-full max-w-full mb-4 bg-transparent">
        <thead class="text-gray-700">
        <tr>
            <% for(let input of this.crud.inputs) { %>
            <% if(input.isAllowedOnIndexPages() && input.onIndex) { %>
            <% cols++ %>
            <% if(input.isNumeric()) { %>
            <th class="px-4 py-3 text-right">@lang('crud.<$ crudNameSnakeCase $>.inputs.<$ input.name $>')</th>
            <% } else { %>
            <th class="px-4 py-3 text-left">@lang('crud.<$ crudNameSnakeCase $>.inputs.<$ input.name $>')</th>
            <% } %>
            <% } %>
            <% } %>
            <th></th>
        </tr>
        </thead>
        <tbody class="text-gray-600">
        @forelse($<$ modelPluralCamelCase $> as $<$ modelNameCamelCase $>)
        <tr class="hover:bg-gray-50">
            <% for(let input of this.crud.inputs) { %>
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
            <td class="px-4 py-3 text-left"><x-partials.thumbnail src="{{ $<$ modelNameCamelCase $>-><$ input.name $> ? \Storage::url($<$ modelNameCamelCase $>-><$ input.name $>) : '' }}"/></td>
            <% } else if(input.isFile()) { %>
            <td class="px-4 py-3 text-left">@if($<$ modelNameCamelCase $>-><$ input.name $>) <a href="{{ \Storage::url($<$ modelNameCamelCase $>-><$ input.name $>) }}" target="blank"><i class="mr-1 icon ion-md-download"></i>&nbsp;Download</a> @else - @endif</td>
            <% } else { %>
            <% if(input.isForRelationship()) { %>
            <td class="px-4 py-3 text-left">{{ optional($<$ modelNameCamelCase $>-><$ input.relationship.name $>)-><$ input.relationship.model.getLabelFieldName() $> ?? '-' }}</td>
            <% } else if(input.isNumeric()) { %>
            <td class="px-4 py-3 text-right">{{ $<$ modelNameCamelCase $>-><$ input.name $> ?? '-' }}</td>
            <% } else if(input.isJson()) { %>
            <td class="px-4 py-3 text-right"><pre>{{ json_encode($<$ modelNameCamelCase $>-><$ input.name $>) ?? '-' }}</pre></td>
            <% } else if(input.isUrl()) { %>
            <td class="px-4 py-3 text-left"><a class="underline cursor-pointer" target="_blank" href="{{ $<$ modelNameCamelCase $>-><$ input.name $> }}">{{ $<$ modelNameCamelCase $>-><$ input.name $> ?? '-' }}</a></td>
            <% } else { %>
            <td class="px-4 py-3 text-left">{{ $<$ modelNameCamelCase $>-><$ input.name $> ?? '-' }}</td>
            <% } %>
            <% } %>
            <% } %>
            <% } %>
            <td class="px-4 py-3 text-center" style="width: 134px;">
                <div role="group" aria-label="Row Actions" class="relative inline-flex align-middle">
                    @can('update', $<$ modelNameCamelCase $>)
                    <a href="{{ route('<$ modelPluralParamCase $>.edit', $<$ modelNameCamelCase $>) }}" class="mr-1">
                        <button type="button" class="button">
                            <i class="icon ion-md-create"></i>
                        </button>
                    </a>
                    @endcan

                    @can('view', $<$ modelNameCamelCase $>)
                    <a href="{{ route('<$ modelPluralParamCase $>.show', $<$ modelNameCamelCase $>) }}" class="mr-1">
                        <button type="button" class="button">
                            <i class="icon ion-md-eye"></i>
                        </button>
                    </a>
                    @endcan
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="<$ cols $>">@lang('crud.common.no_items_found')</td>
        </tr>
        @endforelse
        </tbody>
        <tfoot>
        <tr>
            <td colspan="<$ cols $>">
                <div class="mt-10 px-4">
                    {!! $<$ modelPluralCamelCase $>->render() !!}
                </div>
            </td>
        </tr>
        </tfoot>
    </table>
</div>