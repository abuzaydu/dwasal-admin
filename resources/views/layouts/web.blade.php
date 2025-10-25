<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>DWASAL - Dodoma Washed Sand Limited</title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport">
        <meta content="Washed Sand, Stone, Blocks and all Building Materials" name="keywords">
        <meta content="Washed Sand, Stone, Blocks and all Building Materials" name="description">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Favicon -->
        <link href="{{ asset('img/favicon.png') }}" rel="icon">

        <!-- Google Font -->
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

        <!-- CSS Libraries -->
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
        <script src="https://kit.fontawesome.com/d0183c4010.js" crossorigin="anonymous"></script>
        <link href="{{ asset('lib/flaticon/font/flaticon.css') }}" rel="stylesheet"> 
        <link href="{{ asset('lib/animate/animate.min.css') }}" rel="stylesheet">
        <link href="{{ asset('lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">
        <link href="{{ asset('lib/lightbox/css/lightbox.min.css') }}" rel="stylesheet">
        <link href="{{ asset('lib/slick/slick.css') }}" rel="stylesheet">
        <link href="{{ asset('lib/slick/slick-theme.css') }}" rel="stylesheet">

        <link rel="stylesheet" href="{{ asset('css/lib/vendor.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/lib/plugins.min.css') }}">
        <!-- Template Stylesheet -->
        <link href="{{ asset('css/main.css') }}" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="{{ asset('css/custom.css') }}">
        <!-- @yield('page-styles') -->

        @yield('head-script')
    </head>
    <?php

        $gservices = App\Models\GeneralService::where('is_active', true)->select('name', 'short_description', 'service_img')->get();
        $abouts = App\Models\About::where('is_active', true)->select('id', 'title')->get();
        // $prodcategories = App\Models\Category::select('id', 'name')->get();

        $cartitems = null;
        $user = Auth::user();
        if (!is_null($user)) {
            $cart = App\Models\Cart::where('user_id', $user->id)->first();
            if (!is_null($cart)) {
                $cartitems = App\Models\CartItem::where('cart_id', $cart->id)->join('products', 'products.id', '=', 'cart_items.product_id')->select('cart_items.id', 'name', 'image_url', 'quantity', 'price')->get();
            }
        }
    ?>
    <body>
        <div class="wrapper">
            <!-- Top Bar Start -->
            <div class="top-bar">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <div class="col-lg-4 col-md-12">
                            <div class="logo">
                                <a href="{{ url('/') }}">
                                    <h1>DWASAL</h1>
                                    <!-- <img src="img/logo.jpg" alt="Logo"> -->
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-8 col-md-7 d-none d-lg-block">
                            <div class="row">
                                <div class="col-4">
                                    <div class="top-bar-item">
                                        <div class="top-bar-icon">
                                            <i class="flaticon-calendar"></i>
                                        </div>
                                        <div class="top-bar-text">
                                            <h3>Opening Hour</h3>
                                            <p>Mon - Sat, 8:00 - 17:00</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="top-bar-item">
                                        <div class="top-bar-icon">
                                            <i class="flaticon-call"></i>
                                        </div>
                                        <div class="top-bar-text">
                                            <h3>Call Us</h3>
                                            <p><a href="tel:+255758458004" style="color: #FF7518">+255 758 458 004</a></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="top-bar-item">
                                        <div class="top-bar-icon">
                                            <i class="flaticon-send-mail"></i>
                                        </div>
                                        <div class="top-bar-text">
                                            <h3>Email Us</h3>
                                            <p><a href="mailto:info@dwasal.co.tz" style="color: #FF7518;">info@dwasal.co.tz</a></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Top Bar End -->

            <!-- Nav Bar Start -->
            <div class="nav-bar">
                <div class="container-fluid">
                    <nav class="navbar navbar-expand-lg navbar-dark">
                        <a href="#" class="navbar-brand">MENU</a>
                        <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
                            <span class="navbar-toggler-icon"></span>
                        </button>

                        <div class="collapse navbar-collapse justify-content-between" id="navbarCollapse">
                            <div class="navbar-nav mr-auto">
                                <div class="nav-item dropdown">
                                    <a href="{{ url('product') }}" class="nav-link dropdown-toggle {{ request()->is('product') ? 'active' : '' }}" data-toggle="dropdown">Products</a>
                                    <div class="dropdown-menu">
                                        <div class="container" >
                                            <a href="{{ url('std-washed-sand')}}" class="dropdown-item"><i class="fas fa-caret-right pe-2"></i> Standard Washed Sand</a>
                                            <a href="{{ url('fine-washed-sand')}}" class="dropdown-item"><i class="fas fa-caret-right pe-2"></i> Fine Washed Sand</a>
                                            <a href="{{ url('coarse-washed-sand')}}" class="dropdown-item"><i class="fas fa-caret-right pe-2"></i> Coarse Washed Sand</a>
                                            <!-- <a href="{{ url('concret-block')}}" class="dropdown-item"><i class="fas fa-caret-right pe-2"></i> Concrete Blocks</a> -->
                                        </div>
                                    </div>
                                </div>
                                <div class="nav-item dropdown">
                                    <a href="#" class="nav-link dropdown-toggle {{ request()->is('sand') || request()->is('blocks') ? 'active' : '' }}" data-toggle="dropdown">How We Make It</a>
                                    <div class="dropdown-menu">
                                        <a href="{{ url('sand')}}" class="dropdown-item"> <i class="fas fa-caret-right pe-2"></i> Washed Sand </a>
                                        <!-- <a href="{{ url('block') }}" class="dropdown-item"><i class="fas fa-caret-right pe-2"></i> Blocks</a> -->
                                    </div>
                                </div>
                                <a href="{{ url('why-choose-us') }}" class="nav-item nav-link {{ request()->is('why-choose-us') ? 'active' : '' }}">Why Choose Us</a>
                                <div class="nav-item dropdown mega-menu position-static">
                                    <a href="{{ url('about-us') }}" class="nav-link dropdown-toggle {{ request()->is('about-us') || request()->is('what-we-do') || request()->is('how-we-do-it') || request()->is('executive-team') || request()->is('careers') || request()->is('case-studies') ? 'active' : '' }}" data-toggle="dropdown"  id="navbarDropdown" role="button" data-mdb-toggle="dropdown" aria-expanded="false">About Us</a>
                                    <div class="dropdown-menu w-100 mt-0 custom-menu" aria-labelledby="navbarDropdown" style="border-top-left-radius: 0;border-top-right-radius: 0; padding: 0;">
                                        <div class="row my-0 pl-3 pr-3">
                                            <a href="{{ url('about-us')}}" class="col-md-6 col-xl-3 col-sm-12 mb-0 menu-item {{ request()->is('about-us') ? 'active' : '' }}">
                                               <i class="fas fa-caret-right pe-2"></i>   About DWASAL
                                            </a>
                                            <a href="{{ url('what-we-do')}}" class="col-md-6 col-xl-3 col-sm-12 mb-0 menu-item {{ request()->is('what-we-do') ? 'active' : '' }}">
                                               <i class="fas fa-caret-right pe-2"></i>   What We Do
                                            </a>
                                            <a href="{{ url('how-we-do-it')}}" class="col-md-6 col-xl-3 col-sm-12 mb-0 menu-item {{ request()->is('how-we-do-it') ? 'active' : '' }}">
                                                <i class="fas fa-caret-right pe-2"></i>  How We Do It
                                            </a>
                                            <a href="{{ url('executive-team')}}" class="col-md-6 col-xl-3 col-sm-12 mb-0 menu-item {{ request()->is('executive-team') ? 'active' : '' }}">
                                                <i class="fas fa-caret-right pe-2"></i>  Executive Team
                                            </a>
                                            <a href="{{ url('careers')}}" class="col-md-6 col-xl-3 col-sm-12 mb-0 menu-item {{ request()->is('careers') ? 'active' : '' }}">
                                               <i class="fas fa-caret-right pe-2"></i>   Working at DWASAL
                                            </a>
                                            <a href="{{ url('case-studies')}}" class="col-md-6 col-xl-3 col-sm-12 mb-0 menu-item {{ request()->is('case-studies') ? 'active' : '' }}">
                                               <i class="fas fa-caret-right pe-2"></i>   Case Studies
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <a href="{{ url('contact') }}" class="nav-item nav-link {{ request()->is('contact') ? 'active' : '' }}">Contact Us</a>
                            </div>
                            <div class="ml-auto">
                                <button class="btn btn-primary" data-toggle="modal" data-src="#" data-target="#quoteModal"><i class="fa-solid fa-comments"></i> Let's Talk</button>
                                <!-- <button class="btn"  href="#offcanvas-wishlish">
                                    <i class="fa fa-heart"></i> Wishlist
                                    <span  class="badge badge-pill badge-success">3</span>
                                </button> -->
                                <a href="#offcanvas-add-cart" class="cart-btn btn-info  offcanvas-toggle" > <i class="fa fa-shopping-cart" aria-hidden="true"></i> Basket <span class="badge badge-pill badge-danger">@if(!is_null($cartitems)) {{$cartitems->count() }} @else {{ count((array) session('cart')) }}@endif</span></a>
                                <a class="cart-btn btn-info" href="{{ url('my-dashboard') }}"><i class="fa fa-user"></i> My Account</a>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
            <!-- Nav Bar End -->

            @yield('content')


            <!-- Quote Modal Start -->            
            <div class="modal fade" id="quoteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-body">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>        
                            <!-- 16:9 aspect ratio -->
                            <div class="quote-form">
                                <h3 class="text-center">Request A Quote</h3>
                                <div id="success"></div>
                                <form class="row g-1" name="sentMessage" id="quoteForm" novalidate="novalidate">
                                    <div class="col-md-6 control-group">
                                        <input type="text" class="form-control" id="name" placeholder="Your Name" />
                                        <p class="help-block text-danger"></p>
                                    </div>
                                    <div class="col-md-6 control-group">
                                        <input type="email" class="form-control" id="email" placeholder="Your Email (Required)" required="required" data-validation-required-message="Please enter your email" />
                                        <p class="help-block text-danger"></p>
                                    </div>
                                    <div class="col-md-6 control-group">
                                        <input type="tel" class="form-control" id="phone" placeholder="Your Phone or Whatsapp(Required)" data-validation-required-message="Please enter your phone/Whatsapp number" />
                                        <p class="help-block text-danger"></p>
                                    </div>
                                    <div class="col-md-6 control-group">
                                        <input type="text" class="form-control" id="address" placeholder="Your Address" />
                                        <p class="help-block text-danger"></p>
                                    </div>
                                    <div class="col-md-12 control-group">
                                        <input type="text" class="form-control" id="product" placeholder="Product(s)" />
                                        <p class="help-block text-danger"></p>
                                    </div>
                                    <div class="col-md-12 control-group">
                                        <textarea class="form-control" rows="3" id="message" placeholder="Message(Required)" required="required" data-validation-required-message="Please enter your message"></textarea>
                                        <p class="help-block text-danger"></p>
                                    </div>
                                    <div class="col-md-12 text-center">
                                        <button class="btn" type="submit" id="sendMessageButton">Send A Message</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Quote Modal End -->


            <!-- Quote Modal Start -->            
            <div class="modal fade" id="quoteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-body">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>        
                            <!-- 16:9 aspect ratio -->
                            <div class="quote-form">
                                <h3 class="text-center">Request A Quote</h3>
                                <div id="success"></div>
                                <form class="row g-1" name="sentMessage" id="quoteForm" novalidate="novalidate">
                                    <div class="col-md-6 control-group">
                                        <input type="text" class="form-control" id="name" placeholder="Your Name" />
                                        <p class="help-block text-danger"></p>
                                    </div>
                                    <div class="col-md-6 control-group">
                                        <input type="email" class="form-control" id="email" placeholder="Your Email (Required)" required="required" data-validation-required-message="Please enter your email" />
                                        <p class="help-block text-danger"></p>
                                    </div>
                                    <div class="col-md-6 control-group">
                                        <input type="tel" class="form-control" id="phone" placeholder="Your Phone or Whatsapp(Required)" data-validation-required-message="Please enter your phone/Whatsapp number" />
                                        <p class="help-block text-danger"></p>
                                    </div>
                                    <div class="col-md-6 control-group">
                                        <input type="text" class="form-control" id="address" placeholder="Your Address" />
                                        <p class="help-block text-danger"></p>
                                    </div>
                                    <div class="col-md-12 control-group">
                                        <input type="text" class="form-control" id="product" placeholder="Product(s)" />
                                        <p class="help-block text-danger"></p>
                                    </div>
                                    <div class="col-md-12 control-group">
                                        <textarea class="form-control" rows="3" id="message" placeholder="Message(Required)" required="required" data-validation-required-message="Please enter your message"></textarea>
                                        <p class="help-block text-danger"></p>
                                    </div>
                                    <div class="col-md-12 text-center">
                                        <button class="btn" type="submit" id="sendMessageButton">Send A Message</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Quote Modal End -->

            <!-- Footer Start -->
            <div class="footer wow fadeIn" data-wow-delay="0.3s">
                <div class="footer-top">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-6 col-lg-3">
                                <div class="footer-contact">
                                    <h2>Office Contact</h2>
                                    <p><i class="fa fa-map-marker-alt"></i>41406 Mulebe, Msamalo, Chamwino District, P.O. Box 548 Dodoma, Tanzania </p>
                                    <p><i class="fa fa-phone-alt"></i>+255 758 458 004</p>
                                    <p><i class="fa fa-envelope"></i>info@dwasal.co.tz</p>
                                    <div class="footer-social">
                                        <a href=""><i class="fab fa-twitter"></i></a>
                                        <a href=""><i class="fab fa-facebook-f"></i></a>
                                        <a href=""><i class="fab fa-youtube"></i></a>
                                        <a href=""><i class="fab fa-instagram"></i></a>
                                        <a href=""><i class="fab fa-linkedin-in"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="footer-link">
                                    <h2>Services Areas</h2>
                                    @foreach($gservices as $key => $service)
                                    @if($key < 5)
                                    <a href="">{{$service->name}}</a>
                                    @endif
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="footer-link">
                                    <h2>Useful Pages</h2>
                                    <a href="{{ url('about') }}">About Us</a>
                                    <a href="{{ url('content') }}">Contact Us</a>
                                    <a href="{{ url('service') }}">Services</a>
                                    <a href="{{ url('product') }}">Products</a>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="newsletter">
                                    <h2>Newsletter</h2>
                                    <p>
                                        Subscribe to receive news updates of our products and services
                                    </p>
                                    <div class="form">
                                        <input class="form-control" placeholder="Email here">
                                        <button class="btn">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="footer-bottom">
                    <div class="container footer-menu">
                        <div class="f-menu">
                            <a href="{{ url('terms-and-conditions') }}">Terms & Conditions</a>
                            <a href="{{ url('privacy-policy') }}">Privacy policy</a>
                            <a href="{{ url('cookies') }}">Cookies</a>
                            <a href="{{ url('refund-policy') }}">Refund Policy</a>
                            <a href="{{ url('frequently-asked-questions') }}">FQAs</a>
                        </div>
                    </div>
                    <div class="container copyright">
                        <div class="row">
                            <div class="col-md-6">
                                <p>&copy; <a href="https://dwasal.co.tz">Dodoma Washed Sand Limited</a>, All Right Reserved.</p>
                            </div>
                            <div class="col-md-6">
                                <p>Designed By <a href="https://petopesa.co.tz" target="_blank">PETOPESA</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer End -->

            <a href="#" class="back-to-top"><i class="fa fa-chevron-up"></i></a>
        </div>

         <!-- ...:::: Start Offcanvas Addcart Section :::... -->
    <div id="offcanvas-add-cart" class="offcanvas offcanvas-rightside offcanvas-add-cart-section">
        <!-- Start Offcanvas Header -->
        <div class="offcanvas-header text-end">
            <button class="offcanvas-close"><i class="fa fa-times"></i></button>
        </div> <!-- End Offcanvas Header -->
        
        <!-- Start  Offcanvas Addcart Wrapper -->
        <div class="offcanvas-add-cart-wrapper">
            <h4 class="offcanvas-title">Basket</h4>
            @if((!is_null($cartitems) && $cartitems->count() > 0) || session('cart'))
            <ul class="offcanvas-cart">
                <?php $subtotal = 0; ?>
                @if(!is_null($cartitems))
                @foreach($cartitems as $key => $item)
                <?php $subtotal += ($item->quantity*$item->price); ?>
                <li class="offcanvas-cart-item-single">
                    <div class="offcanvas-cart-item-block">
                        <a href="" class="offcanvas-cart-item-image-link">
                            <img src="{{ asset('storage/'.$item->image_url) }}" alt="" class="offcanvas-cart-image">
                        </a>
                        <div class="offcanvas-cart-item-content">
                            <a href="" class="offcanvas-cart-item-link">{{ $item->name }}</a>
                            <div class="offcanvas-cart-item-details">
                                <span class="offcanvas-cart-item-details-quantity">{{ $item->quantity+0 }} x </span>
                                <span class="offcanvas-cart-item-details-price">{{ number_format($item->price) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="offcanvas-cart-item-delete text-end">
                        <a href="#" class="offcanvas-cart-item-delete"><i class="fa fa-trash-o"></i></a>
                    </div>
                </li>
                @endforeach
                @else
                @if(session('cart'))
                @foreach(session('cart') as $id => $details)
                <?php $subtotal += ($details['quantity']*$details['price']); ?>
                <li class="offcanvas-cart-item-single">
                    <div class="offcanvas-cart-item-block">
                        <a href="" class="offcanvas-cart-item-image-link">
                            <img src="{{ asset('storage/'.$details['img_url'])}}" alt="" class="offcanvas-cart-image">
                        </a>
                        <div class="offcanvas-cart-item-content">
                            <a href="" class="offcanvas-cart-item-link">{{ $details['name'] }}</a>
                            <div class="offcanvas-cart-item-details">
                                <span class="offcanvas-cart-item-details-quantity">{{ $details['quantity']}} x </span>
                                <span class="offcanvas-cart-item-details-price">{{ $details['price'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="offcanvas-cart-item-delete text-end">
                        <a href="#" onclick="removeFromCart('<?php echo $id; ?>')" class="offcanvas-cart-item-delete"><i class="fa fa-trash-o"></i></a>
                    </div>
                </li>
                @endforeach
                @endif
                @endif
            </ul>
            <div class="offcanvas-cart-total-price">
                <span class="offcanvas-cart-total-price-text">Subtotal:</span>
                <span class="offcanvas-cart-total-price-value">{{number_format($subtotal, 2, '.', ',') }}</span>
            </div>
            <ul class="offcanvas-cart-action-button">
                <li class="offcanvas-cart-action-button-list"><a href="{{ url('cart') }}" class="offcanvas-cart-action-button-link">View Cart</a></li>
                <li class="offcanvas-cart-action-button-list"><a href="{{ url('checkout') }}" class="offcanvas-cart-action-button-link">Checkout</a></li>
            </ul>
            @else
            <div class="empty-cart text-center">
                <img src="{{ asset('img/empty-cart.webp')}}" width="300"><br>
                <strong class="text-warning">Sorry, Your cart is Empty.<br> <a href="{{ url('product') }}" class="text-primary">Please Continue Shopping</a></strong>
            </div>
            @endif
        </div> <!-- End  Offcanvas Addcart Wrapper -->

    </div> <!-- ...:::: End  Offcanvas Addcart Section :::... -->

    <!-- ...:::: Start Offcanvas Mobile Menu Section:::... -->
    <div id="offcanvas-wishlish" class="offcanvas offcanvas-rightside offcanvas-add-cart-section">
        <!-- Start Offcanvas Header -->
        <div class="offcanvas-header text-end">
            <button class="offcanvas-close"><i class="fa fa-times"></i></button>
        </div> <!-- ENd Offcanvas Header -->

        <!-- Start Offcanvas Mobile Menu Wrapper -->
        <div class="offcanvas-wishlist-wrapper">
            <h4 class="offcanvas-title">Wishlist</h4>
            <ul class="offcanvas-wishlist">
                <li class="offcanvas-wishlist-item-single">
                    <div class="offcanvas-wishlist-item-block">
                        <a href="" class="offcanvas-wishlist-item-image-link">
                            <img src="" alt="" class="offcanvas-wishlist-image">
                        </a>
                        <div class="offcanvas-wishlist-item-content">
                            <a href="" class="offcanvas-wishlist-item-link">Car Wheel</a>
                            <div class="offcanvas-wishlist-item-details">
                                <span class="offcanvas-wishlist-item-details-quantity">1 x </span>
                                <span class="offcanvas-wishlist-item-details-price">$49.00</span>
                            </div>
                        </div>
                    </div>
                    <div class="offcanvas-wishlist-item-delete text-end">
                        <a href="#" class="offcanvas-wishlist-item-delete"><i class="fa fa-trash-o"></i></a>
                    </div>
                </li>
            </ul>
            <ul class="offcanvas-wishlist-action-button">
                <li class="offcanvas-wishlist-action-button-list"><a href="wishlist.html" class="offcanvas-wishlist-action-button-link">View wishlist</a></li>
            </ul>
        </div> <!-- End Offcanvas Mobile Menu Wrapper -->

    </div> <!-- ...:::: End Offcanvas Mobile Menu Section:::... -->

        <!-- JavaScript Libraries -->
        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
        <script src="{{ asset('lib/easing/easing.min.js') }}"></script>
        <script src="{{ asset('lib/wow/wow.min.js') }}"></script>
        <script src="{{ asset('lib/owlcarousel/owl.carousel.min.js') }}"></script>
        <script src="{{ asset('lib/isotope/isotope.pkgd.min.js') }}"></script>
        <script src="{{ asset('lib/lightbox/js/lightbox.min.js') }}"></script>
        <script src="{{ asset('lib/waypoints/waypoints.min.js') }}"></script>
        <script src="{{ asset('lib/counterup/counterup.min.js') }}"></script>
        <script src="{{ asset('lib/slick/slick.min.js') }}"></script>

        <script src="{{ asset('js/lib/vendor.min.js') }}"></script>
        <script src="{{ asset('js/lib/plugins.min.js') }}"></script>
        <!-- Contact Javascript File -->
        <script src="{{ asset('js/jqBootstrapValidation.min.js') }}"></script>
        <!-- Template Javascript -->
        <script src="{{ asset('js/main.js') }}"></script>

        <!-- Main JS -->
        <script src="{{ asset('js/lib/main.js') }}"></script>
        @yield('page-scripts')

        <script>
            $(function () {

                $("#quoteForm input, #quoteForm textarea").jqBootstrapValidation({
                    preventSubmit: true,
                    submitError: function ($form, event, errors) {
                    },
                    submitSuccess: function ($form, event) {
                        event.preventDefault();
                        var name = $("input#name").val();
                        var email = $("input#email").val();
                        var phone = $("input#phone").val();
                        var address = $("input#address").val();
                        var product = $("input#product").val();
                        var message = $("textarea#message").val();

                        $this = $("#sendMessageButton");
                        $this.prop("disabled", true);

                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });

                        $.ajax({
                            url: "{{url('quote-request-msg')}}",
                            type: "POST",
                            data: {
                                name: name,
                                email: email,
                                phone: phone,
                                address: address,
                                product: product,
                                message: message
                            },
                            cache: false,
                            success: function () {
                                $('#success').html("<div class='alert alert-success'>");
                                $('#success > .alert-success').html("<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;")
                                        .append("</button>");
                                $('#success > .alert-success')
                                        .append("<strong>Your message has been sent. </strong>");
                                $('#success > .alert-success')
                                        .append('</div>');
                                $('#quoteForm').trigger("reset");
                                setTimeout(function(){
                                    $("#quoteModal").modal('hide');
                                }, 3000);
                            },
                            error: function () {
                                $('#success').html("<div class='alert alert-danger'>");
                                $('#success > .alert-danger').html("<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;")
                                        .append("</button>");
                                $('#success > .alert-danger').append($("<strong>").text("Sorry " + name + ", it seems that our mail server is not responding. Please try again later!"));
                                $('#success > .alert-danger').append('</div>');
                                $('#quoteForm').trigger("reset");
                            },
                            complete: function () {
                                setTimeout(function () {
                                    $this.prop("disabled", false);
                                }, 1000);
                            }
                        });
                    },
                    filter: function () {
                        return $(this).is(":visible");
                    },
                });

                $("a[data-toggle=\"tab\"]").click(function (e) {
                    e.preventDefault();
                    $(this).tab("show");
                });
            });

            $('#name').focus(function () {
                $('#success').html('');
            });

            function removeFromCart(id) {
                if(confirm("Are you sure want to remove?")) {
                    $.ajax({
                        url: "{{ route('remove.from.cart') }}",
                        method: "DELETE",
                        data: {
                            _token: '{{ csrf_token() }}', 
                            id: id
                        },
                        success: function (response) {
                            window.location.reload();
                        }
                    });
                }
            }
        </script>
    </body>
</html>
