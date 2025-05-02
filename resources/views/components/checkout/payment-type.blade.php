<div>
    <label class="block text-sm font-medium mb-2">Tipo de Pagamento</label>
    <div class="flex space-x-4">
        @foreach (['PIX' => 'Pix', 'BOLETO' => 'Boleto', 'CREDIT_CARD' => 'Cartão de Crédito'] as $value => $label)
            <label>
                <input type="radio" name="billingType" value="{{ $value }}"
                    {{ old('billingType') === $value ? 'checked' : '' }}
                    {{ $loop->first ? 'required' : '' }}> {{ $label }}
            </label>
        @endforeach
    </div>
</div>
