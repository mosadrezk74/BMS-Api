
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Book Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  </head>
<body>

<center>
@foreach ($products as $product )
    <br> 
    <h5>{{$product->name}}</h5>
    <p>{{$product->price}}</p>
    <input type="hidden" name="id" value="{{$product->id}}">
    <button class="btn btn-primary">Add To Cart</button>

    <hr>
    @endforeach
</center>


















</body>
</html>