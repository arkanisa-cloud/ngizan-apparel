<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center px-6 py-3 bg-soft-cloud border border-hairline rounded-full font-medium text-xs text-ink uppercase tracking-wider hover:bg-neutral-200 focus:outline-none transition ease-in-out duration-150 cursor-pointer disabled:opacity-40']) }}>
    {{ $slot }}
</button>
