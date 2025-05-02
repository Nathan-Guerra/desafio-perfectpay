<div class="max-w-3xl mx-auto mt-10 p-6 bg-white rounded shadow">
    <h1 class="text-2xl font-bold mb-6">Checkout</h1>

    <x-error-box />

    <form id="checkout-form" class="space-y-6">
        @csrf
        <x-checkout.personal-info />
        <x-checkout.payment-type />
        <x-checkout.credit-card-fields />

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Pagar agora!
        </button>
    </form>
</div>
