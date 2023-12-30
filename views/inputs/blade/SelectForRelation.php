<% let isForLivewire = this.crud.isForLivewire %>
<% let relationshipModel = this.relationshipId ? this.relationship.model : this.foreign.relatedEntity %>
<% let starterOptionLabel = 'Please select the ' + relationshipModel.name.case('capitalCase') %>

<x-inputs.group class="<$ this.project.css().getReponsiveClassesForInput(this) $>">
    <x-inputs.select
        name="<$ this.livewireInputReference || this.name $>"
        label="{{ __('crud.<$ this.crud.model.plural.case('snakeCase') $>.inputs.<$ this.name.case('snakeCase') $>') }}"
    <% if(isForLivewire) { %>
    wire:model="<$ this.livewireInputReference $>"
    <% } %>
    <% if(!this.crud.isForLivewire) { %>
    <$ this.getRequiredAttributeForTemplate() $>
    <% } %>
    >
    <% if(!isForLivewire) { %>
    @php $selected = old('<$ this.name $>', ($editing ? $<$ this.crud.model.name.case('camelCase') $>-><$ this.name $> : '<$ this.defaultValue $>')) @endphp
    <option disabled {{ empty($selected) ? 'selected' : '' }}><$ this.starterOptionText ? this.starterOptionText : starterOptionLabel $></option>
    <% } else { %>
    <option value="null" disabled><$ this.starterOptionText ? this.starterOptionText : starterOptionLabel $></option>
    <% } %>
    @foreach($<$ this.getVariableForRelationshipSelect() $> as $value => $label)
    <option value="{{ $value }}" <% if(!isForLivewire) { %>{{ $selected == $value ? 'selected' : '' }}<% } %> >{{ $label }}</option>
    @endforeach
    </x-inputs.select>
</x-inputs.group>