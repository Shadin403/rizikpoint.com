<?php $__env->startSection('content'); ?>
<?php
    $default_bg = uploaded_asset(get_setting('admin_login_background'));
    $nature_images = [
        'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=1200&q=80',
        'https://images.unsplash.com/photo-1447752875215-b2761acb3c5d?auto=format&fit=crop&w=1200&q=80',
        'https://images.unsplash.com/photo-1470071459604-3b5ec3a7fe05?auto=format&fit=crop&w=1200&q=80',
        'https://images.unsplash.com/photo-1509316975850-ff9c5deb0cd9?auto=format&fit=crop&w=1200&q=80',
        'https://images.unsplash.com/photo-1475924156734-496f6cac6ec1?auto=format&fit=crop&w=1200&q=80',
        'https://images.unsplash.com/photo-1505118380757-91f5f5632de0?auto=format&fit=crop&w=1200&q=80',
        'https://images.unsplash.com/photo-1483728642387-6c3bdd6c93e5?auto=format&fit=crop&w=1200&q=80'
    ];
    if ($default_bg) {
        $nature_images[] = $default_bg;
    }
    $random_bg = $nature_images[array_rand($nature_images)];
?>
<style>
/* Split Layout CSS */
.login-split-container {
    min-height: 100vh;
    display: flex;
    background: #0d0d17;
    font-family: 'Poppins', sans-serif;
}

/* Left side visual panel */
.login-visual-panel {
    flex: 1.2;
    position: relative;
    padding: 50px;
    display: none; /* hidden on small screen */
    overflow: hidden;
}

@media (min-width: 992px) {
    .login-visual-panel {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
}

.visual-bg-layer {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    transition: opacity 1.2s ease-in-out;
    opacity: 0;
    z-index: 0;
}

.visual-bg-layer.active {
    opacity: 1;
}

.login-bg-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(16, 16, 28, 0.45) 0%, rgba(10, 10, 18, 0.85) 100%);
    z-index: 1;
    pointer-events: none;
}

