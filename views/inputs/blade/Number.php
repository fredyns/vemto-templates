<x-inputs.group class="<$ this.project.css().getReponsiveClassesForInput(this) $>">
    <% if(this.isLinkedToField()) { %>
    <x-inputs.number 
        name="<$ this.livewireInputReference || this.name $>" 
        label="{{ __('crud.<$ this.crud.model.name.case('snakeCase') $>.inputs.<$ this.name.case('snakeCase') $>') }}"
        <% if(this.crud.isForLivewire) { %>
        wire:model="<$ this.livewireInputReference $>"
        <% } else { %>
        :value="old('<$ this.name $>', ($editing ? $<$ this.crud.model.name.case('camelCase') $>-><$ this.name $> : '<$ this.defaultValue $>'))"
        <% } %>
        <$ this.min ? ` min="${this.min}"` : '' $>
        <$ this.max ? ` max="${this.max}"` : '' $>
        <$ this.step ? ` step="${this.step}"` : '' $>
        <$ this.placeholder ? ` placeholder="${this.placeholder}"` : '' $>
        <% if(!this.crud.isForLivewire) { %>
        <$ this.getRequiredAttributeForTemplate() $>
        <% } %>
    ></x-inputs.number>
    <% } else { %>
    <x-inputs.number 
        name="<$ this.name $>" 
        label="{{ __('crud.<$ this.crud.model.name.case('snakeCase') $>.inputs.<$ this.name.case('snakeCase') $>') }}"
        :value="old('<$ this.name $>', '<$ this.defaultValue $>')"
        <$ this.min ? ` min="${this.min}"` : '' $>
        <$ this.max ? ` max="${this.max}"` : '' $>
        <$ this.step ? ` step="${this.step}"` : '' $>
        <$ this.placeholder ? ` placeholder="${this.placeholder}"` : '' $>
        <% if(!this.crud.isForLivewire) { %>
        <$ this.getRequiredAttributeForTemplate() $>
        <% } %>
    ></x-inputs.number>
    <% } %>
</x-inputs.group>