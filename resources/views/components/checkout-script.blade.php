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
                    alert('Pagamento efetuado com sucesso!' +
                        ' Vamos redirecioná-lo para a página de confirmação.'
                    );

                    form.reset();
                    toggleCreditCardFields(); // Reset validation state

                    window.location.href = `/payments/${result.data.payment_uuid}`;
                }
            } catch (err) {
                console.error('Error:', err);
                console.log(err.data);
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
