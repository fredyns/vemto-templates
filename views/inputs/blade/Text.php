<x-inputs.group class="<$ this.project.css().getReponsiveClassesForInput(this) $>">
    <% if(this.isLinkedToField()) { %>
    <x-inputs.text 
        name="<$ this.livewireInputReference || this.name $>"
        label="{{ __('crud.<$ this.crud.model.plural.case('snakeCase') $>.inputs.<$ this.name.case('snakeCase') $>') }}"
        <% if(this.crud.isForLivewire) { %>
        wire:model="<$ this.livewireInputReference $>"
        <% } else { %>
        :value="old('<$ this.name $>', ($editing ? $<$ this.crud.model.name.case('camelCase') $>-><$ this.name $> : '<$ this.defaultValue $>'))"
        <% } %>
        <$ this.min ? ` minlength="${this.min}"` : '' $>
        <$ this.max ? ` maxlength="${this.max}"` : '' $>
        <$ this.placeholder ? ` placeholder="{{ __('crud.${this.crud.model.name.case('snakeCase')}.inputs.${this.name.case('snakeCase')}') }}"` : '' $>
        <% if(!this.crud.isForLivewire) { %>
        <$ this.getRequiredAttributeForTemplate() $>
        <% } %>
    ></x-inputs.text>
    <% } else { %>
    <x-inputs.text 
        name="<$ this.name $>" 
        label="{{ __('crud.<$ this.crud.model.plural.case('snakeCase') $>.inputs.<$ this.name.case('snakeCase') $>') }}"
        :value="old('<$ this.name $>', '<$ this.defaultValue $>')" 
        <$ this.min ? ` minlength="${this.min}"` : '' $>
        <$ this.max ? ` maxlength="${this.max}"` : '' $>
        <$ this.placeholder ? ` placeholder="{{ __('crud.${this.crud.model.name.case('snakeCase')}.inputs.${this.name.case('snakeCase')}') }}"` : '' $>
        <% if(!this.crud.isForLivewire) { %>
        <$ this.getRequiredAttributeForTemplate() $>
        <% } %>
    ></x-inputs.text>
    <% } %>
</x-inputs.group>