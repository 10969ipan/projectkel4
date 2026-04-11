<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StockFlow | Warehouse Inventory System</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- AOS CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Custom Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                fontFamily: {
                    sans: ['Inter', 'sans-serif'],
                },
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                        secondary: {
                            50: '#f8fafc',
                            100: '#f1f5f9',
                            200: '#e2e8f0',
                            300: '#cbd5e1',
                            400: '#94a3b8',
                            500: '#64748b',
                            600: '#475569',
                            700: '#334155',
                            800: '#1e293b',
                            900: '#0f172a',
                        },
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'float-reverse': 'float-reverse 5s ease-in-out infinite',
                        'pulse-slow': 'pulse 6s infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': {
                                transform: 'translateY(0)'
                            },
                            '50%': {
                                transform: 'translateY(-20px)'
                            },
                        },
                        'float-reverse': {
                            '0%, 100%': {
                                transform: 'translateY(0)'
                            },
                            '50%': {
                                transform: 'translateY(20px)'
                            },
                        },
                    }
                }
            }
        }
    </script>
    <style>
        html,
        body {
            height: 100%;
            overflow: hidden;
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
        }

        body {
            background: linear-gradient(135deg, rgba(14, 165, 233, 0.05) 0%, rgba(2, 132, 199, 0.05) 100%);
            position: relative;
        }

        .hero-text {
            background-clip: text;
            -webkit-background-clip: text;
            color: transparent;
            background-image: linear-gradient(45deg, #0ea5e9, #0284c7);
        }

        .center-absolute {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 90%;
            max-width: 800px;
            text-align: center;
        }

        .nav-container {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            padding: 1.5rem;
            z-index: 50;
        }

        .bg-bubbles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            overflow: hidden;
        }

        .bg-bubble {
            position: absolute;
            border-radius: 50%;
            opacity: 0.1;
            filter: blur(40px);
        }

        /* Custom animation for arrow */
        @keyframes pulseArrow {

            0%,
            100% {
                transform: translateX(0);
            }

            50% {
                transform: translateX(5px);
            }
        }

        .pulse-arrow {
            animation: pulseArrow 1.5s infinite;
        }
    </style>
</head>

<body class="font-sans antialiased">
    <!-- Animated Background Bubbles -->
    <div class="bg-bubbles">
        <div class="bg-bubble animate-float"
            style="width: 200px; height: 200px; background: #0ea5e9; top: 20%; left: 10%;"></div>
        <div class="bg-bubble animate-float-reverse"
            style="width: 300px; height: 300px; background: #0284c7; bottom: 15%; right: 10%;"></div>
        <div class="bg-bubble animate-float"
            style="width: 250px; height: 250px; background: #0369a1; top: 60%; left: 70%;"></div>
    </div>

    <!-- Navigation -->
    <nav class="nav-container max-w-5xl mx-auto flex justify-between items-center" data-aos="fade-down">
        <div class="flex items-center space-x-3">
            <i class="fas fa-boxes text-2xl text-primary-600"></i>
            <span class="text-xl font-bold text-primary-800">StockFlow</span>
            <span class="text-sm text-secondary-500 hidden sm:inline">Warehouse Inventory System</span>
        </div>
        <div class="flex space-x-4">
            <a href="/login"
                class="px-4 py-2 text-sm font-medium text-primary-600 hover:text-primary-800 transition-colors hover:scale-105">Login</a>
            <a href="/register"
                class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-all hover:scale-105 shadow-md hover:shadow-lg">Register</a>
        </div>
    </nav>

    <!-- Main Content - Centered Absolutely -->
    <div class="center-absolute">
        <!-- Title -->
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-secondary-800 mb-6" data-aos="fade-up"
            data-aos-delay="100">
            Modern <span class="hero-text" data-aos="fade-up" data-aos-delay="200">Warehouse</span> <span
                data-aos="fade-up" data-aos-delay="300">Inventory</span>
        </h1>

        <!-- Description -->
        <p class="text-lg md:text-xl text-secondary-600 mb-10 mx-auto max-w-lg" data-aos="fade-up" data-aos-delay="400">
            Streamline your warehouse operations with our powerful inventory management system. Real-time tracking,
            automated reports, and intuitive controls.
        </p>

        <!-- Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="/login"
                class="px-8 py-3 text-base font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-all hover:scale-105 shadow-lg hover:shadow-xl flex items-center justify-center"
                data-aos="fade-up" data-aos-delay="500">
                Get Started <i class="fas fa-arrow-right ml-2 pulse-arrow"></i>
            </a>
            <a href="/register"
                class="px-8 py-3 text-base font-medium text-primary-600 bg-white border border-primary-200 rounded-lg hover:bg-primary-50 transition-all hover:scale-105 shadow-lg hover:shadow-xl"
                data-aos="fade-up" data-aos-delay="600">
                Request Demo
            </a>
        </div>
    </div>

    <!-- AOS JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-out-quart',
            once: true,
            offset: 10
        });

        // Add dynamic bubbles
        document.addEventListener('DOMContentLoaded', () => {
            const bubblesContainer = document.querySelector('.bg-bubbles');
            for (let i = 0; i < 5; i++) {
                const bubble = document.createElement('div');
                bubble.className = 'bg-bubble';
                bubble.style.width = `${Math.random() * 200 + 100}px`;
                bubble.style.height = bubble.style.width;
                bubble.style.background = i % 2 === 0 ? '#0ea5e9' : '#0369a1';
                bubble.style.top = `${Math.random() * 100}%`;
                bubble.style.left = `${Math.random() * 100}%`;
                bubble.style.animation =
                    `float ${Math.random() * 5 + 5}s ease-in-out infinite ${Math.random() * 2}s`;
                bubblesContainer.appendChild(bubble);
            }
        });
    </script>
</body>

</html>
