@extends('layouts.admin')

@section('content')
<div class="admin-page-header">
    <div>
        <h1 class="admin-page-title">Commande hors ligne</h1>
        <p class="admin-page-subtitle">Enregistrer une commande effectuée hors ligne.</p>
    </div>
    <div class="admin-page-actions">
        <a href="{{ route('admin.orders.index') }}" class="btn-admin secondary">
            <i class="fa-solid fa-arrow-left"></i> Retour aux commandes
        </a>
    </div>
</div>

@if(session('error'))
    <div class="card" style="margin-bottom: 1rem; background: #fef2f2; border-color: #fecaca;">
        <p style="margin: 0; color: #dc2626;">{{ session('error') }}</p>
    </div>
@endif

    <form method="POST" action="{{ route('admin.orders.store') }}" class="card admin-form admin-order-offline" id="order-offline-form">
        @csrf
        <div class="form-section">
            <h2 class="section-title" style="display:flex;align-items:center;gap:6px;margin-bottom:10px;font-size:16px;">
                <span style="display:inline-flex;width:22px;height:22px;border-radius:999px;background:#ecfdf3;color:#16a34a;align-items:center;justify-content:center;font-size:13px;">1</span>
                <span>Client hors ligne</span>
            </h2>
            <p class="form-hint">Enregistrez ici un client qui n'a pas (ou pas encore) de compte en ligne.</p>
            <div class="form-row" style="gap: 16px;">
                <div class="form-group" style="flex: 1; min-width: 180px;">
                    <label for="client_name">Nom du client *</label>
                    <input type="text" name="client_name" id="client_name" value="{{ old('client_name') }}" placeholder="Nom et prénom">
                    @error('client_name')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group" style="flex: 1; min-width: 160px;">
                    <label for="client_phone">Téléphone *</label>
                    <input type="text" name="client_phone" id="client_phone" value="{{ old('client_phone') }}" placeholder="ex. 01 64 00 00 00">
                    @error('client_phone')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="form-group" style="max-width: 320px;">
                <label for="client_email">Email (optionnel)</label>
                <input type="email" name="client_email" id="client_email" value="{{ old('client_email') }}" placeholder="email@exemple.com">
                @error('client_email')<span class="form-error">{{ $message }}</span>@enderror
            </div>
        </div>

        @php
            $marinade = $products->firstWhere('name', 'Parasel-Bio Marinade');
            $marinadeId = $marinade ? (int) $marinade->id : 0;
            $marinadeVariants = $marinade && is_array($marinade->variants ?? null) ? $marinade->variants : [];
        @endphp
        <div class="form-section" style="margin-top:24px;">
            <h2 class="section-title" style="display:flex;align-items:center;gap:6px;margin-bottom:10px;font-size:16px;">
                <span style="display:inline-flex;width:22px;height:22px;border-radius:999px;background:#eff6ff;color:#2563eb;align-items:center;justify-content:center;font-size:13px;">2</span>
                <span>Produits et quantités</span>
            </h2>
            <p class="form-hint">Ajoutez les produits vendus. Le stock global sera diminué à l'enregistrement. Pour Parasel-Bio Marinade, choisissez la variante (poids).</p>
            <div id="order-items">
                <div class="order-item-row flex gap align-center flex-wrap" style="margin-bottom: 12px;">
                    <select name="items[0][product_id]" required class="item-product" data-row="0">
                        <option value="">Produit</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" data-price="{{ $p->price }}" {{ old('items.0.product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                    <div class="item-variant-wrap" data-row="0" style="display: {{ old('items.0.product_id') == $marinadeId ? 'block' : 'none' }};">
                        <select name="items[0][variant_size]" class="item-variant" title="Variante (obligatoire pour Marinade)">
                            <option value="">Variante</option>
                            @foreach($marinadeVariants as $v)
                                <option value="{{ $v['size'] ?? '' }}" data-price="{{ $v['price'] ?? 0 }}" {{ old('items.0.variant_size') === ($v['size'] ?? '') ? 'selected' : '' }}>{{ $v['size'] ?? '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <input type="number" name="items[0][quantity]" min="1" value="{{ old('items.0.quantity', 1) }}" placeholder="Qté" style="width: 80px;" required>
                    <button type="button" class="btn-admin secondary btn-remove-row" title="Supprimer la ligne"><i class="fa-solid fa-minus"></i></button>
                </div>
            </div>
            <div class="flex" style="justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;margin-top:8px;">
                <button type="button" id="add-item-row" class="btn-admin secondary">
                    <i class="fa-solid fa-plus"></i> Ajouter un produit
                </button>
                <div id="order-summary" style="font-size:14px;color:#111827;margin-left:auto;">
                    Total : <strong id="order-total-preview">0 FCFA</strong>
                </div>
            </div>
            @error('items')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-admin success" data-loading-text="Enregistrement..."><i class="fa-solid fa-check"></i> Enregistrer la commande</button>
            <a href="{{ route('admin.orders.index') }}" class="btn-admin secondary">Annuler</a>
        </div>
    </form>

        @php
            $productOptionsJson = $products->map(function ($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'price' => (float) $p->price,
                ];
            })->values();
        $marinadeForJs = $marinade ? ['id' => $marinade->id, 'variants' => array_map(function ($v) {
            return ['size' => $v['size'] ?? '', 'price' => (float) ($v['price'] ?? 0)];
        }, $marinadeVariants)] : null;
    @endphp
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('order-items');
        const addBtn = document.getElementById('add-item-row');
        const productOptions = @json($productOptionsJson);
        const marinade = @json($marinadeForJs);
        const marinadeId = marinade ? marinade.id : 0;
        const totalEl = document.getElementById('order-total-preview');

        function toggleVariant(rowEl) {
            var sel = rowEl.querySelector('.item-product');
            var wrap = rowEl.querySelector('.item-variant-wrap');
            var variantSel = rowEl.querySelector('.item-variant');
            if (!wrap || !variantSel) return;
            if (parseInt(sel.value, 10) === marinadeId) {
                wrap.style.display = 'block';
                variantSel.required = true;
            } else {
                wrap.style.display = 'none';
                variantSel.required = false;
                variantSel.value = '';
            }
        }

        function addRow(index) {
            var row = document.createElement('div');
            row.className = 'order-item-row flex gap align-center flex-wrap';
            row.style.marginBottom = '12px';
            var opts = '<option value="">Produit</option>';
            productOptions.forEach(function(p) {
                opts += '<option value="' + p.id + '" data-price="' + p.price + '">' + p.name + '</option>';
            });
            var variantOpts = '<option value="">Variante</option>';
            if (marinade && marinade.variants) {
                marinade.variants.forEach(function(v) {
                    if (v.size) variantOpts += '<option value="' + v.size + '" data-price="' + v.price + '">' + v.size + '</option>';
                });
            }
            row.innerHTML = '<select name="items[' + index + '][product_id]" required class="item-product" data-row="' + index + '">' + opts + '</select>' +
                '<div class="item-variant-wrap" data-row="' + index + '" style="display:none;"><select name="items[' + index + '][variant_size]" class="item-variant" title="Variante (obligatoire pour Marinade)">' + variantOpts + '</select></div>' +
                '<input type="number" name="items[' + index + '][quantity]" min="1" value="1" placeholder="Qté" style="width: 80px;" required>' +
                '<button type="button" class="btn-admin secondary btn-remove-row" title="Supprimer la ligne"><i class="fa-solid fa-minus"></i></button>';
            container.appendChild(row);
            row.querySelector('.item-product').addEventListener('change', function() {
                toggleVariant(row);
                updateTotal();
            });
            var qtyInput = row.querySelector('input[type="number"]');
            qtyInput.addEventListener('input', updateTotal);
            var vs = row.querySelector('.item-variant');
            if (vs) {
                vs.addEventListener('change', updateTotal);
            }
            toggleVariant(row);
            row.querySelector('.btn-remove-row').addEventListener('click', function() {
                row.remove();
                reindexRows();
                updateTotal();
            });
        }

        function reindexRows() {
            var rows = container.querySelectorAll('.order-item-row');
            rows.forEach(function(r, i) {
                r.querySelector('.item-product').setAttribute('name', 'items[' + i + '][product_id]');
                var wrap = r.querySelector('.item-variant-wrap');
                if (wrap) {
                    var vs = wrap.querySelector('.item-variant');
                    if (vs) vs.setAttribute('name', 'items[' + i + '][variant_size]');
                }
                r.querySelector('input[type="number"]').setAttribute('name', 'items[' + i + '][quantity]');
                r.classList.remove('is-single');
            });
            if (rows.length === 1) {
                rows[0].classList.add('is-single');
            }
        }

        function getPriceForRow(rowEl) {
            var productSelect = rowEl.querySelector('.item-product');
            var variantSelect = rowEl.querySelector('.item-variant');
            var productId = parseInt(productSelect.value || '0', 10);
            var basePrice = 0;
            productOptions.forEach(function(p) {
                if (p.id === productId) basePrice = p.price;
            });
            if (marinade && productId === marinadeId && variantSelect && variantSelect.value) {
                var vsPrice = 0;
                marinade.variants.forEach(function(v) {
                    if (v.size === variantSelect.value) vsPrice = v.price;
                });
                if (vsPrice > 0) return vsPrice;
            }
            return basePrice;
        }

        function updateTotal() {
            if (!totalEl) return;
            var rows = container.querySelectorAll('.order-item-row');
            var total = 0;
            rows.forEach(function(r) {
                var qtyInput = r.querySelector('input[type="number"]');
                var productSelect = r.querySelector('.item-product');
                if (!qtyInput || !productSelect || !productSelect.value) return;
                var qty = parseInt(qtyInput.value || '0', 10);
                if (qty <= 0) return;
                var price = getPriceForRow(r);
                total += price * qty;
            });
            var formatted = new Intl.NumberFormat('fr-FR', { maximumFractionDigits: 0 }).format(Math.round(total));
            totalEl.textContent = formatted + ' FCFA';
        }

        container.querySelectorAll('.order-item-row').forEach(function(row) {
            var sel = row.querySelector('.item-product');
            var qtyInput = row.querySelector('input[type="number"]');
            var vs = row.querySelector('.item-variant');
            if (sel) {
                sel.addEventListener('change', function() {
                    toggleVariant(row);
                    updateTotal();
                });
            }
            if (qtyInput) {
                qtyInput.addEventListener('input', updateTotal);
            }
            if (vs) {
                vs.addEventListener('change', updateTotal);
            }
            toggleVariant(row);
        });
        reindexRows();

        addBtn.addEventListener('click', function() {
            addRow(container.querySelectorAll('.order-item-row').length);
            updateTotal();
        });

        container.addEventListener('click', function(e) {
            if (e.target.closest('.btn-remove-row')) {
                const row = e.target.closest('.order-item-row');
                if (container.querySelectorAll('.order-item-row').length > 1) {
                    row.remove();
                    reindexRows();
                    updateTotal();
                }
            }
        });

        document.querySelectorAll('.client-type-radio').forEach(function(radio) {
            radio.addEventListener('change', function() {
                var checked = document.querySelector('input[name="client_type"]:checked');
                var isExisting = checked && checked.value === 'existing';
                document.getElementById('client-existing-wrap').style.display = isExisting ? 'block' : 'none';
                document.getElementById('client-offline-wrap').style.display = isExisting ? 'none' : 'block';
                document.getElementById('user_id').required = isExisting;
                document.getElementById('client_name').required = !isExisting;
                document.getElementById('client_phone').required = !isExisting;

                // Mettre à jour le style actif des cartes d'option client
                document.querySelectorAll('.client-type-option').forEach(function(opt) {
                    opt.classList.remove('is-active');
                    var input = opt.querySelector('.client-type-radio');
                    if (checked && input && input.value === checked.value) {
                        opt.classList.add('is-active');
                    }
                });
            });
        });
        document.querySelector('input[name="client_type"]:checked').dispatchEvent(new Event('change'));
        updateTotal();
    });
    </script>
@endsection
