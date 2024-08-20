<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        *{
            background-color: black;
            color: #ff9625;
            font-family: 'Courier New', Courier, monospace;
        }
    </style>
</head>
<body>
    <center>
    @foreach ($books as $bk)
    {{-- <img src="{{ asset('storage/images'.$bk->image) }}" alt="{{ $bk->image }}"> --}}
    <h3>{{$bk->name}}</h3>
    <h3>{{$bk->price}}</h3>

    @endforeach
    <h5>#####################################################################################</h5>
    @foreach ($carts as $cart )
    <h5>{{$cart->user->name}}</h5>
    <h5>{{$cart->book->name}}</h5>
    <h5>{{$cart->quantity}}</h5>
    @endforeach


    </center>

</body>
</html>
