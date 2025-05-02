<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    @vite('resources/css/app.css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('checkout-form');
            const errorBox = document.getElementById('error-box');

            form.addEventListener('submit', async function (e) {
                e.preventDefault();

                const button = document.querySelector('button[type="submit"]');
                const buttonOrignalText = button.innerHTML;
                button.innerHTML = 'Processando...';
                button.disabled = true;

                // Clear previous errors
                errorBox.innerHTML = '';
                errorBox.classList.add('hidden');

                const formData = new FormData(form);
                const data = Object.fromEntries(formData.entries());

                data['value'] *= 100; // converte para centavos

                // Group nested fields properly
                const formattedData = {
                    ...data,
                    creditCard: {
                        number: data['creditCard[number]'],
                        expiryMonth: data['creditCard[expiryMonth]'],
                        expiryYear: data['creditCard[expiryYear]'],
                        ccv: data['creditCard[ccv]'],
                    },
                    creditCardHolderInfo: {
                        postalCode: data['creditCardHolderInfo[postalCode]'],
                        addressNumber: data['creditCardHolderInfo[addressNumber]'],
                        addressComplement: data['creditCardHolderInfo[addressComplement]'],
                    }
                };

                // Remove flat fields to prevent duplication
                [
                    'creditCard[number]',
                    'creditCard[expiryMonth]',
                    'creditCard[expiryYear]',
                    'creditCard[ccv]',
                    'creditCardHolderInfo[postalCode]',
                    'creditCardHolderInfo[addressNumber]',
                    'creditCardHolderInfo[addressComplement]'
                ].forEach(field => delete formattedData[field]);

                try {
                    const response = await fetch('{{ route('api.payments.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(formattedData)
                    });

                    const result = await response.json();

                    if (!response.ok) {
                        if (result.errors) {
                            showErrors(result.errors);
                        } else if (result.error ?? result.message) {
                            showErrors({default: [result.error ?? result.message]});
                        } else {
                            errorBox.innerHTML = 'Parece que o servidor está com problemas para atender sua ' +
                                'solicitação no momento. Tente mais tarde, por favor.';
                            errorBox.classList.remove('hidden');
                        }
                    } else {
                        alert('Pagamento efetuado com sucesso!');
                        form.reset();
                        toggleCreditCardFields(); // Reset validation state
                    }
                } catch (err) {
                    errorBox.innerHTML = 'A requisição falhou. Tente novamente.';
                    errorBox.classList.remove('hidden');
                }

                button.innerHTML = buttonOrignalText;
                button.disabled = false;
            });

            function showErrors(errors) {
                const ul = document.createElement('ul');
                ul.classList.add('list-disc', 'pl-4');
                for (const key in errors) {
                    const li = document.createElement('li');
                    li.textContent = errors[key][0];
                    ul.appendChild(li);
                }
                errorBox.appendChild(ul);
                errorBox.classList.remove('hidden');
            }

            window.toggleCreditCardFields = function () {
                const method = document.querySelector('input[name="billingType"]:checked')?.value;
                const ccFields = document.querySelectorAll('.credit-card-field');
                ccFields.forEach(field => {
                    field.disabled = method !== 'CREDIT_CARD';
                    field.required = method === 'CREDIT_CARD' && field.name !== 'creditCardHolderInfo[addressComplement]';
                });

                const ccRequiredFields = document.querySelectorAll('span[data-cc]');
                ccRequiredFields.forEach(field => {
                    field.classList.toggle('hidden', method !== 'CREDIT_CARD');
                    field.classList.toggle('text-red-500', method === 'CREDIT_CARD');
                });
            }

            document.querySelectorAll('input[name="billingType"]').forEach(input => {
                input.addEventListener('change', toggleCreditCardFields);
            });

            toggleCreditCardFields();
        });
    </script>
