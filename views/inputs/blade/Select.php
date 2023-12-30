<% let isForLivewire = this.crud.isForLivewire %>

<x-inputs.group class="<$ this.project.css().getReponsiveClassesForInput(this) $>">
    <% if(this.isLinkedToField()) { %>
    <x-inputs.select
        name="<$ this.livewireInputReference || this.name $>"
        label="{{ __('crud.<$ this.crud.model.plural.case('snakeCase') $>.inputs.<$ this.name.case('snakeCase') $>') }}"
    <% if(isForLivewire) { %>
    wire:model="<$ this.livewireInputReference $>"
    <% } %>
    >
    <% if(!isForLivewire) { %>
    @php $selected = old('<$ this.name $>', ($editing ? $<$ this.crud.model.name.case('camelCase') $>-><$ this.name $> : '<$ this.defaultValue $>')) @endphp
    <% } %>

    <% for(let item of this.items) { %>
    <% let selectedCondition = this.crud.isManyToManyDetail ? '' : `{{ $selected == '${item.value}' ? 'selected' : '' }}`  %>
    <option value="<$ item.value $>" <$ selectedCondition $> ><$ item.label $></option>
    <% } %>
    </x-inputs.select>
    <% } else { %>
    <x-inputs.select name="<$ this.name $>" label="<$ this.label $>">
        <% for(let item of this.items) { %>
        <option value="<$ item.value $>"><$ item.label $></option>
        <% } %>
    </x-inputs.select>
    <% } %>
</x-inputs.group>