<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    @foreach ([
        'name' => 'Nome',
        'cpfCnpj' => 'CPF/CNPJ',
        'phone' => 'Celular',
        'email' => 'Email'
    ] as $field => $label)
        <div>
            <label for="{{ $field }}" class="block text-sm font-medium">{{ $label }}</label>
            <input type="{{ $field === 'email' ? 'email' : 'text' }}"
                   name="{{ $field }}"
                   id="{{ $field }}"
                   value="{{ old($field) }}"
                   class="w-full border border-gray-300 p-2 rounded">
        </div>
    @endforeach

    <div class="md:col-span-2">
        <label for="value" class="block text-sm font-medium">Valor (R$)</label>
        <input type="number" min="0" step="0.01" name="value" id="value" value="{{ old('value') }}"
               class="w-full border border-gray-300 p-2 rounded">
    </div>
</div>
