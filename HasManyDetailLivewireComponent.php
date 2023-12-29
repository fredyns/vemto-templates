<?php
<# TEMPLATE VARIABLES #>
<% let parentCrudModel = this.crud.model %>
<% let relatedCrudModel = this.relatedCrud.model %>
<% let modelsNamespace = this.projectHelper.getModelsNamespace() %>
<% let parentCrudModelNameCamelCase = parentCrudModel.name.case('camelCase') %>
<% let relatedCrudModelNameCamelCase = relatedCrudModel.name.case('camelCase') %>
<% let relatedCrudModelPluralCamelCase = relatedCrudModel.plural.case('camelCase') %>
<% let relForeignKeyName = this.relationship.foreignKey.name %>
<####>

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\View\View;
use <$ modelsNamespace $>\<$ parentCrudModel.name $>;
use <$ modelsNamespace $>\<$ relatedCrudModel.name $>;
<% if(this.relatedCrud.hasBelongsToInputs()) { %>
    <###>
    <% for(let input of this.relatedCrud.getBelongsToInputs()) { %>
        <##>
        <% if(input.relationship.modelId !== relatedCrudModel.id) { %>
        use <$ modelsNamespace $>\<$ input.relationship.model.name $>;
        <% } %>
    <% } %>
<% } %>
<###>
<% if(this.relatedCrud.hasPasswordInputs()) { %>
use Illuminate\Support\Facades\Hash;
<% } %>
<###>
<% if(this.relatedCrud.hasFileOrImageInputs()) { %>
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
<% } %>
<###>
<% if(relatedCrudModel.hasUniqueFields() && !this.hasSpecificRequests) { %>
use Illuminate\Validation\Rule;
<% } %>
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class <$ this.fileNameWithoutExtension $> extends Component
{
    <% if(this.relatedCrud.hasFileOrImageInputs()) { %>
    use WithFileUploads;
    <% } %>
    use AuthorizesRequests;
    use WithPagination;

    public <$ parentCrudModel.name $> $<$ parentCrudModelNameCamelCase $>;
    public <$ relatedCrudModel.name $> $<$ relatedCrudModelNameCamelCase $>;
    <% if(this.relatedCrud.hasBelongsToInputs()) { %>
        <# BelongsTo inputs grouped by collection name #>
        <% for(let input of this.relatedCrud.getBelongsToInputsGroupedByCollection()) { %>
            <###>
            <% if(input.relationship.modelId !== relatedCrudModel.id) { %>
            public $<$ input.getVariableForRelationshipSelect() $> = [];
            <% } %>
        <% } %>
    <% } %>
    <###>
    <% if(this.relatedCrud.hasPasswordInputs()) { %>
        <# Password inputs property #>
        <% for(let input of this.relatedCrud.getPasswordInputs()) { %>
        public $<$ input.getLivewireSingleName() $>;
        <% } %>
    <% } %>
    <###>
    <% if(this.relatedCrud.hasFileOrImageInputs()) { %>
        <# File inputs property #>
        <% for(let input of this.relatedCrud.getFileAndImageInputs()) { %>
        public $<$ input.getLivewireSingleName() $>;
        <% } %>
    public $uploadIteration = 0;
    <% } %>
    <###>
    <# Date inputs property #>
    <% for(let input of this.relatedCrud.getDateAndDatetimeInputs()) { %>
    public $<$ input.getLivewireSingleName() $>;
    <% } %>
    
    public $selected = [];
    public $editing = false;
    public $allSelected = false;
    public $showingModal = false;
    
    public $modalTitle = 'New <$ relatedCrudModel.name $>';

    protected $rules = [
        <% for (let input of this.relatedCrud.inputs) { %>
            <###>
            <% if(input.hasValidation()) { %>
            <% let inputName = (input.canUseLivewireReference()) ? input.livewireInputReference : input.getLivewireSingleName() %>
            '<$ inputName $>' => <$ input.getValidationForTemplate() $>,
            <% } %>
        <% } %>
    ];

    public function mount(<$ parentCrudModel.name $> $<$ parentCrudModelNameCamelCase $>): void
    {
        $this-><$ parentCrudModelNameCamelCase $> = $<$ parentCrudModelNameCamelCase $>;
        <% if(this.relatedCrud.hasBelongsToInputs()) { %>
            <###>
            <% for(let input of this.relatedCrud.getBelongsToInputsGroupedByCollection()) { %>
                <# Default values for belongsTo inputs #>
                <% if(input.relationship.modelId !== relatedCrudModel.id) { %>
                <% let relationshipModel = input.relationship.model %>
                $this-><$ input.getVariableForRelationshipSelect() $> = <$ relationshipModel.name $>::pluck('<$ relationshipModel.getLabelFieldName() $>', '<$ relationshipModel.getPrimaryKeyName() $>');
                <% } %>
            <% } %>
        <% } %>
        $this->reset<$ relatedCrudModel.name.case('pascalCase') $>Data();
    }

    public function reset<$ relatedCrudModel.name.case('pascalCase') $>Data(): void
    {
        $this-><$ relatedCrudModelNameCamelCase $> = new <$ relatedCrudModel.name $>();

        <# Reset password inputs #>
        <% if(this.relatedCrud.hasPasswordInputs()) { %>
            <###>
            <% for(let input of this.relatedCrud.getPasswordInputs()) { %>
            $this-><$ input.getLivewireSingleName() $> = '';
            <% } %>
        <% } %>
        <# Reset file inputs #>
        <% if(this.relatedCrud.hasFileOrImageInputs()) { %>
            <###>
            <% for(let input of this.relatedCrud.getFileAndImageInputs()) { %>
            $this-><$ input.getLivewireSingleName() $> = null;
            <% } %>
        <% } %>
        <# Reset date inputs #>
        <% for(let input of this.relatedCrud.getDateAndDatetimeInputs()) { %>
            <###>
            <% if(input.isLinkedToField()) { %>
            $this-><$ input.getLivewireSingleName() $> = null;
            <% } %>
        <% } %>
        <# Reset select inputs #>
        <% if(this.relatedCrud.hasSelectInputs()) { %>
            <###>
            <% for(let input of this.relatedCrud.getSelectInputs()) { %>
                <###>
                <% if(input.defaultValue) { %>
                $this-><$ input.getLivewirePropertyReference() $> = '<$ input.defaultValue $>';
                <% } else { %>
                <% let firstItem = input.getFirstItem() %>
                $this-><$ input.getLivewirePropertyReference() $> = <$ firstItem ? `'${firstItem.value}'` : 'null' $>;
                <% } %>
            <% } %>
        <% } %>

        $this->dispatchBrowserEvent('refresh');
    }

    public function new<$ relatedCrudModel.name.case('pascalCase') $>(): void
    {        
        $this->editing = false;
        $this->modalTitle = trans('crud.<$ this.relatedCrud.name.case('snakeCase') $>.new_title');
        $this->reset<$ relatedCrudModel.name.case('pascalCase') $>Data();

        $this->showModal();
    }

    public function edit<$ relatedCrudModel.name.case('pascalCase') $>(<$ relatedCrudModel.name $> $<$ relatedCrudModelNameCamelCase $>): void
    {
        $this->editing = true;
        $this->modalTitle = trans('crud.<$ this.relatedCrud.name.case('snakeCase') $>.edit_title');
        $this-><$ relatedCrudModelNameCamelCase $> = $<$ relatedCrudModelNameCamelCase $>;

        <% for(let input of this.relatedCrud.getDateAndDatetimeInputs()) { %>
            <###>
            <% if(input.isLinkedToField()) { %>
            $this-><$ input.getLivewireSingleName() $> = optional($this-><$ relatedCrudModelNameCamelCase $>-><$ input.name $>)->format('Y-m-d');
            <% } %>
        <% } %>

        $this->dispatchBrowserEvent('refresh');

        $this->showModal();
    }

    public function showModal(): void
    {
        $this->resetErrorBag();
        $this->showingModal = true;
    }

    public function hideModal(): void
    {
        $this->showingModal = false;
    }

    public function save(): void
    {
        <% if(this.relatedCrud.hasDifferentUpdateValidation()) { %>
        if(!$this-><$ relatedCrudModelNameCamelCase $>-><$ relForeignKeyName $>) {
            $this->validate();
        } else {
            $this->validate([
                <% for (let input of this.relatedCrud.inputs) { %>
                    <###>
                    <% let inputName = (input.canUseLivewireReference()) ? input.livewireInputReference : input.getLivewireSingleName() %>
                    <% if(input.hasUpdateValidation()) { %>
                    '<$ inputName $>' => <$ input.getUpdateValidationWithUniqueRules(true) $>,
                    <% } else { %>
                    '<$ inputName $>' => 'nullable',
                    <% } %>
                <% } %>
            ]);
        }
        <% } else { %>
        $this->validate();
        <% } %>

        if(!$this-><$ relatedCrudModelNameCamelCase $>-><$ relForeignKeyName $>) {
            $this->authorize('create', <$ relatedCrudModel.name $>::class);

            $this-><$ relatedCrudModelNameCamelCase $>-><$ relForeignKeyName $> = $this-><$ parentCrudModelNameCamelCase $>-><$ parentCrudModel.getPrimaryKeyName() $>;
        } else {
            $this->authorize('update', $this-><$ relatedCrudModelNameCamelCase $>);
        }

        <# Password Inputs #>
        <% if(this.relatedCrud.hasPasswordInputs()) { %>
            <###>
            <% for(let input of this.relatedCrud.getPasswordInputs()) { %>
            if(!empty($this-><$ input.getLivewireSingleName() $>)) {
                $this-><$ relatedCrudModelNameCamelCase $>-><$ input.name $> = Hash::make($this-><$ input.getLivewireSingleName() $>);
            }
            <% } %>

        <% } %>

        <# File Inputs #>
        <% if(this.relatedCrud.hasFileOrImageInputs()) { %>
            <###>
            <% for(let input of this.relatedCrud.getFileAndImageInputs()) { %>
            if($this-><$ input.getLivewireSingleName() $>) {
                $this-><$ relatedCrudModelNameCamelCase $>-><$ input.name $> = $this-><$ input.getLivewireSingleName() $>->store('public');
            }
            
            <% } %>
        <% } %>

        <# Json Inputs #>
        <% if(this.relatedCrud.hasJsonInputs()) { %>
            <###>
            <% for(let input of this.relatedCrud.getJsonInputs()) { %>
            $this-><$ relatedCrudModelNameCamelCase $>-><$ input.name $> = json_decode($this-><$ relatedCrudModelNameCamelCase $>-><$ input.name $>, true);
            
            <% } %>
        <% } %>

        <# Date Inputs #>
        <% for(let input of this.relatedCrud.getDateAndDatetimeInputs()) { %>
            <###>
            <% if(input.isLinkedToField()) { %>
            $this-><$ relatedCrudModelNameCamelCase $>-><$ input.name $> = \Carbon\Carbon::make($this-><$ input.getLivewireSingleName() $>);
            <% } %>
        <% } %>

        $this-><$ relatedCrudModelNameCamelCase $>->save();

        <% if(this.relatedCrud.hasFileOrImageInputs()) { %>
        $this->uploadIteration++;
        <% } %>

        $this->hideModal();
    }

    public function destroySelected(): void
    {
        $this->authorize('delete-any', <$ relatedCrudModel.name $>::class);

        <% if(this.relatedCrud.hasFileOrImageInputs()) { %>
        collect($this->selected)->each(function(string $id) {
            $<$ relatedCrudModelNameCamelCase $> = <$ relatedCrudModel.name $>::findOrFail($id);
            
            <# Destroy file when destroying the item #>
            <% for(let input of this.relatedCrud.getFileAndImageInputs()) { %>
            if($<$ relatedCrudModelNameCamelCase $>-><$ input.name $>) {
                Storage::delete($<$ relatedCrudModelNameCamelCase $>-><$ input.name $>);
            }

            <% } %>
            $<$ relatedCrudModelNameCamelCase $>->delete();
        });
        <% } else { %>
        <$relatedCrudModel.name $>::whereIn('<$relatedCrudModel.getPrimaryKeyName() $>', $this->selected)->delete();
        <% } %>

        $this->selected = [];
        $this->allSelected = false;

        $this->reset<$ relatedCrudModel.name.case('pascalCase') $>Data();
    }

    public function toggleFullSelection(): void
    {
        if(!$this->allSelected) {
            $this->selected = [];
            return;
        }

        foreach ($this-><$ parentCrudModelNameCamelCase $>-><$ relatedCrudModelPluralCamelCase $> as $<$ relatedCrudModelNameCamelCase $>) {
            array_push($this->selected, $<$ relatedCrudModelNameCamelCase $>->id);
        }
    }

    public function render(): View
    {
        return view('livewire.<$ this.detail.getLivewireViewName() $>', [
            '<$ relatedCrudModelPluralCamelCase $>' => $this-><$ parentCrudModelNameCamelCase $>-><$ relatedCrudModelPluralCamelCase $>()->paginate(100)
        ]);
    }
}