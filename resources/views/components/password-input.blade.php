@props(['id', 'name', 'autocomplete' => 'off'])

<div class="pw-field">
    <input type="password" id="{{ $id }}" name="{{ $name }}" autocomplete="{{ $autocomplete }}" required>
    <button type="button" class="pw-toggle" aria-label="Show password" aria-pressed="false"
            onclick="const i = document.getElementById('{{ $id }}'); const show = i.type === 'password'; i.type = show ? 'text' : 'password'; this.textContent = show ? 'Hide' : 'Show'; this.setAttribute('aria-pressed', show); this.setAttribute('aria-label', show ? 'Hide password' : 'Show password');">Show</button>
</div>

@once
    <style>
        .pw-field { position: relative; margin-bottom: 12px; }
        .pw-field input { margin-bottom: 0; padding-right: 64px; }
        .pw-toggle { position: absolute; top: 50%; right: 8px; transform: translateY(-50%); background: none; border: 0; padding: 4px 6px; font: inherit; font-size: 0.9rem; color: var(--brand, #1f5fbf); cursor: pointer; }
        .pw-toggle:hover { text-decoration: underline; }
    </style>
@endonce