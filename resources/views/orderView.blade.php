<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order details</title>
</head>
<body >
 {{$order->user->customer->full}} order details:<br><br>
ordered products:
<ul>

@foreach($order->order_products as $product)

<li>{{$product->product->title}}  price:   {{$product->product->price}}$</li>
@endforeach
</ul>
subtotal : {{$order->subtotal}}$
order status  : {{$order->status}}
</body>
</html>