{{-- resources/views/components/checkout/credit-card-fields.blade.php --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
    @php
        $fields = [
            ['creditCard[number]', 'Número do Cartão'],
            ['creditCard[ccv]', 'Código de Verificação do Cartão (CVC)'],
            ['creditCard[expiryMonth]', 'Mês de Validade do Cartão'],
            ['creditCard[expiryYear]', 'Ano de Validade do Cartão'],
            ['creditCardHolderInfo[postalCode]', 'CEP'],
            ['creditCardHolderInfo[addressNumber]', 'Número'],
            ['creditCardHolderInfo[addressComplement]', 'Complemento']
        ];
    @endphp

    @foreach ($fields as [$name, $label])
        <div class="{{ $loop->last ? 'md:col-span-2' : '' }}">
            <label for="{{ str_replace(['[', ']'], '_', $name) }}" class="block text-sm font-medium">
                {{ $label }} @if (!str_contains($label, 'Complemento'))<span data-cc>*</span>@endif
            </label>
            <input type="text"
                   name="{{ $name }}"
                   id="{{ str_replace(['[', ']'], '_', $name) }}"
                   value="{{ old(str_replace(['[', ']'], '.', $name)) }}"
                   class="w-full border border-gray-300 p-2 rounded credit-card-field">
        </div>
    @endforeach
</div>
