<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Confirmação de Pagamento</title>
    @vite('resources/css/app.css')
    <script>
        const copyToClipboard = (text) => {
            navigator.clipboard.writeText(text).then(() => {
                alert('Código copiado!');
            }).catch(err => {
                alert('Falha ao copiar o código.');
            });
        }
    </script>
</head>
<body class="bg-gray-100 text-gray-800 font-sans">
<div class="max-w-2xl mx-auto mt-10 p-6 bg-white rounded shadow text-center">
    <h1 class="text-2xl font-bold mb-6">Confirmação de Pagamento</h1>

    @if ($paymentType === 'PIX')
        <div>
            <p class="mb-1">O seu pagamento PIX expira em {{ $dueDate }}</p>
            <p class="mb-4">Escaneie o QR Code abaixo para completar seu pagamento:</p>
            <img src="data:image/png;base64,{{ $pixImage }}" alt="PIX QR Code"
                 class="mx-auto w-50 h-50 object-contain border p-2 mb-1">
            <div class="flex items-center justify-center space-x-2">
                <textarea readonly rows="5" onload="fixTextAreaHeight"
                          class="border border-gray-300 p-2 rounded w-full max-w-lg text-center">{{ $pixCode }}</textarea>
                <button onclick="copyToClipboard('{{ $pixCode}}')"
                        class="bg-blue-600 text-white px-3 py-2 -1/4rounded hover:bg-blue-700">Copiar
                </button>
            </div>
        </div>

    @elseif ($paymentType === 'BOLETO')
        <div>
            <p class="mb-4">Use o código abaixo para pagar seu boleto:</p>
            <div class="flex items-center justify-center space-x-2">
                <input type="text" value="{{ $boletoCode }}" readonly
                       class="border border-gray-300 p-2 rounded w-full max-w-lg text-center">
                <button onclick="copyToClipboard('{{ $boletoCode }}')"
                        class="bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700">Copiar
                </button>
            </div>
        </div>

    @elseif ($paymentType === 'CREDIT_CARD')
        <div>
            <p class="mb-2">Seu pagamento já foi efetuado!</p>
            <p class="text-lg font-semibold">Status: <span class="text-blue-600">{{ ucfirst($status) }}</span></p>
        </div>
    @endif

    <div class="mt-8">
        <a href="{{ route('checkout') }}" class="text-blue-600 underline">Return to Checkout</a>
    </div>
</div>
</body>
</html>
