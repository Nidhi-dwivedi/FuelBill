<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

$auth = new Auth();
if ($auth->isLoggedIn()) {
    if ($auth->isAdmin()) {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: user/dashboard.php');
    }
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #0061f2 0%, #10b3d6 100%);
        }
        .card-hover {
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 97, 242, 0.1);
        }
        .btn-animated {
            position: relative;
            overflow: hidden;
            z-index: 1;
        }
        .btn-animated:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 9999px;
            z-index: -2;
        }
        .btn-animated:before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.2);
            transition: all 0.3s;
            border-radius: 9999px;
            z-index: -1;
        }
        .btn-animated:hover:before {
            width: 100%;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    <div class="min-h-screen flex flex-col">
        <!-- Navigation -->
        <nav class="bg-white shadow-lg sticky top-0 z-50">
            <div class="container mx-auto px-6 py-4">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-3">
                        <div class="gradient-bg p-2 rounded-lg">
                            <i class="fas fa-gas-pump text-xl text-white"></i>
                        </div>
                        <span class="text-xl font-bold text-blue-600"><?php echo APP_NAME; ?></span>
                    </div>
                    <div class="hidden md:flex space-x-2">
                        <a href="#features" class="px-4 py-2 text-blue-600 hover:text-blue-800 font-medium transition">Features</a>
                        <a href="#" class="px-4 py-2 text-blue-600 hover:text-blue-800 font-medium transition">About</a>
                        <a href="#" class="px-4 py-2 text-blue-600 hover:text-blue-800 font-medium transition">Contact</a>
                    </div>
                    <div class="flex items-center space-x-3">
                        <a href="login.php" class="px-5 py-2 text-blue-600 hover:text-blue-800 font-medium transition">Login</a>
                        <a href="register.php" class="gradient-bg px-5 py-2 rounded-full text-white font-medium hover:shadow-lg transition btn-animated">Register</a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <div class="relative overflow-hidden">
            <div class="gradient-bg absolute top-0 right-0 w-full md:w-1/2 h-full transform skew-x-12 translate-x-1/3 z-0 opacity-20"></div>
            <div class="container mx-auto px-6 py-16 md:py-24 relative z-10">
                <div class="flex flex-col md:flex-row items-center">
                    <div class="md:w-1/2 mb-12 md:mb-0">
                        <h1 class="text-4xl md:text-5xl font-extrabold text-blue-900 leading-tight mb-6">
                            Next-Gen Petrol Pump <span class="text-blue-600">Billing System</span>
                        </h1>
                        <p class="text-lg text-gray-600 mb-8 leading-relaxed">
                            Streamline your operations with our cutting-edge solution that combines speed, security, and simplicity for modern fuel station management.
                        </p>
                        <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                            <a href="register.php" class="gradient-bg text-center px-8 py-3 rounded-full text-white font-medium hover:shadow-lg transition btn-animated">
                                Get Started Now
                            </a>
                            <a href="#features" class="flex items-center justify-center text-center border-2 border-blue-600 bg-white text-blue-600 hover:bg-blue-50 px-8 py-3 rounded-full font-medium transition">
                                <span>Explore Features</span>
                                <i class="fas fa-chevron-down ml-2 text-sm"></i>
                            </a>
                        </div>
                        <div class="mt-8 flex items-center space-x-4">
                            <div class="flex -space-x-2">
                                <img src="/project/images/u1.jpg" class="w-10 h-10 rounded-full border-2 border-white" alt="User">
                                <img src="/project/images/u2.jpg" class="w-10 h-10 rounded-full border-2 border-white" alt="User">
                                <img src="/project/images/u3.jpg" class="w-10 h-10 rounded-full border-2 border-white" alt="User">
                            </div>
                            <p class="text-sm text-gray-600">Trusted by <span class="font-bold text-blue-600">500+</span> petrol pumps nationwide</p>
                        </div>
                    </div>
                    <div class="md:w-1/2 relative">
                        <div class="absolute inset-0 gradient-bg opacity-20 rounded-xl transform rotate-3"></div>
                        <img src="/project/images/pp.jpg" alt="Petrol Pump" class="rounded-xl shadow-2xl relative z-10 transform -rotate-3 hover:rotate-0 transition duration-500">
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Section -->
        <div class="py-12 bg-white">
            <div class="container mx-auto px-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="flex flex-col items-center p-6 text-center">
                        <span class="text-4xl font-bold text-blue-600 mb-2">99.9%</span>
                        <p class="text-gray-600">Uptime Reliability</p>
                    </div>
                    <div class="flex flex-col items-center p-6 text-center">
                        <span class="text-4xl font-bold text-blue-600 mb-2">3x</span>
                        <p class="text-gray-600">Faster Transactions</p>
                    </div>
                    <div class="flex flex-col items-center p-6 text-center">
                        <span class="text-4xl font-bold text-blue-600 mb-2">24/7</span>
                        <p class="text-gray-600">Technical Support</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <section id="features" class="py-16 bg-gray-50">
            <div class="container mx-auto px-6">
                <div class="text-center max-w-2xl mx-auto mb-16">
                    <h2 class="text-3xl font-bold text-blue-900 mb-4">Powerful Features</h2>
                    <p class="text-gray-600">Everything you need to manage your petrol pump operations efficiently</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-white p-8 rounded-xl shadow-lg card-hover">
                        <div class="gradient-bg w-14 h-14 flex items-center justify-center rounded-full mb-6">
                            <i class="fas fa-bolt text-xl text-white"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-blue-900 mb-4">Lightning-Fast Transactions</h3>
                        <p class="text-gray-600 mb-4">Process fuel purchases and services in seconds with our optimized workflow system.</p>
                        <a href="#" class="text-blue-600 hover:text-blue-800 inline-flex items-center font-medium">
                            Learn more <i class="fas fa-arrow-right ml-2 text-sm"></i>
                        </a>
                    </div>
                    <div class="bg-white p-8 rounded-xl shadow-lg card-hover">
                        <div class="gradient-bg w-14 h-14 flex items-center justify-center rounded-full mb-6">
                            <i class="fas fa-receipt text-xl text-white"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-blue-900 mb-4">Smart Digital Receipts</h3>
                        <p class="text-gray-600 mb-4">Send branded digital receipts automatically via email or SMS with detailed transaction information.</p>
                        <a href="#" class="text-blue-600 hover:text-blue-800 inline-flex items-center font-medium">
                            Learn more <i class="fas fa-arrow-right ml-2 text-sm"></i>
                        </a>
                    </div>
                    <div class="bg-white p-8 rounded-xl shadow-lg card-hover">
                        <div class="gradient-bg w-14 h-14 flex items-center justify-center rounded-full mb-6">
                            <i class="fas fa-chart-line text-xl text-white"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-blue-900 mb-4">Advanced Analytics</h3>
                        <p class="text-gray-600 mb-4">Visualize sales trends, inventory levels, and customer behaviors with intuitive dashboards.</p>
                        <a href="#" class="text-blue-600 hover:text-blue-800 inline-flex items-center font-medium">
                            Learn more <i class="fas fa-arrow-right ml-2 text-sm"></i>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-16 gradient-bg">
            <div class="container mx-auto px-6 text-center">
                <h2 class="text-3xl font-bold text-white mb-6">Ready to Transform Your Petrol Pump Operations?</h2>
                <p class="text-blue-100 mb-8 max-w-2xl mx-auto">Join hundreds of satisfied station owners who have streamlined their business with our solution.</p>
                <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-4">
                    <a href="register.php" class="bg-white text-blue-600 hover:bg-blue-50 px-8 py-3 rounded-full font-medium transition">
                        Start Free Trial
                    </a>
                    <a href="#" class="border-2 border-white text-white hover:bg-blue-700 px-8 py-3 rounded-full font-medium transition">
                        Schedule Demo
                    </a>
                </div>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section class="py-16 bg-white">
            <div class="container mx-auto px-6">
                <div class="text-center max-w-2xl mx-auto mb-16">
                    <h2 class="text-3xl font-bold text-blue-900 mb-4">What Our Users Say</h2>
                    <p class="text-gray-600">Hear from petrol pump owners who've transformed their operations</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="bg-gray-50 p-8 rounded-xl shadow-sm">
                        <div class="flex items-center mb-4">
                            <div class="text-blue-600 mr-2">
                                <i class="fas fa-quote-left text-xl"></i>
                            </div>
                            <div class="text-yellow-400 flex">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                        <p class="text-gray-600 mb-6">"This billing system has completely revolutionized how we manage our petrol pump. The real-time reporting has given us insights we never had before."</p>
                        <div class="flex items-center">
                            <img src="/project/images/rk.jpg" alt="User" class="w-12 h-12 rounded-full mr-4">
                            <div>
                                <h4 class="font-semibold text-blue-900">Rajesh Kumar</h4>
                                <p class="text-sm text-gray-500">Highway Petrol Services</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 p-8 rounded-xl shadow-sm">
                        <div class="flex items-center mb-4">
                            <div class="text-blue-600 mr-2">
                                <i class="fas fa-quote-left text-xl"></i>
                            </div>
                            <div class="text-yellow-400 flex">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                        <p class="text-gray-600 mb-6">"Customer satisfaction has improved dramatically since we started using this system. The digital receipts and quick transactions have made a huge difference."</p>
                        <div class="flex items-center">
                            <img src="/project/images/ps.jpg" alt="User" class="w-12 h-12 rounded-full mr-4">
                            <div>
                                <h4 class="font-semibold text-blue-900">Priya Sharma</h4>
                                <p class="text-sm text-gray-500">City Fuel Station</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-blue-900 text-white py-12">
            <div class="container mx-auto px-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                    <div>
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="bg-white p-2 rounded-lg">
                                <i class="fas fa-gas-pump text-blue-600"></i>
                            </div>
                            <span class="text-xl font-bold"><?php echo APP_NAME; ?></span>
                        </div>
                        <p class="text-blue-200 mb-4">Modern solution for petrol pump management that saves time and increases profit.</p>
                        <div class="flex space-x-4">
                            <a href="#" class="text-blue-200 hover:text-white transition">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="text-blue-200 hover:text-white transition">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="text-blue-200 hover:text-white transition">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                        </div>
                    </div>
                    <div>
                        <h3 class="font-semibold text-lg mb-4">Quick Links</h3>
                        <ul class="space-y-2">
                            <li><a href="#" class="text-blue-200 hover:text-white transition">Home</a></li>
                            <li><a href="#features" class="text-blue-200 hover:text-white transition">Features</a></li>
                            <li><a href="#" class="text-blue-200 hover:text-white transition">Pricing</a></li>
                            <li><a href="#" class="text-blue-200 hover:text-white transition">Testimonials</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="font-semibold text-lg mb-4">Resources</h3>
                        <ul class="space-y-2">
                            <li><a href="#" class="text-blue-200 hover:text-white transition">Documentation</a></li>
                            <li><a href="#" class="text-blue-200 hover:text-white transition">Help Center</a></li>
                            <li><a href="#" class="text-blue-200 hover:text-white transition">API Reference</a></li>
                            <li><a href="#" class="text-blue-200 hover:text-white transition">Blog</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="font-semibold text-lg mb-4">Contact</h3>
                        <ul class="space-y-2">
                            <li class="flex items-start">
                                <i class="fas fa-map-marker-alt mt-1 mr-2 text-blue-300"></i>
                                <span class="text-blue-200">India</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-phone-alt mr-2 text-blue-300"></i>
                                <span class="text-blue-200">+91 1234567890</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-envelope mr-2 text-blue-300"></i>
                                <span class="text-blue-200">petrofy8@gmail.com</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="border-t border-blue-800 pt-6 text-center text-blue-300">
                    <p>&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>