</head>
<body class="bg-gray-100 text-gray-800 font-sans">
<div class="max-w-3xl mx-auto mt-10 p-6 bg-white rounded shadow">
    <h1 class="text-2xl font-bold mb-6">Checkout</h1>

    <div id="error-box" class="hidden mb-4 p-4 bg-red-100 text-red-800 rounded"></div>

    <form id="checkout-form" class="space-y-6">

        {{-- Personal Info --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="name" class="block text-sm font-medium">Nome</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}"
                       class="w-full border border-gray-300 p-2 rounded">
            </div>
            <div>
                <label for="cpfCnpj" class="block text-sm font-medium">CPF/CNPJ</label>
                <input type="text" name="cpfCnpj" id="cpfCnpj" value="{{ old('cpfCnpj') }}"
                       class="w-full border border-gray-300 p-2 rounded">
            </div>
            <div>
                <label for="phone" class="block text-sm font-medium">Celular</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                       class="w-full border border-gray-300 p-2 rounded">
            </div>
            <div>
                <label for="email" class="block text-sm font-medium">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}"
                       class="w-full border border-gray-300 p-2 rounded">
            </div>
            <div class="md:col-span-2">
                <label for="value" class="block text-sm font-medium">Valor (R$)</label>
                <input type="number" min="0" step="0.01" name="value" id="value" value="{{ old('value') }}"
                       class="w-full border border-gray-300 p-2 rounded">
            </div>
        </div>

        {{-- Payment Type --}}
        <div>
            <label class="block text-sm font-medium mb-2">Tipo de Pagamento</label>
            <div class="flex space-x-4">
                <label><input type="radio" name="billingType" value="PIX"
                              {{ old('billingType') === 'PIX' ? 'checked' : '' }} required> Pix</label>
                <label><input type="radio" name="billingType"
                              value="BOLETO" {{ old('billingType') === 'BOLETO' ? 'checked' : '' }}> Boleto</label>
                <label><input type="radio" name="billingType"
                              value="CREDIT_CARD" {{ old('billingType') === 'CREDIT_CARD' ? 'checked' : '' }}> Cartão de Crédito</label>
            </div>
        </div>

        {{-- Credit Card Fields --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div>
                <label for="creditCard_number" class="block text-sm font-medium">Número do Cartão<span
                        data-cc>*</span></label>
                <input type="text" name="creditCard[number]" id="creditCard_number"
                       value="{{ old('creditCard.number') }}"
                       class="w-full border border-gray-300 p-2 rounded credit-card-field">
            </div>
            <div>
                <label for="creditCard_ccv" class="block text-sm font-medium">Código de Verificação do Cartão (CVC)<span data-cc>*</span></label>
                <input type="text" name="creditCard[ccv]" id="creditCard_ccv" value="{{ old('creditCard.ccv') }}"
                       class="w-1/3 border border-gray-300 p-2 rounded credit-card-field">
            </div>
            <div>
                <label for="creditCard_expiryMonth" class="block text-sm font-medium">Mês de Validade do Cartão<span data-cc>*</span></label>
                <input type="text" name="creditCard[expiryMonth]" id="creditCard_expiryMonth"
                       value="{{ old('creditCard.expiryMonth') }}"
                       class="w-full border border-gray-300 p-2 rounded credit-card-field">
            </div>
            <div>
                <label for="creditCard_expiryYear" class="block text-sm font-medium">Ano de Validade do Cartão<span
                        data-cc>*</span></label>
                <input type="text" name="creditCard[expiryYear]" id="creditCard_expiryYear"
                       value="{{ old('creditCard.expiryYear') }}"
                       class="w-full border border-gray-300 p-2 rounded credit-card-field">
            </div>
            <div>
                <label for="creditCardHolderInfo_postalCode" class="block text-sm font-medium">CEP<span data-cc>*</span></label>
                <input type="text" name="creditCardHolderInfo[postalCode]" id="creditCardHolderInfo_postalCode"
                       value="{{ old('creditCardHolderInfo.postalCode') }}"
                       class="w-full border border-gray-300 p-2 rounded credit-card-field">
            </div>
            <div>
                <label for="creditCardHolderInfo_addressNumber" class="block text-sm font-medium">Número<span
                        data-cc>*</span></label>
                <input type="text" name="creditCardHolderInfo[addressNumber]" id="creditCardHolderInfo_addressNumber"
                       value="{{ old('creditCardHolderInfo.addressNumber') }}"
                       class="w-full border border-gray-300 p-2 rounded credit-card-field">
            </div>
            <div class="md:col-span-2">
                <label for="creditCardHolderInfo_addressComplement" class="block text-sm font-medium">Complemento</label>
                <input type="text" name="creditCardHolderInfo[addressComplement]"
                       id="creditCardHolderInfo_addressComplement"
                       value="{{ old('creditCardHolderInfo.addressComplement') }}"
                       class="w-full border border-gray-300 p-2 rounded credit-card-field">
            </div>
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Pagar agora!
        </button>
    </form>
</div>
</body>
</html>
