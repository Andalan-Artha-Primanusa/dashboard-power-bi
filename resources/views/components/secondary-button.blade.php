<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center rounded-xl border border-[#BD9B75] bg-white px-4 py-2.5 text-xs font-bold uppercase tracking-wide text-[#BD9B75] transition hover:bg-white focus:outline-none focus:ring-2 focus:ring-[#BD9B75] focus:ring-offset-2 disabled:opacity-40']) }}>
 {{ $slot }}
</button>
