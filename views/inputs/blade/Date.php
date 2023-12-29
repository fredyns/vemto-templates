<x-inputs.group class="<$ this.project.css().getReponsiveClassesForInput(this) $>">
    <% if(this.isLinkedToField()) { %>
    <x-inputs.date 
        <% if(this.crud.isForLivewire) { %>
        name="<$ this.getLivewireSingleName() $>" 
        <% } else { %>
        name="<$ this.name $>" 
        <% } %>
        label="{{ __('crud.<$ this.crud.model.name.case('snakeCase') $>.inputs.<$ this.name.case('snakeCase') $>') }}"
        <% if(this.crud.isForLivewire) { %>
        wire:model="<$ this.getLivewireSingleName() $>"
        <% } else { %>
        value="{{ old('<$ this.name $>', ($editing ? optional($<$ this.crud.model.name.case('camelCase') $>-><$ this.name $>)->format('Y-m-d') : '<$ this.defaultValue $>')) }}"
        <% } %>
        <$ this.min ? ` min="${this.min}"` : '' $>
        <$ this.max ? ` max="${this.max}"` : '' $>
        <% if(!this.crud.isForLivewire) { %>
        <$ this.getRequiredAttributeForTemplate() $>
        <% } %>
    ></x-inputs.date>
    <% } else { %>
    <x-inputs.date 
        name="<$ this.name $>" 
        label="{{ __('crud.<$ this.crud.model.name.case('snakeCase') $>.inputs.<$ this.name.case('snakeCase') $>') }}"
        value="{{ old('<$ this.name $>', '<$ this.defaultValue $>') }}" 
        <$ this.min ? ` min="${this.min}"` : '' $>
        <$ this.max ? ` max="${this.max}"` : '' $>
        <% if(!this.crud.isForLivewire) { %>
        <$ this.getRequiredAttributeForTemplate() $>
        <% } %>
    ></x-inputs.date>
    <% } %>
</x-inputs.group>