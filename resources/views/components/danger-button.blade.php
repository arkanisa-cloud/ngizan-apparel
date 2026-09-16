<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-6 py-3 bg-sale border border-transparent rounded-full font-medium text-xs text-white uppercase tracking-wider hover:opacity-85 active:scale-95 focus:outline-none transition ease-in-out duration-150 cursor-pointer']) }}>
    {{ $slot }}
</button>
