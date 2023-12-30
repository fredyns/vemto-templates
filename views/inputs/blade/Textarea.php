<x-inputs.group class="<$ this.project.css().getReponsiveClassesForInput(this) $>">
    <% if(this.isLinkedToField()) { %>
    <x-inputs.textarea
            name="<$ this.livewireInputReference || this.name $>"
            label="{{ __('crud.<$ this.crud.model.plural.case('snakeCase') $>.inputs.<$ this.name.case('snakeCase') $>') }}"
    <% if(this.crud.isForLivewire) { %>
    wire:model="<$ this.livewireInputReference $>"
    <% } %>
    <$ this.min ? ` minlength="${this.min}"` : '' $>
    <$ this.max ? ` maxlength="${this.max}"` : '' $>
    <% if(!this.crud.isForLivewire) { %>
    <$ this.getRequiredAttributeForTemplate() $>
    <% } %>
    <% if(!this.crud.isForLivewire) { %>
    <% if(this.isJson()) { %>
    >{{ old('<$ this.name $>', ($editing ? json_encode($<$ this.crud.model.name.case('camelCase') $>-><$ this.name $>) : '<$ this.defaultValue $>')) }}</x-inputs.textarea>
    <% } else { %>
    >{{ old('<$ this.name $>', ($editing ? $<$ this.crud.model.name.case('camelCase') $>-><$ this.name $> : '<$ this.defaultValue $>')) }}</x-inputs.textarea>
    <% } %>
    <% } else { %>
    ></x-inputs.textarea>
    <% } %>
    <% } else { %>
    <x-inputs.textarea
            name="<$ this.name $>"
            label="<$ this.label $>"
    <$ this.min ? ` minlength="${this.min}"` : '' $>
    <$ this.max ? ` maxlength="${this.max}"` : '' $>
    <% if(!this.crud.isForLivewire) { %>
    <$ this.getRequiredAttributeForTemplate() $>
    <% } %>
    >{{ old('<$ this.name $>', '<$ this.defaultValue $>') }}</x-inputs.textarea>
    <% } %>
</x-inputs.group>