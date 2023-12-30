<x-inputs.group class="<$ this.project.css().getReponsiveClassesForInput(this) $>">
    <x-inputs.partials.label
        name="<$ this.crud.isForLivewire ? this.getLivewireSingleName() : this.name $>"
        label="{{ __('crud.<$ this.crud.model.plural.case('snakeCase') $>.inputs.<$ this.name.case('snakeCase') $>') }}"
    ></x-inputs.partials.label>
    <br>

    <input
        type="file"
    <% if(this.crud.isForLivewire) { %>
    name="<$ this.getLivewireSingleName() $>"
    id="<$ this.getLivewireSingleName() $>{{ $uploadIteration }}"
    wire:model="<$ this.getLivewireSingleName() $>"
    <% } else { %>
    name="<$ this.name $>"
    id="<$ this.name $>"
    <% } %>
    class="form-control-file"
    >

    <% if(this.crud.isManyToManyDetail) { %>
    @if($<$ this.getLivewireSingleName() $>)
    <% } else { %>
    @if($editing && $<$ this.crud.model.name.case('camelCase') $>-><$ this.name $>)
    <% } %>
    <div class="mt-2">
        <% let imageReference = this.crud.isManyToManyDetail ? this.getLivewireSingleName() : `${this.crud.model.name.case('camelCase')}->${this.name}` %>
        <a href="{{ \Storage::url($<$ imageReference $>) }}" target="_blank"><i class="icon ion-md-download"></i>&nbsp;Download</a>
    </div>
    @endif

    @error('<$ this.crud.isForLivewire ? this.getLivewireSingleName() : this.name $>')
    @include('components.inputs.partials.error')
    @enderror
</x-inputs.group>