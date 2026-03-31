@extends('layout')
@section('content')


<div class="container">
    <?php $total = 0; ?>

    @if(session('cart'))
    <table class="table">
        <thead>
            <tr>
                <th>Book</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach(session('cart') as $id => $details)
            <tr>
                <td>{{ $details['title'] }}</td>
                <td>{{ $details['quantity'] }}</td>
                <td>${{ $details['price'] }}</td>
                <td>{{ $details['price'] * $details['quantity'] }}</td>
            </tr>

            <?php $total += $details['price'] * $details['quantity']; ?>

            @endforeach
            <tr>
                <td colspan="3" class="text-right"><strong>Total Price</strong></td>
                <td><strong>${{ $total }}</strong></td>
            </tr>
            <form action="{{ route('checkout') }}" method="POST">
                @csrf
                <tr></tr>
                    <td colspan="3" class="text-right"><strong>Total Price</strong></td>
                    <td><strong>${{ $total }}</strong></td>
                </tr>
                <a type="submit" href="{{ route('checkout') }}" class="btn btn-primary">Checkout</a>
            </form>

        </tbody>
    </table>
@else
    <p>Your cart is empty.</p>
@endif

@endsection
