<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TestController extends Controller
{
    public function index(){
        $books = Book::all();
        $carts=Cart::with(['book' , 'user' ])->get();

        return view('index' , compact('books' , 'carts' ));
    }
}
