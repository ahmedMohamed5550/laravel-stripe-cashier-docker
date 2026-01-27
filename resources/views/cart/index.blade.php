<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Shopping Cart Items') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <strong>Success!</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <strong>Error!</strong> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($cartItems != null && count($cartItems->courses) > 0)
                <div class="card bg-dark text-white border-secondary p-4">
                    @foreach ($cartItems->courses as $course)
                        <div class="d-flex justify-content-between align-items-center border-bottom border-secondary py-3">
                            <div class="flex-grow-1">
                                <a href="{{ route('courses.show', $course) }}" class="text-decoration-none">
                                    <h5 class="text-white mb-1">{{ $course->name }}</h5>
                                </a>
                                <p class="text-info mb-0 fw-bold">({{ $course->formatPrice() }})</p>
                            </div>
                            <a href="{{ route('cart.remove', $course) }}" class="btn btn-danger btn-m">Remove</a>
                        </div>
                    @endforeach

                    <!-- Total and Checkout Section -->
                    <div class="d-flex justify-content-between align-items-center pt-4">
                        <div>
                            <h5 class="text-white mb-0">
                                Total <span class="text-success">({{ $cartItems->totalPrice() }})</span>
                            </h5>
                        </div>
                        <a href="{{ route('checkout.lineItems') }}" class="btn btn-success btn-lg">Checkout</a>
                    </div>
                </div>
            @else
                <div class="alert alert-info w-100 text-center" role="alert">
                    Your cart is empty.
                </div>
            @endif
        </div>
    </div>
</x-app-layout>