.login-visual-content {
    position: relative;
    z-index: 2;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.login-visual-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.btn-back-web {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 30px;
    padding: 8px 22px;
    color: #ffffff !important;
    font-size: 13px;
    font-weight: 500;
    text-decoration: none !important;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-back-web:hover {
    background: rgba(255, 255, 255, 0.15);
    border-color: rgba(255, 255, 255, 0.3);
    transform: translateX(-2px);
}

.login-tagline-wrap {
    margin-top: auto;
    padding-bottom: 20px;
}

.login-tagline {
    font-size: 2.2rem;
    font-weight: 700;
    line-height: 1.3;
    color: #ffffff;
    letter-spacing: -0.5px;
    max-width: 500px;
}

.login-carousel-dots {
    display: flex;
    gap: 8px;
    margin-top: 24px;
}

.login-dot {
    width: 28px;
    height: 3px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 2px;
    transition: background 0.3s ease;
}

.login-dot.active {
    background: #ffffff;
}

/* Right side form panel */
.login-form-panel {
    flex: 0.8;
    background: #11111f;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px 24px;
}

@media (max-width: 991px) {
    .login-form-panel {
        flex: 1;
        min-height: 100vh;
        background: radial-gradient(circle at center, #16162a 0%, #0d0d17 100%);
    }
}

.login-form-wrap {
    width: 100%;
    max-width: 400px;
}

/* Mobile logo wrapper */
.mobile-logo-wrap {
    display: block;
    margin-bottom: 30px;
    text-align: center;
}

@media (min-width: 992px) {
    .mobile-logo-wrap {
        display: none;
    }
}

/* Custom form fields */
.form-group-custom {
    position: relative;
    margin-bottom: 24px;
}

.input-custom {
    background: rgba(255, 255, 255, 0.03) !important;
    border: 1px solid rgba(255, 255, 255, 0.08) !important;
    border-radius: 12px !important;
    color: #ffffff !important;
    height: 52px !important;
    padding: 10px 16px !important;
    font-size: 14px !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
}

.input-custom::placeholder {
    color: rgba(255, 255, 255, 0.3) !important;
}

.input-custom:focus {
    background: rgba(255, 255, 255, 0.06) !important;
    border-color: var(--primary) !important;
    box-shadow: 0 0 0 3px rgba(247, 123, 11, 0.2) !important;
    outline: none !important;
}

/* Checkbox and links */
.aiz-checkbox-custom {
    display: inline-flex;
    align-items: center;
    cursor: pointer;
    user-select: none;
    margin-bottom: 0;
}

.aiz-checkbox-custom input {
    display: none;
}

.aiz-checkbox-custom span.text {
    color: rgba(255, 255, 255, 0.6) !important;
    font-size: 13px !important;
    font-weight: 500;
    margin-left: 8px;
}

.aiz-checkbox-custom span.checkmark {
    width: 18px;
    height: 18px;
    border: 1.5px solid rgba(255, 255, 255, 0.2);
    border-radius: 5px;
    display: inline-block;
    position: relative;
    transition: all 0.2s ease;
}

.aiz-checkbox-custom input:checked ~ span.checkmark {
    background: var(--primary);
    border-color: var(--primary);
}

.aiz-checkbox-custom span.checkmark::after {
    content: "";
    position: absolute;
    display: none;
    left: 5px;
    top: 2px;
    width: 5px;
    height: 9px;
    border: solid white;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
}

.aiz-checkbox-custom input:checked ~ span.checkmark::after {
    display: block;
}

.text-reset-custom {
    color: rgba(255, 255, 255, 0.5) !important;
    font-size: 13px;
    font-weight: 500;
    transition: color 0.2s ease;
}

.text-reset-custom:hover {
    color: var(--primary) !important;
    text-decoration: none;
}

.btn-login-custom {
    background: linear-gradient(135deg, var(--primary) 0%, #d45e00 100%) !important;
    border: none !important;
    border-radius: 12px !important;
    height: 52px !important;
    color: #ffffff !important;
    font-weight: 600 !important;
    font-size: 15px !important;
    box-shadow: 0 4px 20px rgba(247, 123, 11, 0.3) !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
}

.btn-login-custom:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 25px rgba(247, 123, 11, 0.45) !important;
    color: #ffffff !important;
}

.demo-table-card {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-radius: 12px;
    padding: 12px;
}
</style>

<div class="login-split-container">
    <!-- Left Visual Panel -->
    <div class="login-visual-panel">
        <div class="visual-bg-layer layer-1 active" style="background-image: url('<?php echo e($random_bg); ?>')"></div>
        <div class="visual-bg-layer layer-2"></div>
        <div class="login-bg-overlay"></div>
        
        <div class="login-visual-content">
            <div class="login-visual-header">
                <div>
                    <?php if(get_setting('system_logo_white') != null): ?>
                        <img src="<?php echo e(uploaded_asset(get_setting('system_logo_white'))); ?>" height="40" style="max-height: 40px; object-fit: contain;">
                    <?php elseif(get_setting('system_logo_black') != null): ?>
                        <img src="<?php echo e(uploaded_asset(get_setting('system_logo_black'))); ?>" height="40" style="max-height: 40px; object-fit: contain; filter: brightness(0) invert(1);">
                    <?php else: ?>
                        <img src="<?php echo e(static_asset('assets/img/logo.png')); ?>" height="40" style="max-height: 40px; object-fit: contain; filter: brightness(0) invert(1);">
                    <?php endif; ?>
                </div>
                <a href="<?php echo e(route('home')); ?>" class="btn-back-web">
                    <span>Back to website</span>
                    <i class="las la-arrow-right"></i>
                </a>
            </div>
            
            <div class="login-tagline-wrap">
                <h2 class="login-tagline">
                    Capturing Moments,<br>Creating Memories
                </h2>
                <div class="login-carousel-dots">
                    <?php $__currentLoopData = $nature_images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span class="login-dot <?php echo e($random_bg === $img ? 'active' : ''); ?>" data-index="<?php echo e($index); ?>"></span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Right Form Panel -->
    <div class="login-form-panel">
        <div class="login-form-wrap">
            <!-- Mobile Logo -->
            <div class="mobile-logo-wrap">
                <?php if(get_setting('system_logo_white') != null): ?>
                    <img src="<?php echo e(uploaded_asset(get_setting('system_logo_white'))); ?>" class="mx-auto mb-4" height="40" style="max-height: 40px; object-fit: contain;">
                <?php elseif(get_setting('system_logo_black') != null): ?>
                    <img src="<?php echo e(uploaded_asset(get_setting('system_logo_black'))); ?>" class="mx-auto mb-4" height="40" style="max-height: 40px; object-fit: contain; filter: brightness(0) invert(1);">
                <?php else: ?>
                    <img src="<?php echo e(static_asset('assets/img/logo.png')); ?>" class="mx-auto mb-4" height="40" style="max-height: 40px; object-fit: contain; filter: brightness(0) invert(1);">
                <?php endif; ?>
            </div>

            <div class="mb-4 text-center text-lg-left">
                <h1 class="h3 text-white font-weight-bold mb-2"><?php echo e(translate('Login to account')); ?></h1>
                <p class="text-muted mb-0" style="font-size: 13.5px; opacity: 0.8;"><?php echo e(translate('Welcome back! Please enter your details.')); ?></p>
            </div>
            
            <form class="pad-hor" method="POST" role="form" action="<?php echo e(route('login')); ?>">
                <?php echo csrf_field(); ?>
                <div class="form-group form-group-custom">
                    <input id="email" type="email" class="form-control input-custom <?php echo e($errors->has('email') ? ' is-invalid' : ''); ?>" name="email" value="<?php echo e(old('email')); ?>" required autofocus placeholder="<?php echo e(translate('Email')); ?>">
                    <?php if($errors->has('email')): ?>
                        <span class="invalid-feedback" role="alert" style="color: #ff5c5c !important;">
                            <strong><?php echo e($errors->first('email')); ?></strong>
                        </span>
                    <?php endif; ?>
                </div>
                <div class="form-group form-group-custom">
                    <input id="password" type="password" class="form-control input-custom <?php echo e($errors->has('password') ? ' is-invalid' : ''); ?>" name="password" required placeholder="<?php echo e(translate('Password')); ?>">
                    <?php if($errors->has('password')): ?>
                        <span class="invalid-feedback" role="alert" style="color: #ff5c5c !important;">
                            <strong><?php echo e($errors->first('password')); ?></strong>
                        </span>
                    <?php endif; ?>
                </div>
                <div class="row align-items-center mb-4">
                    <div class="col-6">
                        <label class="aiz-checkbox-custom">
                            <input type="checkbox" name="remember" id="remember" <?php echo e(old('remember') ? 'checked' : ''); ?>>
                            <span class="checkmark"></span>
                            <span class="text"><?php echo e(translate('Remember Me')); ?></span>
                        </label>
                    </div>
                    <?php if(env('MAIL_USERNAME') != null && env('MAIL_PASSWORD') != null): ?>
                        <div class="col-6 text-right">
                            <a href="<?php echo e(route('password.request')); ?>" class="text-reset-custom"><?php echo e(translate('Forgot password ?')); ?></a>
                        </div>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-login-custom">
                    <?php echo e(translate('Login')); ?>

                </button>
            </form>
            
            <?php if(env("DEMO_MODE") == "On"): ?>
                <div class="mt-4 demo-table-card">
                    <table class="table table-sm table-borderless mb-0 text-white" style="font-size: 12px;">
                        <tbody>
                            <tr class="align-middle">
                                <td class="pl-0 opacity-80">admin@example.com</td>
                                <td class="opacity-80">123456</td>
                                <td class="pr-0 text-right">
                                    <button class="btn btn-soft-primary btn-xs font-weight-bold" style="border-radius: 6px;" onclick="autoFill()"><?php echo e(translate('Copy')); ?></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script type="text/javascript">
        function autoFill(){
            $('#email').val('admin@example.com');
            $('#password').val('123456');
        }

        document.addEventListener('DOMContentLoaded', function() {
            const bgImages = <?php echo json_encode($nature_images, 15, 512) ?>;
            const dots = document.querySelectorAll('.login-dot');
            const layer1 = document.querySelector('.visual-bg-layer.layer-1');
            const layer2 = document.querySelector('.visual-bg-layer.layer-2');
            let activeLayer = 1;
            let currentIndex = bgImages.indexOf('<?php echo e($random_bg); ?>');
            if (currentIndex === -1) currentIndex = 0;

            function changeBackground(index) {
                const nextImg = bgImages[index];
                if (nextImg) {
                    if (activeLayer === 1) {
                        layer2.style.backgroundImage = `url('${nextImg}')`;
                        layer2.classList.add('active');
                        layer1.classList.remove('active');
                        activeLayer = 2;
                    } else {
                        layer1.style.backgroundImage = `url('${nextImg}')`;
                        layer1.classList.add('active');
                        layer2.classList.remove('active');
                        activeLayer = 1;
                    }
                    
                    dots.forEach((dot, idx) => {
                        if (idx === index) {
                            dot.classList.add('active');
                        } else {
                            dot.classList.remove('active');
                        }
                    });
                }
            }

            // Cycle background every 5 seconds
            setInterval(function() {
                currentIndex = (currentIndex + 1) % bgImages.length;
                changeBackground(currentIndex);
            }, 5000);
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backend.layouts.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\kachermart\backend\resources\views/auth/login.blade.php ENDPATH**/ ?>