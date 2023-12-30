<x-inputs.group class="<$ this.project.css().getReponsiveClassesForInput(this) $>">
    <% if(this.isLinkedToField()) { %>
    <x-inputs.password
    <% if(this.crud.isForLivewire) { %>
    name="<$ this.getLivewireSingleName() $>"
    <% } else { %>
    name="<$ this.name $>"
    <% } %>
    label="{{ __('crud.<$ this.crud.model.plural.case('snakeCase') $>.inputs.<$ this.name.case('snakeCase') $>') }}"
    <% if(this.crud.isForLivewire) { %>
    wire:model="<$ this.getLivewireSingleName() $>"
    <% } %>
    <$ this.min ? ` minlength="${this.min}"` : '' $>
    <$ this.max ? ` maxlength="${this.max}"` : '' $>
    <$ this.placeholder ? ` placeholder="${this.placeholder}"` : '' $>
    <% if(!this.crud.isForLivewire) { %>
    <$ this.getRequiredAttributeForTemplate() $>
    <% } %>
    ></x-inputs.password>
    <% } else { %>
    <x-inputs.password
        name="<$ this.name $>"
        label="<$ this.label $>"
    <$ this.min ? ` minlength="${this.min}"` : '' $>
    <$ this.max ? ` maxlength="${this.max}"` : '' $>
    <$ this.placeholder ? ` placeholder="${this.placeholder}"` : '' $>
    <% if(!this.crud.isForLivewire) { %>
    <$ this.getRequiredAttributeForTemplate() $>
    <% } %>
    ></x-inputs.password>
    <% } %>
</x-inputs.group>