<html lang="en">
 <head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1" name="viewport"/>
  <title>
   Sign In
  </title>
  <script src="https://cdn.tailwindcss.com">
  </script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&amp;display=swap" rel="stylesheet"/>
  <style>
   body {
      font-family: 'Inter', sans-serif;
      margin: 0;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background-color: #e6e8e5;
    }
    .background-image {
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      z-index: -1;
      object-fit: cover;
      object-position: left center;
      filter: brightness(0.95);
    }
  </style>
 </head>
 <body>
  <img alt="Light blue modern chair next to a small gold table with green plants and a white clock, under a white hanging lamp in a bright room with light wood floor" class="background-image" height="1000" src="https://storage.googleapis.com/a1aa/image/20844bec-e090-4bae-6975-1e2187682df4.jpg" width="1000"/>
  <div class="bg-white bg-opacity-90 rounded-3xl p-8 md:p-12 w-full max-w-md shadow-sm" style="backdrop-filter: saturate(180%) blur(20px);">
   <div class="flex justify-between items-start mb-4">
    <div class="text-[13px] leading-5 text-black">
     Welcome to
     <span class="font-semibold text-[#6b8a2e]">
     my channel
     </span>
    </div>
   
   </div>
   <h1 class="text-3xl font-semibold leading-tight mb-6 text-black">
    Sign in
   </h1>
   <?php if (isset($_GET['error'])): ?>
  <div class="mb-4">
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded relative flex items-center gap-2" role="alert">
      <i class="fas fa-exclamation-circle"></i>
      <span><?= htmlspecialchars($_GET['error']) ?></span>
    </div>
  </div>
<?php endif; ?>
   <form method="POST" action="/app/controller/AuthController.php">
   <div class="mb-4">
     <label class="block text-[11px] font-normal text-black mb-1 select-none" for="username">
      Enter your id 
     </label>
     <input class="w-full border border-[#a3b07a] rounded-md px-3 py-2 text-[12px] placeholder:text-[#a3b07a] focus:outline-none focus:ring-1 focus:ring-[#6b8a2e] focus:border-[#6b8a2e]" name="ma_nhan_vien" placeholder="your id" type="text"/>
    </div>
    <div class="mb-4">
     <label class="block text-[11px] font-normal text-black mb-1 select-none" for="username">
      Enter your username 
     </label>
     <input class="w-full border border-[#a3b07a] rounded-md px-3 py-2 text-[12px] placeholder:text-[#a3b07a] focus:outline-none focus:ring-1 focus:ring-[#6b8a2e] focus:border-[#6b8a2e]" name="ten_dang_nhap" placeholder="Username " type="text"/>
    </div>
    <div class="mb-6">
     <label class="block text-[11px] font-normal text-black mb-1 select-none" for="password">
      Enter your Password
     </label>
     <input class="w-full border border-[#d9d9d9] rounded-md px-3 py-2 text-[12px] placeholder:text-[#d9d9d9] focus:outline-none focus:ring-1 focus:ring-[#6b8a2e] focus:border-[#6b8a2e]" name="mat_khau" placeholder="Password" type="password"/>
    
    </div>
    <button class="w-full bg-[#6b8a2e] text-white text-[13px] font-semibold rounded-md py-2 hover:bg-[#5a7525] transition" type="submit">
     Sign in
    </button>
   </form>
   <div class="sr-only">Your Logo</div>
  </div>
 </body>
</html>