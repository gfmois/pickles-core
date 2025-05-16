<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
<form method="post" class="max-w-md mx-auto bg-white p-8 rounded shadow">
    <div class="mb-4">
        <label for="email" class="block text-gray-700 font-bold mb-2">Email</label>
        <input value="<?= old('email') ?>" type="email" id="email" name="email"  class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
        <div class="text-red-500"><?= error('email') ?></div>
    </div>

    <div class="mb-6">
        <label for="password" class="block text-gray-700 font-bold mb-2">Password</label>
        <input type="password" value="<?= old('password') ?>" type="password" id="password" name="password" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
        <div class="text-red-500"><?= error('password') ?></div>
    </div>

    <button type="submit" class="w-full bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-600 transition">Submit</button>
</form>