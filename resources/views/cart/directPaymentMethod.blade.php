<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Direct Payment Method') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 text-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- Stripe Elements Placeholder -->
                    <form method="POST" id="payment-form" action="{{ route('checkout.storeDirectPaymentMethod') }}">
                        @csrf
                        <input type="hidden" id ="payment_method" name="payment_method">
                        <div id="card-element"></div>

                        <button id="card-button" class = "btn btn-primary mt-4" type="button">
                            Process Payment
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>




<script>
    const stripe = Stripe(@json(env('STRIPE_KEY')));
    const elements = stripe.elements();
    const cardElement = elements.create('card', {
        style: {
            base: {
                color: '#ffffff', // Text color for input
                '::placeholder': {
                    color: '#9ca3af', // Placeholder text color
                }
            },
            invalid: {
                color: '#ef4444', // Text color when invalid
            }
        }
    });
    cardElement.mount('#card-element');

    const cardButton = document.getElementById('card-button');
    cardButton.addEventListener('click', async (e) => {
        const { paymentMethod, error } = await stripe.createPaymentMethod(
            'card', cardElement,
        );

        if (error) {
            console.log(error);
        } else {
            // alert('Payment method created successfully!');
            console.log(paymentMethod);
            document.getElementById('payment_method').value = paymentMethod.id;
            document.getElementById('payment-form').submit();
        }
    });

</script>


</x-app-layout>
